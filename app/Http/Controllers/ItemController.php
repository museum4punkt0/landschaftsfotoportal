<?php

namespace App\Http\Controllers;

use App\Item;
use App\Detail;
use App\ColumnMapping;
use App\Selectlist;
use App\Element;
use App\Utils\Localization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Redirect;

class ItemController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified')->only('own');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        // Check for redirects
        $target = $item->getDetailWhereDataType('_redirect_');
        if ($target && $target != __('items.no_detail_with_data_type')) {
            return Redirect::to($target);
        }
        
        // All items for the show blade, used for image galleries
        $items = Item::find($item->item_id)
            ->descendantsAndSelf()
            ->where('public', 1)
            ->orderBy('title')
            ->get();
        
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->where('public', 1)->orderBy('item_id')->get();
        
        // Get the menu path of the requested item
        $ancestors = Item::find($item->item_id)->ancestorsAndSelf()->orderBy('depth', 'asc')->first();
        $path = array_reverse(explode('.', $ancestors->path));
        
        // Details of selected item
        $details = Detail::where('item_fk', $item->item_id)->get();
        
        // Only columns associated with this item's taxon or its descendants
        $taxon_id = $item->taxon_fk;
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                    });
            })
            ->orderBy('column_order')->get();
        
        $lists = null;
        // Load all list elements of lists used by this item's columns
        foreach ($colmap as $cm) {
            $list_id = $cm->column->list_fk;
            if ($list_id) {
                $constraint = function (Builder $query) use ($list_id) {
                    $query->where('parent_fk', null)->where('list_fk', $list_id);
                };
                $lists[$list_id] = Element::treeOf($constraint)->depthFirst()->get();
            }
        }
        
        // Get current UI language
        $lang = app()->getLocale();
        
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        
        return view('item.show', compact('item', 'items', 'details', 'menu_root', 'path', 'colmap', 'lists', 'translations'));
    }

    /**
     * Display items owned by the current authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function own()
    {
        // Get the item_type for '_image_' items
        // TODO: this should be more flexible; allow configuration of multiple/different item_types
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_type = Element::where('list_fk', $it_list->list_id)
            ->whereHas('values', function (Builder $query) {
                $query->where('value', '_image_');
            })
            ->first()->element_id;
        
        $items = Item::myOwn(Auth::user()->id)->with('details')
            ->where('item_type_fk', $item_type)->latest()->paginate(12);
        
        return view('item.own', compact('items', 'item_type'));
    }

    /**
     * Forces the user's browser to download the image belonging to this item.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function download(Item $item)
    {
        $filename = $item->details->firstWhere('column_fk', 13)->value_string;
        $pathToFile = 'public/'. config('media.full_dir') . $filename;
        
        if (Storage::missing($pathToFile)) {
            abort(404);
        }
        
        return Storage::download($pathToFile);
    }

    /**
     * Display the image gallery containing latest + random items.
     *
     * @return \Illuminate\Http\Response
     */
    public function gallery()
    {
        // Get the item_type for '_image_' items
        // TODO: this should be more flexible; allow configuration of multiple/different item_types
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_type = Element::where('list_fk', $it_list->list_id)
            ->whereHas('values', function (Builder $query) {
                $query->where('value', '_image_');
            })
            ->first()->element_id;
        
        $items['latest'] = Item::with('details')
            ->where('public', 1)->where('item_type_fk', $item_type)->latest()->take(3)->get();
        $items['random'] = Item::with('details')
            ->where('public', 1)->where('item_type_fk', $item_type)->inRandomOrder()->take(3)->get();
        $items['incomplete'] = Item::with('details')
            ->where('public', 1)
            ->where('item_type_fk', $item_type)
            ->whereHas('details', function (Builder $query) {
                // Details with missing location/city value
                $query->where('column_fk', 22)
                    ->where('value_string', '');
            })
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        return view('item.gallery', compact('items'));
    }

    /**
     * Display the timeline for all items.
     *
     * @return \Illuminate\Http\Response
     */
    public function timeline()
    {
        // TODO: remove hard-coded daterange column
        $daterange_column = 27;
        
        // Get bounds for daterange
        $bounds = Detail::selectRaw("
                EXTRACT(DECADE FROM MIN(LOWER(value_daterange)))*10 AS lower,
                EXTRACT(DECADE FROM MAX(UPPER(value_daterange)))*10 AS upper
            ")
            ->whereHas('item', function (Builder $query) {
                $query->where('public', 1);
            })
            ->where('column_fk', $daterange_column) 
            ->first();
        
        // For each decade...
        for ($decade = $bounds->lower; $decade <= $bounds->upper; $decade += 10) {
            $daterange = '['. date('Y-m-d', mktime(0, 0, 0, 1, 1, $decade)) .','.
                date('Y-m-d', mktime(0, 0, 0, 1, 1, $decade + 10)) .')';
            
            // Get number of items per decade
            $decades[$decade] = Detail::
                whereHas('item', function (Builder $query) {
                    $query->where('public', 1);
                })
                ->where('column_fk', $daterange_column)
                ->whereRaw("value_daterange && '$daterange'")
                ->count();
            
            // Get some random items per decade, to be shown as examples
            $details[$decade] = Detail::with('item')
                ->whereHas('item', function (Builder $query) {
                    $query->where('public', 1);
                })
                ->where('column_fk', $daterange_column)
                ->whereRaw("value_daterange && '$daterange'")
                ->inRandomOrder()
                ->take(5)
                ->get();
        }
        
        return view('item.timeline', compact('decades', 'details'));
    }

    /**
     * Display a map showing all items.
     *
     * @return \Illuminate\Http\Response
     */
    public function map()
    {
        return view('item.map');
    }
}
