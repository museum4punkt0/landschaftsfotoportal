<?php

namespace App\Http\Controllers\Admin;

use App\Taxon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect,File,Validator;

class ImportTaxaController extends Controller
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
        return view('admin.import.taxaupload');
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
            $destinationPath = 'storage/import/';
            $fileName = date('YmdHis') .".". $files->getClientOriginalExtension();
            $files->move($destinationPath, $fileName);
            $csv_file = $destinationPath.$fileName;
            
            // Save CSV file path to session
            $request->session()->put('csv_file', $csv_file);
            
            return Redirect::to('admin/import/taxa/preview');
        }
        // Saving file failed
        else
        {
            return Redirect::to('admin/import/taxa/upload')
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
        $csv_data = array_slice($data, 0, 10);
        
        return view('admin.import.taxacontent', compact('csv_data'));
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
                        if ($selected_attr !== 0 && $quantity > 1) {
                            $fail(__('import.attribute_once', [
                                'attribute' => __('taxon.'.$selected_attr)
                            ]));
                        }
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with element IDs
                    if(empty(array_count_values($value)['gsl_id'])) {
                        $fail(__('import.missing_id'));
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with parent IDs
                    if(empty(array_count_values($value)['parent'])) {
                        $fail(__('import.missing_parent'));
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with taxon names
                    if(empty(array_count_values($value)['taxon_name'])) {
                        $fail(__('validation.required', ['attribute' => __('taxon.taxon_name')]));
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with taxon authors
                    if(empty(array_count_values($value)['taxon_author'])) {
                        $fail(__('validation.required', ['attribute' => __('taxon.taxon_author')]));
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with taxon name supplements
                    if(empty(array_count_values($value)['taxon_suppl'])) {
                        $fail(__('validation.required', ['attribute' => __('taxon.taxon_suppl')]));
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with taxon native names
                    if(empty(array_count_values($value)['native_name'])) {
                        $fail(__('validation.required', ['attribute' => __('taxon.native_name')]));
                    }
                },
                function ($attribute, $value, $fail) {
                    // Import needs a column with taxon valid name IDs
                    if(empty(array_count_values($value)['valid_name'])) {
                        $fail(__('validation.required', ['attribute' => __('taxon.valid_name')]));
                    }
                },
            ],
        ]);
        
        if($validator->fails()) {
            return redirect()->route('import.taxa.preview')
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
        
        // Process each line of given CSV file
        foreach($data as $number => $line) {
            // Skip first row if containing table headers
            if($number == 0 && $request->has('header'))
                continue;
            
            $taxon_data['gsl_id'] = null;
            
            // Process each column (= table cell)
            foreach($line as $colnr => $cell) {
                
                switch($selected_attr[$colnr]) {
                    // Save primary key (=ID) of the recent element to temporary tree
                    case 'gsl_id':
                        $taxon_data['gsl_id'] = intval($cell);
                        break;
                    // Get ID of parent element from temporary tree
                    case 'parent':
                        if(!isset($elements_tree[intval($cell)]))
                            $taxon_data['parent_fk'] = null;
                        else
                            $taxon_data['parent_fk'] = $elements_tree[intval($cell)];
                        break;
                    // Get ID of taxon with valid name (in case of synoymes) from temporary tree
                    case 'valid_name':
                        if(!isset($elements_tree[intval($cell)]))
                            $taxon_data['valid_name'] = null;
                        else
                            $taxon_data['valid_name'] = $elements_tree[intval($cell)];
                        break;
                    default:
                        // Check if column was chosen for import
                        if($selected_attr[$colnr]) {
                            $taxon_data[$selected_attr[$colnr]] = $cell;
                        }
                }
                
            }
            // Concatenate some name parts to full name, if not present or not chosen
            if(empty($taxon_data['full_name'])) {
                $taxon_data['full_name'] = $taxon_data['taxon_name'] ." ".
                    $taxon_data['taxon_author'] ." ". $taxon_data['taxon_suppl'];
            }
            
            // Store taxon to database unless it doesn't exist
            $taxon = Taxon::firstOrCreate(['gsl_id' => $taxon_data['gsl_id']], $taxon_data);
            
            // Save primary key (=ID) of the recent element to temporary tree
            $elements_tree[$taxon->gsl_id] = $taxon->taxon_id;
        }
        
        return Redirect::to('admin/taxon')
            ->with('success', __('import.done'));
    }
}
