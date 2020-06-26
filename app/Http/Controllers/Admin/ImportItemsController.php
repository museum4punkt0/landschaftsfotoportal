<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\Item;
use App\Selectlist;
use App\Taxon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Validator,Redirect,File;

class ImportItemsController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        if($files = $request->file('fileUpload')) {
            $destinationPath = 'storage/'. config('media.import_dir');
            $fileName = date('YmdHis') .".". $files->getClientOriginalExtension();
            $files->move($destinationPath, $fileName);
            $csv_file = $destinationPath.$fileName;
            
            // Save CSV file path to session
            $request->session()->put('csv_file', $csv_file);
            
            return redirect()->route('import.items.preview', ['item_type' => $request->input('item_type')]);
        }
        // Saving file failed
        else
        {
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
        // Get CSV file path from session and read file into array $data
        $csv_file = $request->session()->get('csv_file');
        
        // Parse CSV file
        $data = array_map(function($d) {
            return str_getcsv($d, ";");
        }, file($csv_file));
        $csv_data = array_slice($data, 0, 5);
        
        // Load column mapping for selected item_type from database
        $colmaps = ColumnMapping::where('item_type_fk', $request->item_type)->get();
        
        // Check for defined columns for this item_type, otherwise redirect back with error message
        if($colmaps->isEmpty()) {
            return redirect()->route('import.items.upload')
                ->with('error', __('colmaps.none_available'));
        }
        
        $items = Item::tree()->depthFirst()->get();
        
        return view('admin.import.itemscontent', compact('csv_data', 'colmaps', 'items'));
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
                    foreach(array_count_values($value) as $selected_attr => $quantity) {
                        if($selected_attr !== 0 && $quantity > 1) {
                            if($selected_attr > 0)
                                $fail(__('import.attribute_once', [
                                    'attribute' => Column::find($selected_attr)->description
                                ]));
                            if($selected_attr == -1)
                                $fail(__('import.attribute_once', ['attribute' => __('import.element_id')]));
                            if($selected_attr == -2)
                                $fail(__('import.attribute_once', ['attribute' => __('import.parent_id')]));
                        }
                    }
                },
                function ($attribute, $value, $fail) {
                    // Check for missing attributes, at least one (column) must be selected
                    $a = array_filter($value, function ($v) { return $v>0; } );
                    if(!array_sum($a))
                        $fail(__('import.missing_columns'));
                },
            ],
        ]);
        
        if($validator->fails()) {
            return redirect()->route('import.items.preview', ['item_type' => $request->input('item_type')])
                        ->withErrors($validator)
                        ->withInput();
        }
                
        // Get CSV file path from session and read file into array $data
        $csv_file = $request->session()->get('csv_file');
        $data = array_map(function($d) {
            return str_getcsv($d, ";");
        }, file($csv_file));
        
        $selected_attr = $request->input('fields.*');
        $elements_tree = null; // Maps IDs from CSV onto IDs from Database
        $warning_status_msg = null;
        #$messageBag = new MessageBag;
        
        // Process each line of given CSV file
        foreach($data as $number => $line) {
            // Skip first row if containing table headers
            if($number == 0 && $request->has('header'))
                continue;
            
            // Try to match taxon for given full scientific name
            $taxon = Taxon::where('full_name', $line[array_search('-3', $selected_attr)])->first();
            if(empty($taxon)) {
                // Taxon not found: skip this one and set warning message
                $warning_status_msg .= " ". __('import.taxon_not_found', ['full_name' => $line[array_search('-3', $selected_attr)]]);
                $request->session()->flash('warning', $warning_status_msg);
                // TODO: use messageBag for arrays
                #$messageBag->add('warning', $warning_status_msg);
                continue;
            }
            else {
                $item_data = [
                    'parent_fk' => $request->input('parent'),
                    'item_type_fk' => $request->input('item_type'),
                    'taxon_fk' => $taxon->taxon_id,
                ];
                // Check for already existing items
                $existing_item = Item::where([
                    ['taxon_fk', $taxon->taxon_id],
                    ['item_type_fk', $request->input('item_type')],
                ])->first();
                if(!empty($existing_item) && $request->has('unique_taxa')) {
                    $warning_status_msg .= " ". __('import.taxon_exists', ['full_name' => $line[array_search('-3', $selected_attr)]]);
                    $request->session()->flash('warning', $warning_status_msg);
                    // TODO: use messageBag for arrays
                    continue;
                }
                else {
                    $item = Item::create($item_data);
                    
                    // Process each column (= table cell)
                    foreach($line as $colnr => $cell) {
                        // Check for column's attribute chosen by user
                        if($selected_attr[$colnr] > 0) {
                            $detail_data = [
                                'item_fk' => $item->item_id,
                                'column_fk' => $selected_attr[$colnr],
                            ];
                            $data_type = Column::find($selected_attr[$colnr])->getDataType();
                            switch ($data_type) {
                                case '_list_':
                                    $detail_data['element_fk'] = intval($cell);
                                    break;
                                case '_integer_':
                                    $detail_data['value_int'] = intval($cell);
                                    break;
                                case '_float_':
                                    $detail_data['value_float'] = floatval(strtr($cell, ',', '.'));
                                    break;
                                case '_date_':
                                    $detail_data['value_date'] = $cell;
                                    break;
                                case '_string_':
                                case '_url_':
                                case '_image_':
                                    $detail_data['value_string'] = $cell;
                                    break;
                            }
                            Detail::create($detail_data);
                        }
                    }
                }
            }
        }
        
        return Redirect::to('admin/item')
            ->with('success', __('import.done'));
    }
}
