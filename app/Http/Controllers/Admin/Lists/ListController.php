<?php

namespace App\Http\Controllers\Admin\Lists;

use App\Selectlist;
use App\Attribute;
use App\Column;
use App\Element;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Redirect;
use Auth;

class ListController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');

        // Use app\Policies\ListPolicy for authorizing ressource controller
        $this->authorizeResource(Selectlist::class, 'list');
    }

    /**
     * Display a listing of the resource without flag 'internal' being set.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aFilter = [
            'list_id' => $request->input('list_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'hierarchical' => $request->input('hierarchical'),
        ];
        
        $orderby = $request->input('orderby', 'name');
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'desc');
        
        $aWhere = [];
        if (!is_null($aFilter['list_id'])) {
            $aWhere[] = ['list_id', '=', $aFilter['list_id']];
        }
        if (!is_null($aFilter['name'])) {
            $aWhere[] = ['name', 'ilike', '%' . $aFilter['name'] . '%'];
        }
        if (!is_null($aFilter['description'])) {
            $aWhere[] = ['description', 'ilike', '%' . $aFilter['description'] . '%'];
        }
        if (!is_null($aFilter['hierarchical'])) {
            $aWhere[] = ['hierarchical', '=', $aFilter['hierarchical']];
        }

        if (count($aWhere) > 0) {
            $lists = Selectlist::orderBy($orderby, $sort)
                    ->orWhere($aWhere)
                    ->where('internal', false)
                    ->paginate($limit)
                    ->withQueryString(); //append the get parameters
        }
        else {
              $lists = Selectlist::where('internal', false)->orderBy($orderby, $sort)->paginate($limit)->withQueryString();
        }
       
        return view('admin.lists.list.list', compact('lists', 'aFilter'));
    }

    /**
     * Display a listing of the resource with flag 'internal' being set.
     *
     * @return \Illuminate\Http\Response
     */
    public function internal(Request $request)
    {
        $this->authorize('internal', Selectlist::class);

        $data['aFilter'] = [
            'list_id' => $request->input('list_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'hierarchical' => $request->input('hierarchical'),
        ];
        
        $orderby = $request->input('sort', 'name');
        $limit = $request->input('limit', 10);
        
        $aWhere = [];
        if (!is_null($data['aFilter']['list_id'])) {
            $aWhere[] = ['list_id', '=', $data['aFilter']['list_id']];
        }
        if (!is_null($data['aFilter']['name'])) {
            $aWhere[] = ['name', 'ilike', '%' . $data['aFilter']['name'] . '%'];
        }
        if (!is_null($data['aFilter']['description'])) {
            $aWhere[] = ['description', 'ilike', '%' . $data['aFilter']['description'] . '%'];
        }
        if (!is_null($data['aFilter']['hierarchical'])) {
            $aWhere[] = ['hierarchical', '=', $data['aFilter']['hierarchical']];
        }

        if (count($aWhere) > 0) {
            $data['lists'] = Selectlist::orderBy($orderby, 'desc')
                    ->orWhere($aWhere)
                    ->where('internal', true)
                    ->paginate($limit)
                    ->withQueryString(); //append the get parameters
        }
        else {
              $data['lists'] = Selectlist::where('internal', true)->orderBy('name')->paginate($limit)->withQueryString();
        }
        
        return view('admin.lists.list.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.lists.list.create');
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
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'hierarchical' => 'boolean',
            'internal' => 'boolean',
        ]);
        
        Selectlist::create($request->all());
        
        return Redirect::to('admin/lists/list')->with('success', __('lists.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['list'] = Selectlist::find($id);
        #$data['elements'] = Selectlist::find($id)->elements;
        $constraint = function (Builder $query) use ($id) {
            $query->where('parent_fk', null)->where('list_fk', $id);
        };

        $data['elements'] = Element::treeOf($constraint)->depthFirst()->paginate(10);
        
        return view('admin.lists.list.show', $data);
    }

    /**
     * Display the list of item types.
     *
     * @return \Illuminate\Http\Response
     */
    public function showItemTypes()
    {
        $list = Selectlist::where('name', '_item_type_')->first();

        $this->authorize('view', $list);

        $elements = $list->elements()->paginate(10);

        return view('admin.lists.list.show', compact('list', 'elements'));
    }

    /**
     * Display the list of column groups.
     *
     * @return \Illuminate\Http\Response
     */
    public function showColumnGroups()
    {
        $list = Selectlist::where('name', '_column_group_')->first();

        $this->authorize('view', $list);

        $elements = $list->elements()->paginate(10);

        return view('admin.lists.list.show', compact('list', 'elements'));
    }

    /**
     * Display the list of translations (names/descriptions/placeholders of columns).
     *
     * @return \Illuminate\Http\Response
     */
    public function showTranslations()
    {
        $list = Selectlist::where('name', '_translation_')->first();

        $this->authorize('view', $list);

        $elements = $list->elements()->paginate(10);

        return view('admin.lists.list.show', compact('list', 'elements'));
    }

    /**
     * Display the specified resource as tree.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tree($id)
    {
        $this->authorize('tree', Selectlist::find($id));

        $data['list'] = Selectlist::find($id);
        
        $constraint = function (Builder $query) use ($id) {
            $query->where('parent_fk', null)->where('list_fk', $id);
        };

        $data['elements'] = Element::treeOf($constraint)->depthFirst()->paginate(10);
        
        return view('admin.lists.list.tree', $data);
    }

    /**
     * Export the specified resource as CSV file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        $this->authorize('export', Selectlist::find($id));

        $data['list'] = Selectlist::find($id);
        $data['attributes'] = Attribute::orderBy('attribute_id')->get();
        
        $constraint = function (Builder $query) use ($id) {
            $query->where('parent_fk', null)->where('list_fk', $id);
        };
        
        $data['elements'] = Element::treeOf($constraint)->depthFirst()->get();
        
        $attributeMap = null;
        
        // Create heading (1st line) of CSV file
        $csvContent = "id;parent_id;";
        foreach ($data['attributes'] as $attribute) {
            $attributeMap[$attribute->attribute_id] = '';
            $csvContent .= $attribute->name .";";
        }
        $csvContent .= "\n";
        
        // Create all the content lines
        foreach ($data['elements'] as $element) {
            $values = $attributeMap;
            
            foreach ($element->values as $value) {
                $values[$value->attribute_fk] = $value->value;
            }
            
            $csvContent .= $element->element_id .";". $element->parent_fk .";";
            $csvContent .= implode(';', $values);
            $csvContent .= "\n";
        }
        
        // Set file name for download
        $exportFileName = sprintf('list_%d_%s_%s.csv', $id, $data['list']->name, date('Y-m-d'));
        
        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $exportFileName, ["Content-Type" => "text/csv"]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['list'] = Selectlist::find($id);
        
        return view('admin.lists.list.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'hierarchical' => 'boolean',
            'internal' => 'boolean',
        ]);
         
        $update = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'hierarchical' => $request->boolean('hierarchical'),
            'internal' => $request->boolean('internal'),
            'attribute_order' => $request->input('attribute_order'),
        ];
        Selectlist::where('list_id', $id)->update($update);
        
        return Redirect::to('admin/lists/list')->with('success', __('lists.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Selectlist  $list
     * @return \Illuminate\Http\Response
     */
    public function destroy(Selectlist $list)
    {
        // Prevent deleting internal lists
        if ($list->internal) {
            return back()->with('warning', __('lists.cannot_delete_internal'));
        }
        else {
            // Check for columns owning this list
            if (Column::where('list_fk', $list->list_id)->count()) {
                return back()->with('warning', __('lists.still_owned_by'));
            }
        }

        // Delete orphaned elements and values
        foreach ($list->elements as $element) {
            $element->values()->delete();
        }
        $list->elements()->delete();
        // Delete list itself
        $list->delete();

        return back()->with('success', __('lists.deleted'));
    }
}
