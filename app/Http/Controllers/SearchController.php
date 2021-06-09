<?php

namespace App\Http\Controllers;

use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\Selectlist;
use App\Value;
use App\Item;
use App\Taxon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Show search form and results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->where('public', 1)->orderBy('item_id')->get();
        
        // Get the item_type for '_image_' items
        // TODO: this should be more flexible; allow configuration of multiple/different item_types
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $it_element = Element::where('list_fk', $it_list->list_id)
            ->whereHas('values', function (Builder $query) {
                $query->where('value', '_image_');
            })->first();
        if ($it_element) {
            $item_type = $it_element->element_id;
        } else {
            $item_type = 0;
        }
        
        $colmap = ColumnMapping::where('item_type_fk', $item_type)->orderBy('column_order')->get();
        
        // Load all list elements of lists used by this item_type's columns
        $lists = null;
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
        $lang = 'name_'. app()->getLocale();
        
        // Get localized names of columns
        $translations = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_translation_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->with(['attribute'])
        ->get();
        #dd($request->input());
        
        // Search within lists using dropdowns
        $search_details = null;
        $items_details = collect([]);
        
        // Make sure there is at least one dropdown available
        if ($request->input('fields')) {
            // Get only selected columns to search within
            $search_columns = array_filter($request->input('fields'), function ($val) {
                return $val > 0;
            });
            // Prepare the search query using selected columns
            foreach ($search_columns as $col => $val) {
                $search_details[] = [['column_fk', $col], ['element_fk', intval($val)]];
            }
            if ($search_details) {
                $details = Detail::where(function ($query) use ($search_details) {
                    foreach ($search_details as $n => $s) {
                        $query->orWhere($search_details[$n]);
                    }
                })
                ->with('item')
                ->get();
                
                $items_details = $details->groupBy('item_fk')
                    ->filter(function ($value, $key) use ($search_details) {
                        return $value->count() >= count($search_details);
                    })->map(function ($row) {
                        return $row->first()->item;
                    });
            }
        }
        
        // Full text search in all details containing strings
        $items_full_text = collect([]);
        $search_full_text = $request->input('full_text');
        
        if ($search_full_text) {
            $details = Detail::where('value_string', 'ILIKE', "%{$search_full_text}%")
                ->with('item')
                ->get();
            $items_full_text = $details->map(function ($row) {
                return $row->item;
            });
        }
        
        // Intersect all results on items
        if ($search_details && $search_full_text) {
            $items = $items_details->intersect($items_full_text);
        }
        // Concat all results on items
        else {
            $items = $items_details->concat($items_full_text);
        }
        
        // Taxon search: full name or native name
        $taxa = collect([]);
        $search_taxa = $request->input('taxon_name');
        
        if ($search_taxa) {
            $taxa = Taxon::where('full_name', 'ILIKE', "%{$search_taxa}%")
                ->orWhere('native_name', 'ILIKE', "%{$search_taxa}%")
                ->with('items')
                ->orderBy('full_name')
                ->get();
        }
        
        $search_terms = $request->input();
        return view('search.form', compact('menu_root', 'search_terms', 'taxa', 'items', 'lists', 'colmap', 'translations'));
    }
}
