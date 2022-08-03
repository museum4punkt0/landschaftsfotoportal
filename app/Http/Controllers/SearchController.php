<?php

namespace App\Http\Controllers;

use App\Column;
use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\Selectlist;
use App\Value;
use App\Item;
use App\Taxon;
use App\Utils\Localization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Debugbar;

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
        
        // Fake the menu path of the requested item
        $path = [];

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
        $lists = Element::getTrees($colmap);
        
        // Load all date ranges used by this item_type's columns
        $dateranges = null;
        foreach ($colmap as $cm) {
            if ($cm->column->getDataType() == '_date_range_') {
                // Get bounds for daterange
                // TODO: Refactor, copied from Item::timeline()
                $bounds = Detail::selectRaw("
                        EXTRACT(DECADE FROM MIN(LOWER(value_daterange)))*10 AS lower,
                        EXTRACT(DECADE FROM MAX(UPPER(value_daterange)))*10 AS upper
                    ")
                    ->whereHas('item', function (Builder $query) {
                        $query->where('public', 1);
                    })
                    ->where('column_fk', $cm->column_fk) 
                    ->first();
                
                // For each decade...
                // TODO: Refactor, copied from Item::timeline()
                for ($decade = $bounds->lower; $decade <= $bounds->upper; $decade += 10) {
                    $daterange = '['. date('Y-m-d', mktime(0, 0, 0, 1, 1, $decade)) .','.
                        date('Y-m-d', mktime(0, 0, 0, 1, 1, $decade + 10)) .')';
                    
                    // Get number of items per decade
                    $decades[$decade] = Detail::
                        whereHas('item', function (Builder $query) {
                            $query->where('public', 1);
                        })
                        ->where('column_fk', $cm->column_fk)
                        ->whereRaw("value_daterange && '$daterange'")
                        ->count();
                }
                $dateranges[$cm->column_fk] = $decades;
            }
        }
        #dd($dateranges);
        
        // Get current UI language
        $lang = app()->getLocale();

        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);
        
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        
        // Search within lists using dropdowns
        $search_details = null;
        $items_details = collect([]);
        
        // Make sure there is at least one dropdown selected or text input filled
        if ($request->input('fields')) {
            // Get only selected columns to search within
            $search_columns = array_filter($request->input('fields'), function ($val) {
                // Arrays are used for min/max values, e.g. with lat/lon input fields
                if (is_array($val)) {
                    return array_sum($val);
                }
                // Select input fields contain 0 if not selected by user
                else {
                    return $val != 0;
                }
            });
            Debugbar::debug($search_columns);
            
            // Prepare the search query WHERE clause using selected columns
            foreach ($search_columns as $col => $val) {
                switch (Column::find($col)->getDataType()) {
                    case '_list_':
                        $search_details[] = [['column_fk', $col], ['element_fk', intval($val)]];
                    break;
                    case '_float_':
                        $min_max = null;
                        // Handle unset min/max input fields
                        if (!is_null($val['min'])) {
                            $min_max[] = ['column_fk', $col];
                            $min_max[] = ['value_float', '>=', floatval(strtr($val['min'], ',', '.'))];
                        }
                        if (!is_null($val['max'])) {
                            $min_max[] = ['column_fk', $col];
                            $min_max[] = ['value_float', '<=', floatval(strtr($val['max'], ',', '.'))];
                        }
                        $search_details[] = $min_max;
                    break;
                    case '_date_range_':
                        // Valid decade
                        if ($val > 0) {
                            $daterange = '['. date('Y-m-d', mktime(0, 0, 0, 1, 1, intval($val))) .','.
                                date('Y-m-d', mktime(0, 0, 0, 1, 1, intval($val) + 10)) .')';
                            $search_details[] = [['column_fk', $col], ['value_daterange', '&&', $daterange]];
                        }
                        // Unknown date (date range not set)
                        else {
                            $search_details[] = [['column_fk', $col], ['value_daterange', null]];
                        }
                    break;
                }
            }
            Debugbar::debug($search_details);
            
            // Details search (except for full text)
            if ($search_details) {
                $details = Detail::where(function ($query) use ($search_details) {
                    foreach ($search_details as $n => $s) {
                        $query->orWhere($search_details[$n]);
                    }
                })
                ->whereHas('item', function (Builder $query) {
                    $query->where('public', 1);
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
                ->whereHas('item', function (Builder $query) {
                    $query->where('public', 1);
                })
                ->with('item')
                ->get();
            
            $items_full_text = $details->map(function ($row) {
                return $row->item;
            })->unique();
        }
        
        // Intersect all results on items
        if ($search_details && $search_full_text) {
            $items = $items_details->intersect($items_full_text);
        }
        // Concat all results on items
        else {
            // Simple concatenating isn't sufficient because we need an eloquent collection for modelKeys()
            if ($items_details->count()) {
                $items = $items_details->concat($items_full_text);
            }
            else {
                $items = $items_full_text->concat($items_details);
            }
        }
        
        // Save primary keys of all found items to session
        if ($items->count()) {
            $request->session()->put('search_results', $items->modelKeys());
        }
        else {
            $request->session()->forget('search_results');
        }
        
        // Taxon search: full name or native name
        $taxa = collect([]);
        $search_taxa = $request->input('taxon_name');
        
        if ($search_taxa) {
            $taxa = Taxon::where('full_name', 'ILIKE', "%{$search_taxa}%")
                ->orWhere('native_name', 'ILIKE', "%{$search_taxa}%")
                // TODO: this should be more flexible; allow configuration of multiple/different item_types
                ->whereHas('items', function (Builder $query) {
                    $query->where('item_type_fk', '<>', 188);
                })
                ->with('items')
                ->orderBy('full_name')
                ->get();
        }
        
        $search_terms = $request->input();

        // Get all HTTP query parameters except for lat/lon
        $column_ids['lon'] = optional(Column::ofDataType('_float_')
                                            ->ofItemType('_image_')
                                            ->ofSubType('location_lon')
                                            ->first())
                                            ->column_id;
        $column_ids['lat'] = optional(Column::ofDataType('_float_')
                                            ->ofItemType('_image_')
                                            ->ofSubType('location_lat')
                                            ->first())
                                            ->column_id;
        $request_query = $request->except([
            'fields.' . $column_ids['lon'],
            'fields.' . $column_ids['lat'],
            'source',
        ]);
        Debugbar::debug($request_query);
        
        // Prepare the query string to be passed to the map controller
        $query_str = http_build_query($request_query);

        return view('search.form', compact('menu_root', 'path', 'search_terms', 'lists', 'dateranges',
            'colmap', 'translations', 'item_types', 'taxa', 'items', 'query_str'));
    }
}
