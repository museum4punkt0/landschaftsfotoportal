<?php

namespace App\Http\Controllers\Admin;

use App\ColumnMapping;
use App\Column;
use App\Selectlist;
use App\Element;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class ColumnMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $colmaps = ColumnMapping::orderBy('colmap_id')->paginate(10);
        
        return view('admin.colmap.list', compact('colmaps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $columns = Column::all();
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        return view('admin.colmap.create', compact('columns', 'item_types'));
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
            'column' => 'required|integer',
            'item_type' => 'required|integer',
        ]);
        
        $data = [
            'column_fk' => $request->input('column'),
            'item_type_fk' => $request->input('item_type'),
        ];
        ColumnMapping::create($data);
        
        return Redirect::to('admin/colmap')
            ->with('success', __('colmaps.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ColumnMapping  $colmap
     * @return \Illuminate\Http\Response
     */
    public function show(ColumnMapping $colmap)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ColumnMapping  $colmap
     * @return \Illuminate\Http\Response
     */
    public function edit(ColumnMapping $colmap)
    {
        $columns = Column::all();
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        return view('admin.colmap.edit', compact('colmap', 'columns', 'item_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ColumnMapping  $colmap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColumnMapping $colmap)
    {
        $request->validate([
            'column' => 'required|integer',
            'item_type' => 'required|integer',
        ]);
        
        $colmap->column_fk = $request->input('column');
        $colmap->item_type_fk = $request->input('item_type');
        $colmap->save();
        
        return Redirect::to('admin/colmap')
            ->with('success', __('colmaps.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ColumnMapping  $colmap
     * @return \Illuminate\Http\Response
     */
    public function destroy(ColumnMapping $colmap)
    {
        $colmap->delete();
        
        return Redirect::to('admin/colmap')
            ->with('success', __('colmaps.deleted'));
    }
}
