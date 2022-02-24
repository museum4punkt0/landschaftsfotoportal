<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\Selectlist;
use App\Element;
use App\Attribute;
use App\Value;
use App\Utils\Localization;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Redirect;

class ColumnController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        // Use app\Policies\ColumnPolicy for authorizing ressource controller
        $this->authorizeResource(Column::class, 'column');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aFilter = [
            'column_id' => $request->input('column_id'),
            'description' => $request->input('description'),
            'name' => $request->input('name'),
            'data_type_fk' => $request->input('data_type_fk'),
            'list_fk' => $request->input('list_fk'),
        ];
        
        $orderby = $request->input('orderby', 'description');
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');
        
        $aWhere = [];
        if (!is_null($aFilter['column_id'])) {
            $aWhere[] = ['column_id', '=', $aFilter['column_id']];
        }
        if (!is_null($aFilter['description'])) {
            $aWhere[] = ['description', 'ilike', '%' . $aFilter['description'] . '%'];
        }
        if (!is_null($aFilter['list_fk'])) {
            $aWhere[] = ['list_fk', '=', $aFilter['list_fk'] ];
        }
        if (!is_null($aFilter['data_type_fk'])) {
            $aWhere[] = ['data_type_fk', '=', $aFilter['data_type_fk'] ];
        }


//        if (!is_null($aFilter['name'])) {
//            $aWhere[] = ['name', 'ilike', '%' . $aFilter['name'] . '%'];
//        }

        if (count($aWhere) > 0) {
            $columns = Column::orderBy($orderby, $sort)
                    ->orWhere($aWhere)
                    ->paginate($limit)
                    ->withQueryString(); //append the get parameters
        }
        else {
            $columns = Column::orderBy($orderby, $sort)->paginate($limit)->withQueryString();
        }
        
        //data for the filter-selects
        $lists = Selectlist::where('internal', false)->orderBy('name')->get();
        $lang = app()->getLocale();
        $data_types = Localization::getDataTypes($lang);
//        dd($data_types);
        
        return view('admin.column.list', compact('columns', 'aFilter', 'data_types', 'lists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lists = Selectlist::where('internal', false)->orderBy('name')->get();
        
        // Get current UI language
        $lang = app()->getLocale();
        
        // Get name attribute for current language
        $attribute = Attribute::where('name', 'name_'. $lang)->first();
        
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        
        // Get data types of columns with localized names
        $data_types = Localization::getDataTypes($lang);
        
        // Get IDs for data_types
        $data_type_ids['_list_'] = Value::where('value', '_list_')->first()->element_fk;
        $data_type_ids['_multi_list_'] = Value::where('value', '_multi_list_')->first()->element_fk;
        
        return view('admin.column.create', compact('lists', 'data_types', 'data_type_ids', 'translations', 'attribute'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'list' => [
                Rule::requiredIf(function () use ($request) {
                    if ($request->input('data_type') == Value::where('value', '_list_')->first()->element_fk) {
                        return true;
                    }
                    if ($request->input('data_type') == Value::where('value', '_multi_list_')->first()->element_fk) {
                        return true;
                    }
                    return false;
                }),
                'integer',
            ],
            'data_type' => 'required|integer',
            'translation' => 'required|integer',
            'description' => 'required|string',
            'new_translation' => 'exclude_unless:translation,-1|required|string',
            'lang' => 'required|integer',
        ]);
        
        $data = [
            'list_fk' => $this->getListIdFromFormRequest($request),
            'data_type_fk' => $request->input('data_type'),
            'translation_fk' => $request->input('translation'),
            'description' => $request->input('description'),
        ];
        
        // Store element and value for new translation
        if ($request->input('translation') == -1) {
            $element_data = [
                'parent_fk' => null,
                'list_fk' => Selectlist::where('name', '_translation_')->first()->list_id,
                'value_summary' => '',
            ];
            $element = Element::create($element_data);
            
            $value_data = [
                'element_fk' => $element->element_id,
                'attribute_fk' => $request->input('lang'),
                'value' => $request->input('new_translation'),
            ];
            Value::create($value_data);
            
            $data['translation_fk'] = $element->element_id;
        }
        
        Column::create($data);
        
        return Redirect::to('admin/column')
            ->with('success', __('columns.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function show(Column $column)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function edit(Column $column)
    {
        $lists = Selectlist::where('internal', false)->orderBy('name')->get();
        
        // Get current UI language
        $lang = app()->getLocale();
        
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');
        
        // Get data types of columns with localized names
        $data_types = Localization::getDataTypes($lang);
        
        // Get IDs for data_types
        $data_type_ids['_list_'] = Value::where('value', '_list_')->first()->element_fk;
        $data_type_ids['_multi_list_'] = Value::where('value', '_multi_list_')->first()->element_fk;
        
        return view('admin.column.edit', compact('column', 'lists', 'data_types', 'data_type_ids', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Column $column)
    {
        $request->validate([
            'list' => [
                Rule::requiredIf(function () use ($request) {
                    if ($request->input('data_type') == Value::where('value', '_list_')->first()->element_fk) {
                        return true;
                    }
                    if ($request->input('data_type') == Value::where('value', '_multi_list_')->first()->element_fk) {
                        return true;
                    }
                    return false;
                }),
                'integer',
            ],
            'data_type' => 'required|integer',
            'translation' => 'required|integer',
            'description' => 'required|string',
        ]);
        
        $column->list_fk = $this->getListIdFromFormRequest($request);
        $column->data_type_fk = $request->input('data_type');
        $column->translation_fk = $request->input('translation');
        $column->description = $request->input('description');
        $column->save();
        
        return Redirect::to('admin/column')
            ->with('success', __('columns.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function destroy(Column $column)
    {
        // Check for column mappings owning this column
        if ($column->column_mapping->count()) {
            return Redirect::to('admin/column')
                ->with('warning', __('columns.still_owned_by'));
        }
        
        $column->delete();
        
        return Redirect::to('admin/column')
            ->with('success', __('columns.deleted'));
    }

    /**
     * Get resource for AJAX autocompletion search field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request)
    {
        $results = Column::select('column_id', 'description')
            ->where('description', 'ILIKE', "%{$request->search}%")
            ->orderBy('description')
            ->limit(config('ui.autocomplete_results', 5))
            ->get();
        
        $response = array();
        foreach ($results as $result) {
            $response[] = array(
                "value" => $result->column_id,
                "label" => $result->description,
                "edit_url" => route('column.edit', $result->column_id),
            );
        }
        
        return response()->json($response);
    }

    /**
     * Get the ID of the selected list if appropriate data type was chosen, null otherwise.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function getListIdFromFormRequest(Request $request)
    {
        // Get the element_id of data_type '_list' for validating the user's input
        // TODO: check for list '_data_type' and maybe attribute
        
        if ($request->input('data_type') == Value::where('value', '_list_')->first()->element_fk) {
            return $request->input('list');
        }
        if ($request->input('data_type') == Value::where('value', '_multi_list_')->first()->element_fk) {
            return $request->input('list');
        }
        return null;
    }
}
