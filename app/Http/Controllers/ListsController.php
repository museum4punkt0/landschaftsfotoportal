<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// added
use Auth;
use Redirect;
use App\Selectlist;

class ListsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['lists'] = Selectlist::orderBy('name')->paginate(10);
        
        return view('list.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('list.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'hierarchical' => 'boolean',
        ]);
        
        Selectlist::create($request->all());
    
        return Redirect::to('list')->with('success','Great! Product created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $where = array('list_id' => $id);
        $data['list'] = Selectlist::where($where)->first();
 
        return view('list.edit', $data);
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
        //
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'hierarchical' => 'boolean',
        ]);
         
        $update = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'hierarchical' => $request->boolean('hierarchical'),
            'attribute_order' => $request->input('attribute_order'),
        ];
        Selectlist::where('list_id', $id)->update($update);
        
        return Redirect::to('list')->with('success','Great! Product updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Selectlist::where('list_id', $id)->delete();
        
        return Redirect::to('list')->with('success','Product deleted successfully');
    }
}
