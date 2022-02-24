<?php

namespace App\Http\Controllers\Admin\Lists;

use App\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;
use Auth;

class AttributeController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
    
        // Use app\Policies\AttributePolicy for authorizing ressource controller
        $this->authorizeResource(Attribute::class, 'attribute');
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['attributes'] = Attribute::orderBy('name')->paginate(10);
        
        return view('admin.lists.attribute.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.lists.attribute.create');
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
        ]);
        
        Attribute::create($request->all());
        
        return Redirect::to('admin/lists/attribute')
            ->with('success', __('attributes.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute)
    {
        $data['attribute'] = $attribute;
        
        return view('admin.lists.attribute.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $attribute->name = $request->input('name');
        $attribute->save();
        
        return Redirect::to('admin/lists/attribute')
            ->with('success', __('attributes.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attribute  $attribute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        
        return Redirect::to('admin/lists/attribute')
            ->with('success', __('attributes.deleted'));
    }
}
