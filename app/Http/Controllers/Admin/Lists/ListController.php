<?php

namespace App\Http\Controllers\Admin\Lists;

use App\Selectlist;
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
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource without flag 'internal' being set.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['lists'] = Selectlist::where('internal', false)->orderBy('name')->paginate(10);
        
        return view('admin.lists.list.list',$data);
    }

    /**
     * Display a listing of the resource with flag 'internal' being set.
     *
     * @return \Illuminate\Http\Response
     */
    public function internal()
    {
        $data['lists'] = Selectlist::where('internal', true)->orderBy('name')->paginate(10);
        
        return view('admin.lists.list.list',$data);
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
            'name' => 'required',
            'description' => 'required',
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
     * Display the specified resource as tree.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tree($id)
    {
        $data['list'] = Selectlist::find($id);
        
        $constraint = function (Builder $query) use ($id) {
            $query->where('parent_fk', null)->where('list_fk', $id);
        };

        $data['elements'] = Element::treeOf($constraint)->depthFirst()->paginate(10);
        
        return view('admin.lists.list.tree', $data);
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
            'name' => 'required',
            'description' => 'required',
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Selectlist::where('list_id', $id)->delete();
        
        // TODO: delete orphaned elements and values
        
        return Redirect::to('admin/lists/list')->with('success', __('lists.deleted'));
    }
}
