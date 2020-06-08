<?php

namespace App\Http\Controllers\Admin;

use App\ColumnMapping;
use App\Column;
use App\Detail;
use App\Item;
use App\Taxon;
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
        
        $taxa = Taxon::tree()->get();
        
        return view('admin.colmap.create', compact('columns', 'item_types', 'taxa'));
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
            'taxon' => 'nullable|integer',
        ]);
        
        $data = [
            'column_fk' => $request->input('column'),
            'item_type_fk' => $request->input('item_type'),
            'taxon_fk' => $request->input('taxon'),
        ];
        ColumnMapping::create($data);
        
        $success_status_msg =  __('colmaps.created');
        
        // Create missing details for all items
        $count = 0;
        foreach(Item::where('item_type_fk', $data['item_type_fk'])->get() as $item) {
            Detail::firstOrCreate(['column_fk' => $data['column_fk'], 'item_fk' => $item->item_id]);
            $count++;
        }
        if($count) {
            $success_status_msg .= " ". __('colmaps.details_added', ['count' => $count]);
        }
        
        return Redirect::to('admin/colmap')
            ->with('success', $success_status_msg);
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
     * Danger! This should not be called by any user, not even by admins!
     * All Links in the backend have been removed. The URL route is still available though.
     *
     * @param  \App\ColumnMapping  $colmap
     * @return \Illuminate\Http\Response
     */
    public function edit(ColumnMapping $colmap)
    {
        $columns = Column::all();
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        $taxa = Taxon::tree()->get();
        
        return view('admin.colmap.edit', compact('colmap', 'columns', 'item_types', 'taxa'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Danger! This should not be called by any user, not even by admins!
     * All Links in the backend have been removed. The URL route is still available though.
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
            'taxon' => 'nullable|integer',
        ]);
        
        $colmap->column_fk = $request->input('column');
        $colmap->item_type_fk = $request->input('item_type');
        $colmap->taxon_fk = $request->input('taxon');
        $colmap->save();
        
        // TODO: Create missing details for all items, see function store()
        
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
