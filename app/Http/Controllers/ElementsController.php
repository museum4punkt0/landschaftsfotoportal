<?php

namespace App\Http\Controllers;

use App\Selectlist;
use App\Element;
use App\Value;
use App\Attribute;
use Illuminate\Http\Request;
use Redirect;
use Auth;

class ElementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $list_id  ID of the list owning this element
     * @return \Illuminate\Http\Response
     */
    public function create($list_id)
    {
        $data['list'] = Selectlist::find($list_id);
        $data['elements'] = Element::where('list_fk', $list_id)->get();
        $data['attributes'] = Attribute::all();
        
        return view('element.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $list_id  ID of the list owning this element
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $list_id)
    {
        $request->validate([
            'value' => 'required',
            'attribute' => 'required',
            #'parent' => 'required',
        ]);
        
        $element_data = [
            'parent_fk' => $request->input('parent_fk'),
            'list_fk' => $list_id,
            'value_summary' => '',
        ];
        $element = Element::create($element_data);
        
        $value_data = [
            'element_fk' => $element->element_id,
            'attribute_fk' => $request->input('attribute'),
            'value' => $request->input('value'),
        ];
        Value::create($value_data);
        
        return Redirect::to('list/'.$list_id)
            ->with('success', __('elements.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Element  $element
     * @return \Illuminate\Http\Response
     */
    public function show(Element $element)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Element  $element
     * @return \Illuminate\Http\Response
     */
    public function edit(Element $element)
    {
        $data['element'] = $element;
        $data['elements'] = Element::where('list_fk', $element->list_fk)->get();
        
        return view('element.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Element $element)
    {
        $request->validate([
            'parent_fk' => 'required',
        ]);
         
        $element->parent_fk = $request->input('parent_fk');
        $element->save();
        
        return Redirect::to('list/'.$element->list_fk)
            ->with('success', __('elements.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(Element $element)
    {
        // Delete all values owned by this element
        Value::where('element_fk', $element->element_id)->delete();
        
        // TODO fix hierarchy
        
        // Delete the element itself
        $element->delete();
        
        return Redirect::to('list/'.$element->list_fk)
            ->with('success', __('elements.deleted'));
    }
}
