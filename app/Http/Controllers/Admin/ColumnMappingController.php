<?php

namespace App\Http\Controllers\Admin;

use App\ColumnMapping;
use App\Column;
use App\Detail;
use App\Item;
use App\Taxon;
use App\Selectlist;
use App\Value;
use App\Element;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Redirect;

class ColumnMappingController extends Controller
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
        $colmaps = ColumnMapping::orderByRaw('item_type_fk, column_order')->paginate(10);
        
        return view('admin.colmap.list', compact('colmaps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #$columns = Column::doesntHave('column_mapping')->orderBy('description')->get();
        $columns = Column::orderBy('description')->get();
        
        $lang = 'name_'. app()->getLocale();
        $column_groups = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_column_group_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        $taxa = Taxon::tree()->depthFirst()->get();
        
        return view('admin.colmap.create', compact('columns', 'column_groups', 'item_types', 'taxa'));
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
            'column_group' => 'required|integer',
            'item_type' => 'required|integer',
            'taxon' => 'nullable|integer',
            'config' => 'nullable|string',
        ]);
        
        $data = [
            'column_fk' => $request->input('column'),
            'column_group_fk' => $request->input('column_group'),
            'item_type_fk' => $request->input('item_type'),
            'taxon_fk' => $request->input('taxon'),
            'config' => $request->input('config'),
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
     * Show the form for mass mapping columns to item types, column groups and taxa.
     *
     * @param  int  $item_type
     * @return \Illuminate\Http\Response
     */
    public function map($item_type)
    {
        $lang = 'name_'. app()->getLocale();
        $column_groups = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_column_group_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        $taxa = Taxon::tree()->depthFirst()->get();
        
        $columns_mapped = Column::whereHas('column_mapping', function (Builder $query) use ($item_type) {
            $query->where('item_type_fk', $item_type);
        })
            ->join('column_mapping', 'column_id', '=', 'column_mapping.column_fk')
            ->orderBy('column_order')
            ->get();
        
        $columns_avail = Column::doesntHave('column_mapping')->orderBy('description')->get();
        
        return view('admin.colmap.map', compact(
            'item_type', 'column_groups', 'item_types', 'taxa', 'columns_mapped', 'columns_avail'
        ));
    }

    /**
     * Save the mass mappings and create missing details for all items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function map_store(Request $request)
    {
        $request->validate([
            'column_avail' => 'required|array|min:1',  // at least one column must be selected
            'column_avail.*' => 'required|integer',
            'column_group' => 'required|integer',
            'item_type' => 'required|integer',
            'taxon' => 'nullable|integer',
        ]);
        
        $mapcount = 0;
        $success_status_msg = '';
        
        // Create mapping for selected columns
        foreach($request->input('column_avail') as $key => $colnr) {
            $data = [
                'column_fk' => $colnr,
                'column_group_fk' => $request->input('column_group'),
                'item_type_fk' => $request->input('item_type'),
                'taxon_fk' => $request->input('taxon'),
            ];
            ColumnMapping::create($data);
            
            $mapcount++;
            
            // Create missing details for all items
            $count = 0;
            foreach(Item::where('item_type_fk', $data['item_type_fk'])->get() as $item) {
                Detail::firstOrCreate(['column_fk' => $data['column_fk'], 'item_fk' => $item->item_id]);
                $count++;
            }
            if($count) {
                $success_status_msg .= __('colmaps.details_added', ['count' => $count]) ." ";
            }
        }
        $success_status_msg .=  __('colmaps.created_num', ['count' => $mapcount]);
        
        return Redirect::to('admin/colmap/map/'.$request->item_type)
            ->with('success', $success_status_msg);
    }

    /**
     * Show the form for sorting columns for a given item type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $item_type
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request, $item_type)
    {
        // Redirect if selected using drop-down menu
        if(isset($request->item_type) && $request->item_type <> $item_type )
            return Redirect::to('admin/colmap/sort/'.intval($request->item_type));
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        $columns_mapped = Column::whereHas('column_mapping', function (Builder $query) use ($item_type) {
            $query->where('item_type_fk', $item_type);
        })
            ->join('column_mapping', 'column_id', '=', 'column_mapping.column_fk')
            ->orderBy('column_order')
            ->get();
        
        return view('admin.colmap.sort', compact('item_type', 'item_types', 'columns_mapped'));
    }

    /**
     * Save the sorting of columns for a given item type.
     * 
     * This is called via AJAX request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sort_store(Request $request)
    {
        if($request->has('ids')){
            $arr = explode(',', $request->input('ids'));
            
            foreach($arr as $sortOrder => $id){
                $colmap = ColumnMapping::firstWhere('colmap_id', $id);
                $colmap->column_order = $sortOrder;
                $colmap->save();
            }
            return ['success'=>true, 'message'=>'Updated'];
        }
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
        $columns = Column::orderBy('description')->get();
        #$columns = Column::doesntHave('column_mapping')->orderBy('description')->get();
        
        $lang = 'name_'. app()->getLocale();
        $column_groups = Value::whereHas('element', function ($query) {
            $query->where('list_fk', Selectlist::where('name', '_column_group_')->first()->list_id);
        })
        ->whereHas('attribute', function ($query) use ($lang) {
            $query->where('name', $lang);
        })
        ->orderBy('value')->get();
        
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        $taxa = Taxon::tree()->depthFirst()->get();
        
        return view('admin.colmap.edit', compact('colmap', 'columns', 'column_groups', 'item_types', 'taxa'));
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
            'column_group' => 'required|integer',
            'item_type' => 'required|integer',
            'taxon' => 'nullable|integer',
            'column_order' => 'required|integer',
            'config' => 'nullable|string',
        ]);
        
        $colmap->column_fk = $request->input('column');
        $colmap->column_group_fk = $request->input('column_group');
        $colmap->item_type_fk = $request->input('item_type');
        $colmap->taxon_fk = $request->input('taxon');
        $colmap->column_order = $request->input('column_order');
        $colmap->config = $request->input('config');
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
