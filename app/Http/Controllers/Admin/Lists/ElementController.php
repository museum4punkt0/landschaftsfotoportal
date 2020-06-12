<?php

namespace App\Http\Controllers\Admin\Lists;

use App\Selectlist;
use App\Element;
use App\Value;
use App\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Redirect;
use Auth;

class ElementController extends Controller
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
        $constraint = function (Builder $query) use ($list_id) {
            $query->where('parent_fk', 0)->where('list_fk', $list_id);
        };
        $data['elements'] = Element::treeOf($constraint)->depthFirst()->get();
        $data['attributes'] = Attribute::all();
        
        return view('admin.lists.element.create', $data);
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
        
        return Redirect::to('admin/lists/list/'.$list_id)
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
        $list_id = $element->list_fk;
        $constraint = function (Builder $query) use ($list_id) {
            $query->where('parent_fk', 0)->where('list_fk', $list_id);
        };
        $data['elements'] = Element::treeOf($constraint)->depthFirst()->get();
        
        // Remove all descendants to avoid circular dependencies
        $data['elements'] = $data['elements']->diff($element->descendantsAndSelf()->get());
        
        return view('admin.lists.element.edit', $data);
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
        
        return Redirect::to('admin/lists/list/'.$element->list_fk)
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
        $success_status_msg =  __('elements.orphans_deleted');
        
        // Delete the element itself
        $element->delete();
        $success_status_msg .= " ". __('elements.deleted');
        
        // Fix hierarchy of elements if necessary
        if($element->list->hierarchical) {
            // Check for existing descendants
            foreach(Element::where('parent_fk', $element->element_id)->get() as $descendant) {
                // Fix parent ID on descendant element
                $descendant->parent_fk = $element->parent_fk;
                $descendant->save();
                $success_status_msg .= " ".
                    __('elements.hierarchy_fixed', ['id'=>$descendant->element_id]);
            }
        }
        
        return Redirect::to('admin/lists/list/'.$element->list_fk)
            ->with('success', $success_status_msg);
    }
}
