<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\ColumnMapping;
use App\Detail;
use App\Element;
use App\Item;
use App\Location;
use App\Selectlist;
use App\Http\Controllers\Controller;
use App\Utils\Localization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Debugbar;
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
        $this->authorize('import-items');

        // Get current UI language
        $lang = app()->getLocale();
        $item_types = Localization::getItemTypes($lang);
        
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
        $this->authorize('import-items');

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
        $this->authorize('import-items');

        // Get selected attributes from cookie
        $selected_attr = json_decode($request->cookie('import_'. $request->item_type), true);
        $geocoder_attr = json_decode($request->cookie('import_geocoder_'. $request->item_type), true);
        
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
        $colmaps = ColumnMapping::where('item_type_fk', $request->item_type)
            ->with('column')
            ->orderBy('column_order')
            ->get();
        
        // Check for defined columns for this item_type, otherwise redirect back with error message
        if ($colmaps->isEmpty()) {
            return redirect()->route('import.items.upload')
                ->with('error', __('colmaps.none_available'));
        }

        // Check for existing 'item_type' config on colmaps for relation columns
        $response = $this->checkRelationConfig($colmaps);
        if ($response) {
            return $response;
        }

        $items = Item::tree()->depthFirst()->get();
        
        // Get current UI language
        $lang = app()->getLocale();
        $item_types = Localization::getItemTypes($lang);
        
        return view('admin.import.itemscontent', compact('file_name', 'csv_data', 'colmaps', 'items', 'item_types', 'selected_attr', 'geocoder_attr'));
    }
    
    /**
     * Process the uploaded file and store its content to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        $this->authorize('import-items');

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
                'array',
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
        // Save to cookie for future usage after session has expired
        $cookie_content = json_encode($selected_attr, JSON_FORCE_OBJECT);
        Cookie::queue(Cookie::forever('import_'. $request->input('item_type'), $cookie_content));
        
        $geocoder_attr = $request->input('geocoder');
        // Save to cookie for future usage after session has expired
        $cookie_content = json_encode($geocoder_attr, JSON_FORCE_OBJECT);
        Cookie::queue(Cookie::forever('import_geocoder_'. $request->input('item_type'), $cookie_content));
        
        $warning_status_msg = null;
        
        // Save selected attributes to session
        $request->session()->put('selected_attr', $selected_attr);
        $request->session()->put('geocoder_attr', $geocoder_attr);
        
        // Save total number of items in CSV to session
        $request->session()->put('total_items', $total_items);
        
        // Reset input from all checkboxes because it wont be updated for un-checked ones
        $request->session()->forget(['header', 'geocoder_enable', 'geocoder_interactive']);
        
        // Save all request input data to session
        session($request->except(['_token', 'fields', 'geocoder']));
        
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
        $this->authorize('import-items');

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

    /**
     * Check for existing and valid 'item_type' config on colmaps for columns of data type 'relation'.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $colmaps
     * @return \Illuminate\Http\Response | false
     */
    private function checkRelationConfig($colmaps)
    {
        foreach ($colmaps as $cm) {
            // Check for columns with data type '_relation_'
            if ($cm->column->data_type_name == '_relation_') {
                Debugbar::debug('column with relation: ' . $cm->column->description);
                $item_type = $cm->getConfigValue('item_type');
                Debugbar::debug('--> related item type: ' . $item_type);
                if (!$item_type) {
                    return back()->with('error', __('import.missing_related_item_type',
                        ['desc' => $cm->column->description]
                    ));
                }
                // Check if item type is valid
                $element = Element::find($item_type);
                Debugbar::debug($element);
                if (!$element || optional($element->list)->name !== '_item_type_') {
                    return back()->with('error', __('import.invalid_related_item_type',
                        ['desc' => $cm->column->description, 'id' => $item_type]
                    ));
                }
            }
        }
        return false;
    }
}
