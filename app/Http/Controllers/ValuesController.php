<?php

namespace App\Http\Controllers;

use App\Element;
use App\Value;
use App\Attribute;
use Illuminate\Http\Request;
use Redirect;
use Auth;

class ValuesController extends Controller
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
     * @param  int  $element_id  ID of the element owning this value
     * @return \Illuminate\Http\Response
     */
    public function create($element_id)
    {
        $data['element'] = Element::find($element_id);
        $data['attributes'] = Attribute::all();
        
        return view('value.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $element_id  ID of the element owning this value
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $element_id)
    {
        $request->validate([
            'value' => 'required',
            'attribute' => 'required',
        ]);
        
        $value_data = [
            'element_fk' => $element_id,
            'attribute_fk' => $request->input('attribute'),
            'value' => $request->input('value'),
        ];
        Value::create($value_data);
        
        $element = Element::find($element_id);
        
        return Redirect::to('list/'.$element->list_fk)
            ->with('success', __('values.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Value  $value
     * @return \Illuminate\Http\Response
     */
    public function show(Value $value)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Value  $value
     * @return \Illuminate\Http\Response
     */
    public function edit(Value $value)
    {
        $data['value'] = $value;
        $data['attributes'] = Attribute::all();
        
        return view('value.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Value  $value
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Value $value)
    {
        $request->validate([
            'value' => 'required',
            'attribute' => 'required',
        ]);
         
        $value->value = $request->input('value');
        $value->attribute_fk = $request->input('attribute');
        
        $value->save();
        
        return Redirect::to('list/'.$value->element->list_fk)
            ->with('success', __('values.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Value  $value
     * @return \Illuminate\Http\Response
     */
    public function destroy(Value $value)
    {
        $value->delete();
        $success_status_msg =  __('values.deleted');
        
        // Check for orphaned elements and delete them
        if(!Value::where('element_fk', $value->element_fk)->count()) {
            $value->element->delete();
            $success_status_msg .= " ". __('elements.deleted');
        
            // Fix hierarchy of elements if necessary
            if($value->element->list->hierarchical) {
                // Check for existing descendants
                foreach(Element::where('parent_fk', $value->element_fk)->get() as $element) {
                    // Fix parent ID on descendant element
                    $element->parent_fk = $value->element->parent_fk;
                    $element->save();
                    $success_status_msg .= " ".
                        __('elements.hierarchy_fixed', ['id'=>$element->element_id]);
                }
            }
        }
        
        return Redirect::to('list/'.$value->element->list_fk)
            ->with('success', $success_status_msg);
    }
}
