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
use App\Utils\Localization;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Redirect;
use Debugbar;

class ColumnMappingController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        // Use app\Policies\ColumnMappingPolicy for authorizing ressource controller
        $this->authorizeResource(ColumnMapping::class, 'colmap');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aFilter = [
            'colmap_id' => $request->input('colmap_id'),
            'description' => $request->input('description'),
            'element' => $request->input('element_id'),
            'item_type_fk' => $request->input('item_type_fk'),
            'column_group_fk' => $request->input('column_group_fk'),
            'taxon_fk' => $request->input('taxon_fk'),
        ];
        
        $orderby = $request->input('orderby', 'colmap_id');
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');
        
        $aWhere = [];
        if (!is_null($aFilter['colmap_id'])) {
            $aWhere[] = ['colmap_id', '=', $aFilter['colmap_id']];
        }
        if (!is_null($aFilter['description'])) {
            // just a dummy, should always be true
            $aWhere[] = ['colmap_id', '>', 0];
        }
        if (!is_null($aFilter['item_type_fk'])) {
            $aWhere[] = ['item_type_fk', '=', $aFilter['item_type_fk']];
        }
        if (!is_null($aFilter['taxon_fk'])) {
            $aWhere[] = ['taxon_fk', '=', $aFilter['taxon_fk']];
        }
        if (!is_null($aFilter['column_group_fk'])) {
            $aWhere[] = ['column_group_fk', '=', $aFilter['column_group_fk']];
        }

        if (count($aWhere) > 0) {
            $colmaps = ColumnMapping::orderBy($orderby, $sort)
                    ->when(!is_null($aFilter['description']), function ($query) use ($aFilter) {
                        return $query->whereHas('column', function ($query) use ($aFilter) {
                            $query->where('description', 'ILIKE', '%'.$aFilter['description'].'%');
                        });
                    })
                    ->where($aWhere)
                    ->paginate($limit)
                    ->withQueryString(); //append the get parameters
        }
        else {
              $colmaps = ColumnMapping::orderBy($orderby, $sort)->paginate($limit);
        }
        
        // Get current UI language
        $lang = app()->getLocale();
        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);             
        // Get column groups with localized names
        $column_groups = Localization::getColumnGroups($lang);
        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');

        $taxa = Taxon::has('column_mapping')->orderBy('full_name')->get();
        
        return view('admin.colmap.list', compact('colmaps', 'aFilter', 'item_types', 'column_groups', 'translations', 'taxa'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #$columns = Column::doesntHave('column_mapping')->orderBy('description')->get();
        $columns = Column::with(['translation.values'])->orderBy('description')->get();
        
        // Get current UI language
        $lang = app()->getLocale();

        // Get column groups with localized names
        $column_groups = Localization::getColumnGroups($lang);

        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);             
        
        // Check for existing item_type, otherwise redirect back with warning message
        if ($item_types->isEmpty()) {
            return redirect()->route('list.internal')
                ->with('warning', __('colmaps.no_item_type'));
        }
        
        return view('admin.colmap.create', compact('columns', 'column_groups', 'item_types'));
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
            'public' => 'required|integer',
            'api_attribute' => 'nullable|string|max:255',
            'config' => 'nullable|string|max:4095',
        ]);
        
        $data = [
            'column_fk' => $request->input('column'),
            'column_group_fk' => $request->input('column_group'),
            'item_type_fk' => $request->input('item_type'),
            'taxon_fk' => $request->input('taxon'),
            'public' => $request->input('public'),
            'api_attribute' => $request->input('api_attribute'),
            'config' => $request->input('config'),
        ];
        $colmap = ColumnMapping::create($data);
        
        $success_status_msg =  __('colmaps.created');

        // Sort this mapped column to the end
        if ($request->input('sort_end')) {
            $colmap->setHighestColumnOrder();
        }
        
        // Create missing details for all items
        $count = 0;
        foreach (Item::where('item_type_fk', $data['item_type_fk'])->get() as $item) {
            Detail::firstOrCreate(['column_fk' => $data['column_fk'], 'item_fk' => $item->item_id]);
            $count++;
        }
        if ($count) {
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function map(Request  $request)
    {
        $this->authorize('map', ColumnMapping::class);

        $item_type = $request->item_type;

        // Get current UI language
        $lang = app()->getLocale();

        // Get column groups with localized names
        $column_groups = Localization::getColumnGroups($lang);

        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);             

        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');

        // Check for existing item_type, otherwise redirect back with warning message
        if ($item_types->isEmpty()) {
            return redirect()->route('list.internal')
                ->with('warning', __('colmaps.no_item_type'));
        }
        
        // Use first item type found in database if ID is invalid
        if (!$item_types->firstWhere('element_fk', $item_type)) {
            $item_type = $item_types->first()->element_fk;
        }
        
        // Get all columns mapped to the given item type
        $columns_mapped = Column::whereHas('column_mapping', function (Builder $query) use ($item_type) {
            $query->where('item_type_fk', $item_type);
        })
            ->join('column_mapping', 'column_id', '=', 'column_mapping.column_fk')
            ->orderBy('column_order')
            ->get();
        
        $columns_avail = Column::doesntHave('column_mapping')->orderBy('description')->get();
        
        return view('admin.colmap.map', compact(
            'item_type',
            'column_groups',
            'item_types',
            'translations',
            'columns_mapped',
            'columns_avail'
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
        $this->authorize('map', ColumnMapping::class);

        $request->validate([
            'column_avail' => 'required|array|min:1',  // at least one column must be selected
            'column_avail.*' => 'required|integer',
            'column_group' => 'required|integer',
            'item_type' => 'required|integer',
            'taxon' => 'nullable|integer',
            'public' => 'required|integer',
            'config' => 'nullable|string',
        ]);
        
        $mapcount = 0;
        $success_status_msg = '';
        
        // Create mapping for selected columns
        foreach ($request->input('column_avail') as $key => $colnr) {
            $data = [
                'column_fk' => $colnr,
                'column_group_fk' => $request->input('column_group'),
                'item_type_fk' => $request->input('item_type'),
                'taxon_fk' => $request->input('taxon'),
                'public' => $request->input('public'),
                'config' => $request->input('config'),
            ];
            $colmap = ColumnMapping::create($data);
            
            // Sort this mapped column to the end
            if ($request->input('sort_end')) {
                $colmap->setHighestColumnOrder();
            }

            $mapcount++;
            
            // Create missing details for all items
            $count = 0;
            foreach (Item::where('item_type_fk', $data['item_type_fk'])->get() as $item) {
                Detail::firstOrCreate(['column_fk' => $data['column_fk'], 'item_fk' => $item->item_id]);
                $count++;
            }
            if ($count) {
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
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $this->authorize('sort', ColumnMapping::class);

        $item_type = $request->item_type;
        
        // Get current UI language
        $lang = app()->getLocale();

        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);             

        // Get localized names of columns
        $translations = Localization::getTranslations($lang, 'name');

        // Check for existing item_type, otherwise redirect back with warning message
        if ($item_types->isEmpty()) {
            return Redirect::to('admin/colmap')
                ->with('warning', __('colmaps.no_item_type'));
        }
        
        // Use first item type found in database if ID is invalid
        if (!$item_types->firstWhere('element_fk', $item_type)) {
            $item_type = $item_types->first()->element_fk;
        }
        
        // Get all columns mapped to the given item type
        $columns_mapped = Column::whereHas('column_mapping', function (Builder $query) use ($item_type) {
            $query->where('item_type_fk', $item_type);
        })
            ->join('column_mapping', 'column_id', '=', 'column_mapping.column_fk')
            ->orderBy('column_order')
            ->get();
        
        return view('admin.colmap.sort', compact('item_type', 'item_types', 'translations', 'columns_mapped'));
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
        $this->authorize('sort', ColumnMapping::class);

        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));
            
            foreach ($arr as $order => $id) {
                $colmap = ColumnMapping::firstWhere('colmap_id', $id);
                $colmap->column_order = $order;
                $colmap->save();
            }
            return response()->json(['success' => __('common.update_success')]);
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
        $columns = Column::with(['translation.values'])->orderBy('description')->get();
        #$columns = Column::doesntHave('column_mapping')->orderBy('description')->get();
        
        // Get current UI language
        $lang = app()->getLocale();

        // Get column groups with localized names
        $column_groups = Localization::getColumnGroups($lang);

        // Get item types with localized names
        $item_types = Localization::getItemTypes($lang);             

        return view('admin.colmap.edit', compact('colmap', 'columns', 'column_groups', 'item_types'));
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
            //'column' => 'required|integer',
            'column_group' => 'required|integer',
            //'item_type' => 'required|integer',
            'taxon' => 'nullable|integer',
            'column_order' => 'required|integer',
            'public' => 'required|integer',
            'api_attribute' => 'nullable|string|max:255',
            'config' => 'nullable|string|max:4095',
        ]);
        
        //$colmap->column_fk = $request->input('column');
        $colmap->column_group_fk = $request->input('column_group');
        //$colmap->item_type_fk = $request->input('item_type');
        $colmap->taxon_fk = $request->input('taxon');
        $colmap->column_order = $request->input('column_order');
        $colmap->public = $request->input('public');
        $colmap->api_attribute = $request->input('api_attribute');
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

    /**
     * Toggle public visibility using AJAX.
     *
     * @param  \App\ColumnMapping  $colmap
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publish(ColumnMapping $colmap, Request $request)
    {
        $this->authorize('publish', $colmap);

        $public = $colmap->public;
        $colmap->public = $public ? 0 : 1;
        $colmap->save();

        $response = array(
            "success" => $colmap->public ? __('colmaps.published') : __('colmaps.unpublished'),
            "public" => $colmap->public,
        );

        return response()->json($response);
    }

    /**
     * Get resource for AJAX autocompletion search field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request)
    {
        $term = $request->search;
        $results = ColumnMapping::with('column')
            ->whereHas('column', function ($query) use ($term) {
                $query->where('description', 'ILIKE', "%{$term}%");
            })
            ->limit(config('ui.autocomplete_results', 5))
            ->get();
        
        $response = array();
        foreach ($results as $result) {
            $tax_str = $result->taxon ? '; ' . $result->taxon->full_name : '';
            $response[] = array(
                "value" => $result->colmap_id,
                "label" => $result->column->description . $tax_str,
                "edit_url" => route('colmap.edit', $result->colmap_id),
            );
        }
        
        return response()->json($response);
    }
}
