<?php

namespace App\Http\Controllers\Admin\Lists;

use App\Selectlist;
use App\Element;
use App\Value;
use App\Attribute;
use App\Column;
use App\ColumnMapping;
use App\Detail;
use App\Item;
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
        $this->middleware('verified');

        // Use app\Policies\ElementPolicy for authorizing ressource controller
        $this->authorizeResource(Element::class, 'element');
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
            $query->where('parent_fk', null)->where('list_fk', $list_id);
        };
        $data['elements'] = Element::treeOf($constraint)->depthFirst()->get();
        $data['attributes'] = Attribute::all();

        return view('admin.lists.element.create', $data);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $list_id  ID of the list owning this element
     * @return \Illuminate\Http\Response
     */
    public function createBatch($list_id)
    {
        $data['list'] = Selectlist::find($list_id);
        $data['attributes'] = Attribute::orderBy('attribute_id')->get();

        $aAttr = [];
        foreach ($data['attributes'] AS $att) {
            $aAttr[] = $att->name;
        }
        $data['example_value'] = implode('|', $aAttr);

        return view('admin.lists.element.create-batch', $data);
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
            'value' => 'required|string|max:4095',
            'attribute' => 'required|integer',
            'parent_fk' => 'nullable|integer',
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

        return Redirect::to('admin/lists/list/' . $list_id)
                        ->with('success', __('elements.created'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $list_id  ID of the list owning this element
     * @return \Illuminate\Http\Response
     */
    public function storeBatch(Request $request, $list_id)
    {
        $request->validate([
            'multivalues' => 'required|string',
        ]);

        $aValues = explode("\n", $request->input('multivalues'));

        $aAttributes = Attribute::orderBy('attribute_id')->get();

        foreach ($aValues as $sValue) {
            $aValuesByAttributes = explode('|', $sValue);
            $j = 0;
            $element_data = [
                'parent_fk' => null,
                'list_fk' => $list_id,
                'value_summary' => '',
            ];
            $element = Element::create($element_data);

            foreach ($aAttributes as $sAttribute) {
                if (isset($aValuesByAttributes[$j]) && !empty(trim($aValuesByAttributes[$j]))) {
                    $value_data = [
                        'element_fk' => $element->element_id,
                        'attribute_fk' => $sAttribute->attribute_id,
                        'value' => trim($aValuesByAttributes[$j]),
                    ];
                    Value::create($value_data);
                }
                $j++;
            }
        }
      
        return Redirect::to('admin/lists/list/' . $request->input('list_id'))
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
        $data['element'] = $element;
        $data['list'] = $element->list;

        return view('admin.lists.element.show', $data);
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
            $query->where('parent_fk', null)->where('list_fk', $list_id);
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
        // This is only available for hierarchical lists
        $request->validate([
            'parent_fk' => 'nullable|integer',
        ]);

        $element->parent_fk = $request->input('parent_fk');
        $element->save();
        
        return Redirect::to('admin/lists/list/' . $element->list_fk)
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
        // Some checks before deleting
        $response = $this->checkBeforeDestroy($element);
        if ($response) {
            return $response;
        }
        #dd('stopped deleting element');

        // Delete all values owned by this element
        Value::where('element_fk', $element->element_id)->delete();
        $success_status_msg = __('elements.orphans_deleted');

        // Delete the element itself
        $element->delete();
        $success_status_msg .= " " . __('elements.deleted');

        // Fix hierarchy of elements if necessary
        if ($element->list->hierarchical) {
            // Check for existing descendants
            foreach (Element::where('parent_fk', $element->element_id)->get() as $descendant) {
                // Fix parent ID on descendant element
                $descendant->parent_fk = $element->parent_fk;
                $descendant->save();
                $success_status_msg .= " " .
                        __('elements.hierarchy_fixed', ['id' => $descendant->element_id]);
            }
        }

        return Redirect::to('admin/lists/list/' . $element->list_fk)
                        ->with('success', $success_status_msg);
    }

    /**
     * Check relations before removing the specified resource from storage.
     *
     * @param  \App\Element  $element
     * @return \Illuminate\Http\Response | false
     */
    private function checkBeforeDestroy(Element $element)
    {
        // What list belongs the element to
        switch ($element->list->name) {
            // Prevent deleting data types
            case '_data_type_':
                return back()->with('warning', __('elements.cannot_delete_data_type'));
            // Do not delete item types used by ...
            case '_item_type_':
                if (ColumnMapping::where('item_type_fk', $element->element_id)->count()) {
                    return back()->with('warning', __('elements.item_type_still_owned_by_cm'));
                }
                if (Item::where('item_type_fk', $element->element_id)->count()) {
                    return back()->with('warning', __('elements.item_type_still_owned_by_it'));
                }
                break;
            // Do not delete column groups used by column mappings
            case '_column_group_':
                if (ColumnMapping::where('column_group_fk', $element->element_id)->count()) {
                    return back()->with('warning', __('elements.column_group_still_owned_by'));
                }
                break;
            // Do not delete translations used by columns
            case '_translation_':
                if (Column::where('translation_fk', $element->element_id)->count()) {
                    return back()->with('warning', __('elements.translation_still_owned_by'));
                }
                break;
            // This seems to be a "normal" list, so check for usage by details
            default:
                if (Detail::where('element_fk', $element->element_id)->count()) {
                    return back()->with('warning', __('elements.still_owned_by_detail'));
                }
                // Many-to-many-relation for multi-select-lists
                if ($element->details->count()) {
                    return back()->with('warning', __('elements.still_owned_by_detail'));
                }
        }
        return false;
    }

    /**
     * Get resource for AJAX autocompletion search field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $list_id  ID of the list to search within
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request, $list_id)
    {
        $term = $request->search;
        $results = Element::with('values')
                ->where('list_fk', $list_id)
                ->whereHas('values', function ($query) use ($term) {
                    $query->where('value', 'ILIKE', "%{$term}%");
                })
                ->limit(config('ui.autocomplete_results', 5))
                ->get();

        $response = array();
        foreach ($results as $result) {
            foreach ($result->values as $value) {
                $response[] = array(
                    "value" => $value->value_id,
                    "label" => $value->value,
                    "edit_url" => route('value.edit', $value->value_id),
                );
            }
        }

        return response()->json($response);
    }

}
