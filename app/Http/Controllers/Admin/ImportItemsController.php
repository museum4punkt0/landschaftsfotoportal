<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\ColumnMapping;
use App\DateRange;
use App\Detail;
use App\Element;
use App\Item;
use App\Selectlist;
use App\Value;
use App\Taxon;
use App\Utils\Image;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Validator;
use Redirect;
use File;

class ImportItemsController extends Controller
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
     * Display a form for uploading a CSV file.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        return view('admin.import.itemsupload', compact('item_types'));
    }
 
    /**
     * Store a newly uploaded CSV file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        // Validate file size and extension
        $request->validate([
            'fileUpload' => 'required|mimes:csv,txt|max:4096',
        ]);
        
        // Save CSV file
        if ($files = $request->file('fileUpload')) {
            $destinationPath = 'storage/'. config('media.import_dir');
            $fileName = date('YmdHis') .".". $files->getClientOriginalExtension();
            $files->move($destinationPath, $fileName);
            $csv_file = $destinationPath.$fileName;
            
            // Save CSV file path to session
            $request->session()->put('csv_file', $csv_file);
            // Save original CSV file name to session
            $request->session()->put('file_name', $files->getClientOriginalName());
            // Save CSV separators to session
            $request->session()->put('column_separator', $request->input('column_separator'));
            $request->session()->put('element_separator', $request->input('element_separator'));
            
            return redirect()->route('import.items.preview', ['item_type' => $request->input('item_type')]);
        }
        // Saving file failed
        else {
            return redirect()->route('import.items.upload')
                ->with('error', __('import.save_error'));
        }
    }
    
    /**
     * Display a preview of the uploaded file and a form with options for import.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        // Get CSV file path from session and
        $csv_file = $request->session()->get('csv_file');
        // Get original CSV file name from session
        $file_name = $request->session()->get('file_name');
        $separator = $request->session()->get('column_separator');
        
        // Parse CSV file read file into array $data
        $data = array_map(function ($d) use ($separator) {
            return str_getcsv($d, $separator);
        }, file($csv_file));
        $csv_data = array_slice($data, 0, 5);
        
        // Load column mapping for selected item_type from database
        $colmaps = ColumnMapping::where('item_type_fk', $request->item_type)->get();
        
        // Check for defined columns for this item_type, otherwise redirect back with error message
        if ($colmaps->isEmpty()) {
            return redirect()->route('import.items.upload')
                ->with('error', __('colmaps.none_available'));
        }
        
        $items = Item::tree()->depthFirst()->get();
        
        // Get list of all item types for dropdown menu
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        return view('admin.import.itemscontent', compact('file_name', 'csv_data', 'colmaps', 'items', 'item_types'));
    }
    
    /**
     * Process the uploaded file and store its content to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // Validate the form inputs
        $validator = Validator::make($request->all(), [
            'fields' => [
                function ($attribute, $value, $fail) {
                    // Check for duplicate attributes but not for 'ignored' ones
                    foreach (array_count_values($value) as $selected_attr => $quantity) {
                        if ($selected_attr !== 0 && $quantity > 1) {
                            if ($selected_attr > 0) {
                                $fail(__('import.attribute_once', [
                                    'attribute' => Column::find($selected_attr)->description
                                ]));
                            }
                            if ($selected_attr == -2) {
                                $fail(__('import.attribute_once', ['attribute' => __('import.parent_id')]));
                            }
                        }
                    }
                },
                function ($attribute, $value, $fail) {
                    // Check for missing attributes, at least one (column) must be selected
                    $a = array_filter($value, function ($v) {
                        return $v>0;
                    });
                    if (!array_sum($a)) {
                        $fail(__('import.missing_columns'));
                    }
                },
            ],
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('import.items.preview', ['item_type' => $request->input('item_type')])
                        ->withErrors($validator)
                        ->withInput();
        }
                
        // Get CSV file path from session and read file into array $data
        $csv_file = $request->session()->get('csv_file');
        $separator = $request->session()->get('column_separator');
        
        $data = array_map(function ($d) use ($separator) {
            return str_getcsv($d, $separator);
        }, file($csv_file));
        
        Log::channel('import')->info(__('import.read_csv', ['file' => $csv_file]));
        
        $selected_attr = $request->input('fields.*');
        $warning_status_msg = null;
        #$messageBag = new MessageBag;
        
        // Process each line of given CSV file
        foreach ($data as $number => $line) {
            // Skip first row if containing table headers
            if ($number == 0 && $request->has('header')) {
                continue;
            }
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
                        ['item_type_fk', $request->input('item_type')],
                    ])->first();
                    if (!empty($existing_item) && $request->has('unique_taxa')) {
                        $warning_status_msg .= " ". __('import.taxon_exists', ['full_name' => $line[array_search('-3', $selected_attr)]]);
                        $request->session()->flash('warning', $warning_status_msg);
                        // TODO: use messageBag for arrays
                        continue;
                    }
                }
            }
            // All checks have been passed, let's create the item
            $item_data = [
                'parent_fk' => $request->input('parent'),
                'item_type_fk' => $request->input('item_type'),
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
                                }
                                else {
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
                            Image::storeImageDimensions(config('media.full_dir'), $cell,
                                $item->item_id, $selected_attr[$colnr]);
                            // Create resized images
                            Image::processImageResizing(config('media.full_dir'), $cell);
                            // No break, but fall through
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
                $pit = $request->input('parent_item_type');
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
        }
        Log::channel('import')->info(__('import.done'));
        
        return Redirect::to('admin/item')
            ->with('success', __('import.done'));
    }
    
    /**
     * Fix file name extension for items with item type _image_ and column ID 63 data type _image_.
     *
     * @return \Illuminate\Http\Response
     */
    public function fix_ext()
    {
        $details = Detail::where('column_fk', 63)->get();
        
        $count = 0;
        // Copy title string for all details if doesn't exist yet
        foreach ($details as $detail) {
            if (strpos($detail->value_string, '.tif')) {
                $detail->value_string = str_replace('.tif', '.jpg', $detail->value_string);
                $detail->save();
                $count++;
            }
        }
        
        return Redirect::to('admin/item')
            ->with('success', __('items.file_ext_fixed', ['count' => $count]));
    }
}
