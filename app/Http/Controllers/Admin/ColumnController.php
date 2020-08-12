<?php

namespace App\Http\Controllers\Admin;

use App\Column;
use App\Selectlist;
use App\Element;
use App\Value;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $columns = Column::orderBy('column_id')->paginate(10);
        
        return view('admin.column.list', compact('columns'));
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
        $lang = 'name_'. app()->getLocale();
        
        // Get data types of columns with localized names
        $data_types = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_data_type_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        // Get with localized names of columns
        $translations = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_translation_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        return view('admin.column.create', compact('lists', 'data_types', 'translations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get the element_id of data_type '_list' for validating the user's input
        // TODO: check for list '_data_type' and maybe attribute
        $value = Value::where('value', '_list_')->first();
        
        $request->validate([
            'list' => 'required_if:data_type,'.$value->element_fk,
            'data_type' => 'required|integer',
            'translation' => 'required|integer',
            'description' => 'required|string',
        ]);
        
        $data = [
            'list_fk' => ($request->input('data_type') == $value->element_fk) ? $request->input('list') : null,
            'data_type_fk' => $request->input('data_type'),
            'translation_fk' => $request->input('translation'),
            'description' => $request->input('description'),
        ];
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
        $lang = 'name_'. app()->getLocale();
        
        // Get data types of columns with localized names
        $data_types = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_data_type_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        // Get with localized names of columns
        $translations = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_translation_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        return view('admin.column.edit', compact('column', 'lists', 'data_types', 'translations'));
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
        // Get the element_id of data_type '_list' for validating the user's input
        // TODO: check for list '_data_type' and maybe attribute
        $value = Value::where('value', '_list_')->first();
        
        $request->validate([
            'list' => 'required_if:data_type,'.$value->element_fk,
            'data_type' => 'required|integer',
            'translation' => 'required|integer',
            'description' => 'required|string',
        ]);
        
        $column->list_fk = ($request->input('data_type') == $value->element_fk) ? $request->input('list') : null;
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
        $column->delete();
        
        return Redirect::to('admin/column')
            ->with('success', __('columns.deleted'));
    }
}
