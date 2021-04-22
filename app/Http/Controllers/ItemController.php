<?php

namespace App\Http\Controllers;

use App\Item;
use App\Detail;
use App\ColumnMapping;
use App\Selectlist;
use App\Element;
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
        
        // Translations for titles of columns and column groups
        $l10n_list = Selectlist::where('name', '_translation_')->first();
        $translations = Element::where('list_fk', $l10n_list->list_id)->get();
                
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
        
        $items = Item::myOwn(Auth::user()->id)->with('details')->orderBy('created_at')->paginate(12);
        
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
        
        if (Storage::missing($pathToFile))
            abort(404);
        
        return Storage::download($pathToFile);
    }

    /**
     * Display the image gallery containing latest + random items.
     *
     * @return \Illuminate\Http\Response
     */
    public function gallery()
    {
        $items['latest'] = Item::with('details')->where('public', 1)->orderBy('created_at')->take(3)->get();
        $items['random'] = Item::with('details')->where('public', 1)->inRandomOrder()->take(3)->get();
        
        return view('item.gallery', compact('items'));
    }

    /**
     * Display the timeline for all items.
     *
     * @return \Illuminate\Http\Response
     */
    public function timeline()
    {
        // Get number of items per decade
        $decades = Detail::select("value_int AS decade", DB::raw("COUNT(*) AS images_count"))
                        ->whereHas('item', function (Builder $query) {
                            $query->where('public', 1);
                        })
                        ->where('column_fk', 18) // TODO: introduce a data type for decades?
                        ->groupBy('value_int')
                        ->orderBy('value_int', 'desc')
                        ->get();
        
        // Get some random items per decade, to be shown as examples
        foreach ($decades as $decade) {
            $details[$decade->decade] = Detail::with('item')
                        ->whereHas('item', function (Builder $query) {
                            $query->where('public', 1);
                        })
                        ->where('column_fk', 18) // TODO: introduce a data type for decades?
                        ->where('value_int', $decade->decade)
                        ->inRandomOrder()
                        ->take(3)
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
