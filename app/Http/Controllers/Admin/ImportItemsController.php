<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\Item;
use App\Selectlist;
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
        
        // Count the number of items in CSV file
        $total_items = count($data);
        if ($request->has('header')) {
            $total_items--;
        }
        
        Log::channel('import')->info(__('import.read_csv', ['file' => $csv_file]));
        
        $selected_attr = $request->input('fields.*');
        $warning_status_msg = null;
        
        // Save selected attributes to session
        $request->session()->put('selected_attr', $selected_attr);
        
        // Save total number of items in CSV to session
        $request->session()->put('total_items', $total_items);
        
        // Save request input data to session
        session($request->except(['_token', 'fields']));
        
        // Get original CSV file name from session
        $file_name = $request->session()->get('file_name');
        
        return view('admin.import.itemsprocess', compact('file_name', 'data', 'total_items'));
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
