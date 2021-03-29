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
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->where('public', 1)->orderBy('item_id')->get();
        
        // BEGIN TODO to be refactored
        $item_type = 39;
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
        // END TODO to be refactored
        
        $search_terms = [];
        return view('search.form', compact('menu_root', 'search_terms', 'lists', 'colmap', 'translations'));
    }

    /**
     * Show search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->orderBy('item_id')->get();
        
        // BEGINN TODO to be refactored
        $item_type = 39;
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
        // END TODO to be refactored
        #dd($request->input());
        
        // Search within lists using dropdowns
        $search_columns = array_filter($request->input('fields'), function($val) {
            return $val > 0;
        });
        foreach ($search_columns as $col => $val) {
            $search[] = [['column_fk', $col], ['element_fk', intval($val)]];
        }
        if (isset($search)) {
            $details = Detail::where(function ($query) use ($search) {
                    foreach ($search as $n => $s) {
                        $query->orWhere($search[$n]);
                    }
                })
                ->with('item')
                ->get();
            
            $items = $details->groupBy('item_fk')
                ->filter(function ($value, $key) use ($search) {
                    return $value->count() >= count($search);
                })->map(function ($row) {
                    return $row->first()->item;
                });
        }
        
        // Full text search in all details containing strings
        $search = $request->input('full_text');
        if ($search) {
            $details = Detail::where('value_string', 'LIKE', "%{$search}%")
                ->with('item')
                ->get();
            $items = $details->map(function ($row) {
                return $row->item;
            });
        }
        
        // Taxon search: full name or native name
        $search = $request->input('taxon_name');
        $taxa = Taxon::where('full_name', 'LIKE', "%{$search}%")
            ->orWhere('native_name', 'LIKE', "%{$search}%")
            ->with('items')
            ->orderBy('full_name')
            ->get();
        
        $search_terms = $request->input();
        return view('search.form', compact('menu_root', 'search_terms', 'taxa', 'items', 'lists', 'colmap', 'translations'));
    }
}
