<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\DateRange;
use App\Detail;
use App\Item;
use App\Location;
use App\Taxon;
use App\Value;
use App\Utils\Image;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Redirect;
use File;

class AjaxImportController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importLine(Request $request)
    {
        // Get HTTP parameters from request
        $start = intval($request->start); // line number to start from, counting from 1
        $limit = intval($request->limit); // max number of lines to import per loop cycle
        
        $total_items = intval($request->session()->get('total_items')); // total number of lines w/o header
        $selected_attr = $request->session()->get('selected_attr');
        $geocoder_attr = $request->session()->get('geocoder_attr');
        
        $geocoder_results = null;
        $warning_status_msg = null;
        #dd($request);
        
        // Get CSV file path from session and read file into array $data
        $csv_file = $request->session()->get('csv_file');
        $separator = $request->session()->get('column_separator');
        
        $data = array_map(function ($d) use ($separator) {
            return str_getcsv($d, $separator);
        }, file($csv_file));
        $total_lines = count($data); // total number of lines with header
        
        
        // Process each line of given CSV file; $number contains line number
        for ($number = $start; $number <= $total_lines && $number < ($start + $limit); $number++) {
            // Skip first row if containing table headers
            if ($number == 1 && $request->session()->has('header')) {
                continue;
            }
            
            $line = $data[$number - 1];
            $taxon_fk = null;
            // Check if a taxon is associated to item to be imported
            if (array_search('-3', $selected_attr)) {
                // Try to match taxon for given full scientific name
                $taxon = Taxon::where('full_name', $line[array_search('-3', $selected_attr)])->first();
                if (empty($taxon)) {
                    // Taxon not found: skip this one and set warning message
                    $warning_status_msg .= " ". __('import.taxon_not_found', ['full_name' => $line[array_search('-3', $selected_attr)]]);
                    $request->session()->flash('warning', $warning_status_msg);
                    // TODO: use messageBag for arrays
                    #$messageBag->add('warning', $warning_status_msg);
                    continue;
                } else {
                    $taxon_fk = $taxon->taxon_id;
                    // Check for already existing items (depending on taxon)
                    $existing_item = Item::where([
                        ['taxon_fk', $taxon_fk],
                        ['item_type_fk', $request->session()->get('item_type')],
                    ])->first();
                    if (!empty($existing_item) && $request->session()->has('unique_taxa')) {
                        $warning_status_msg .= " ". __('import.taxon_exists', ['full_name' => $line[array_search('-3', $selected_attr)]]);
                        $request->session()->flash('warning', $warning_status_msg);
                        // TODO: use messageBag for arrays
                        continue;
                    }
                }
            }
            // All checks have been passed, let's create the item
            $item_data = [
                'parent_fk' => $request->session()->get('parent'),
                'item_type_fk' => $request->session()->get('item_type'),
                'taxon_fk' => $taxon_fk,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ];
            $item = Item::create($item_data);
            Log::channel('import')->info(__('import.item_imported', ['id' => $item->item_id]), [
                'line' => $number,
            ]);
            
            // Process each column (= table cell)
            foreach ($line as $colnr => $cell) {
                // Check for column's attribute chosen by user
                if ($selected_attr[$colnr] > 0) {
                    $detail_elements = null;
                    
                    $detail_data = [
                        'item_fk' => $item->item_id,
                        'column_fk' => $selected_attr[$colnr],
                    ];
                    $data_type = Column::find($selected_attr[$colnr])->getDataType();
                    switch ($data_type) {
                        case '_list_':
                            // Get element's ID for given value, independent of language
                            $attr = $selected_attr[$colnr];
                            $value = Value::whereHas('element', function ($query) use ($attr) {
                                $query->where('list_fk', Column::find($attr)->list_fk);
                            })
                            ->where('value', $cell)
                            ->first();
                            // TODO: don't import and add warning if value doesn't exist in list
                            $detail_data['element_fk'] = $value ? $value->element_fk : null;
                            if (!$value) {
                                Log::channel('import')->warning(__('import.element_mismatch', ['element' => $element]), [
                                    'list' => Column::find($attr)->list_fk,
                                    'item' => $item->item_id,
                                    'line' => $number,
                                ]);
                            }
                            break;
                        case '_multi_list_':
                            foreach (explode($request->session()->get('element_separator'), $cell) as $element) {
                                // Strip whitespaces from beginning and end
                                $element = trim($element);
                                
                                // Get element's ID for given value, independent of language
                                $attr = $selected_attr[$colnr];
                                $value = Value::whereHas('element', function ($query) use ($attr) {
                                    $query->where('list_fk', Column::find($attr)->list_fk);
                                })
                                ->where('value', $element)
                                ->first();
                                // TODO: don't import and add warning if value doesn't exist in list
                                if ($value) {
                                    $detail_elements[] = $value->element_fk;
                                } else {
                                    Log::channel('import')->warning(__('import.element_mismatch', ['element' => $element]), [
                                        'list' => Column::find($attr)->list_fk,
                                        'item' => $item->item_id,
                                        'line' => $number,
                                    ]);
                                }
                            }
                            break;
                        case '_boolean_':
                        case '_integer_':
                        case '_image_ppi_':
                            $detail_data['value_int'] = $cell == '' ? null : intval($cell);
                            break;
                        case '_float_':
                            $detail_data['value_float'] = $cell == '' ? null : floatval(strtr($cell, ',', '.'));
                            break;
                        case '_date_':
                            $detail_data['value_date'] = $cell ? $cell : null;
                            break;
                        case '_date_range_':
                            $dates = explode(',', $cell);
                            // Convert a single date into a date range
                            if (count($dates) == 1) {
                                $dates[1] = $dates[0];
                            }
                            $detail_data['value_daterange'] = new DateRange($dates[0], $dates[1]);
                            break;
                        case '_image_':
                            // Store image dimensions in database
                            Image::storeImageDimensions(
                                config('media.full_dir'),
                                $cell,
                                $item->item_id,
                                $selected_attr[$colnr]
                            );
                            Image::storeImageSize(
                                config('media.full_dir'),
                                $cell,
                                $item->item_id,
                                $selected_attr[$colnr]
                            );
                            // Create resized images
                            Image::processImageResizing(config('media.full_dir'), $cell);
                            // no break, but fall through
                        case '_string_':
                        case '_title_':
                        case '_image_title_':
                        case '_image_copyright_':
                        case '_html_':
                        case '_url_':
                        case '_map_':
                            $detail_data['value_string'] = $cell;
                            break;
                    }
                    $detail = Detail::create($detail_data);
                    
                    // Save chosen elements for drop-down lists with multiple selections
                    if ($detail_elements) {
                        $detail->elements()->attach($detail_elements);
                    }
                }
                
                // Set parent fkey of item if individually choosen per item
                $pit = $request->session()->get('parent_item_type');
                // Try to match parent item using a detail
                if ($selected_attr[$colnr] == -1) {
                    // Try to match taxon for given full scientific name
                    $parent_item = Item::whereHas('details', function (Builder $query) use ($cell, $pit) {
                        $query->where('value_string', $cell);
                    })->where('item_type_fk', $pit)->first();
                    if (!empty($parent_item)) {
                        $item->parent_fk = $parent_item->item_id;
                        $item->save();
                    }
                }
                // Try to match parent item using a taxon's full scientific name
                if ($selected_attr[$colnr] == -2) {
                    // Try to match taxon for given full scientific name
                    $parent_item = Item::whereHas('taxon', function (Builder $query) use ($cell, $pit) {
                        $query->where('full_name', $cell);
                    })->where('item_type_fk', $pit)->first();
                    if (!empty($parent_item)) {
                        $item->parent_fk = $parent_item->item_id;
                        $item->save();
                    }
                }
            }
            // Use geocoder for address data from current line
            if ($request->session()->has('geocoder_enable')) {
                $location = $this->getLocationFromLine($line);
                $geocoder_results[] = [
                    'item' => $item->item_id,
                    'original' => $location,
                    'results' => $location->getGeocodingResults('forward'),
                ];
                
                if (!$request->session()->has('geocoder_interactive')) {
                    // Update item with lat and lon from location
                    $item->updateLatLon($location);
                }
            }
        }
        // Reset last line number increment
        $number--;
        
        // Reached last line of import file
        if ($number == $total_lines) {
            Log::channel('import')->info(__('import.done'));
        }
        
        $response_data = [
            'lastLine' => $number,
            'lastItem' => $request->session()->has('header') ? $number-1 : $number,
            'totalItems' => $total_items,
            'statusMessage' => $warning_status_msg,
            'geocoderResults' => $geocoder_results,
            'geocoderInteractive' => $request->session()->has('geocoder_interactive'),
        ];
        
        return response()->json($response_data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importLatLon(Request $request)
    {
        // Get HTTP parameters from request
        $item = intval($request->item);
        $lat = floatval($request->lat);
        $lon = floatval($request->lon);
        
        $warning_status_msg = null;
        #dd($request);
        
        $location = new Location();
        $location->lat = $lat;
        $location->lon = $lon;
        
        Item::find($item)->updateLatLon($location);
        
        $response_data = [
            'statusMessage' => $warning_status_msg,
        ];
        
        return response()->json($response_data);
    }

    /**
     * Collect location data from CSV line and pass to geocoder.
     *
     * @param  array  $line
     * @return \App\Location
     */
    private function getLocationFromLine($line)
    {
        $geocoder_attr = session('geocoder_attr');
        
        // Prepare data for geocoder query
        $location = new Location();
        $location->country = $line[$geocoder_attr['country']];
        $location->city = $line[$geocoder_attr['city']];
        $location->street = $line[$geocoder_attr['street']];
        $location->locality = $line[$geocoder_attr['locality']];
        
        return $location;
    }
}
