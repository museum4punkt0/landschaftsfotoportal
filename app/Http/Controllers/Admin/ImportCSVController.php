<?php

namespace App\Http\Controllers\Admin;

use App\Selectlist;
use App\Element;
use App\Value;
use App\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Redirect;
use File;

class ImportCSVController extends Controller
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

    public function index()
    {
        $lists = Selectlist::orderBy('name')->get();
        
        return view('admin.import.csvupload', compact('lists'));
    }
 
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
            // Save CSV separators to session
            $request->session()->put('column_separator', $request->input('column_separator'));
            
            return redirect()->route('import.csv.preview', ['list'=>$request->input('list')]);
        }
        // Saving file failed
        else {
            return redirect()->route('import.csv.upload')
                ->with('error', __('import.save_error'));
        }
    }
    
    public function preview(Request $request)
    {
        // Get CSV file path from session
        $csv_file = $request->session()->get('csv_file');
        $separator = $request->session()->get('column_separator');
        
        // Parse CSV file and read file into array $data
        $data = array_map(function ($d) use ($separator) {
            return str_getcsv($d, $separator);
        }, file($csv_file));
        $csv_data = array_slice($data, 0, 5);
        
        // Load attributes and selected list from database
        $list = Selectlist::find($request->list);
        $attributes = Attribute::all();
        
        return view('admin.import.csvcontent', compact('csv_data', 'attributes', 'list'));
    }
    
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
                                    'attribute' => Attribute::find($selected_attr)->name
                                ]));
                            }
                            if ($selected_attr == -1) {
                                $fail(__('import.attribute_once', ['attribute' => __('import.element_id')]));
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
                        $fail(__('import.missing_attributes'));
                    }
                },
            ],
        ]);
        // Conditional validation
        $validator->sometimes('fields', [
            function ($attribute, $value, $fail) {
                // Hierarchical lists need a column with element IDs
                if (empty(array_count_values($value)[-1])) {
                    $fail(__('import.missing_id'));
                }
            },
            function ($attribute, $value, $fail) {
                // Hierarchical lists need a column with parent IDs
                if (empty(array_count_values($value)[-2])) {
                    $fail(__('import.missing_parent'));
                }
            },
        ], function ($input) {
            return $input->hierarchical; // If closure returns true, the condition is true
        });
        
        if ($validator->fails()) {
            return redirect()->route('import.csv.preview', ['list'=>$request->input('list')])
                        ->withErrors($validator)
                        ->withInput();
        }
                
        // Get CSV file path from session and read file into array $data
        $csv_file = $request->session()->get('csv_file');
        $separator = $request->session()->get('column_separator');
        
        $data = array_map(function ($d) use ($separator) {
            return str_getcsv($d, $separator);
        }, file($csv_file));
        
        $selected_attr = $request->input('fields.*');
        $list_fk = $request->input('list');
        $elements_tree = null; // Maps IDs from CSV onto IDs from Database
        
        // Process each line of given CSV file
        foreach ($data as $number => $line) {
            // Skip first row if containing table headers
            if ($number == 0 && $request->has('header')) {
                continue;
            }
            
            $element_data = [
                'parent_fk' => null,
                'list_fk' => $list_fk,
                'value_summary' => '',
            ];
            $element = Element::create($element_data);
            
            // Process each column (= table cell)
            foreach ($line as $colnr => $cell) {
                // Check for column's attribute chosen by user
                if ($selected_attr[$colnr] > 0) {
                    $value_data = [
                        'element_fk' => $element->element_id,
                        'attribute_fk' => $selected_attr[$colnr],
                        'value' => $cell,
                    ];
                    Value::create($value_data);
                }
                // Save primary key (=ID) of the recent element to temporary tree
                if ($selected_attr[$colnr] == -1) {
                    $elements_tree[intval($cell)] = $element->element_id;
                }
                // Get ID of parent element from temporary tree
                if ($selected_attr[$colnr] == -2) {
                    if (!isset($elements_tree[intval($cell)])) {
                        $element->parent_fk = null;
                    } else {
                        $element->parent_fk = $elements_tree[intval($cell)];
                    }
                    $element->save();
                }
            }
        }
        
        return Redirect::to('admin/lists/list/'.$list_fk)
            ->with('success', __('import.done'));
    }
}
