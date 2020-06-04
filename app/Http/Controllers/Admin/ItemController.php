<?php

namespace App\Http\Controllers\Admin;

use App\Item;
use App\Detail;
use App\Column;
use App\ColumnMapping;
use App\Selectlist;
use App\Element;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Redirect;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::orderBy('item_id')->paginate(10);
        
        return view('admin.item.list', compact('items'));
    }

    /**
     * Show the form to select the type of the new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function new()
    {
        $it_list = Selectlist::where('name', '_item_type_')->first();
        $item_types = Element::where('list_fk', $it_list->list_id)->get();
        
        return view('admin.item.new', compact('item_types'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $colmap = ColumnMapping::where('item_type_fk', $request->item_type)->get();
        
        $lists = null;
        // Load all list elements of lists used by this item's columns
        foreach($colmap as $cm) {
            $list_id = $cm->column->list_fk;
            if($list_id) {
                $constraint = function (Builder $query) use ($list_id) {
                    $query->where('parent_fk', 0)->where('list_fk', $list_id);
                };
                $lists[$list_id] = Element::treeOf($constraint)->depthFirst()->get();
            }
        }
        
        $dt_list = Selectlist::where('name', '_data_type_')->first();
        $data_types = Element::where('list_fk', $dt_list->list_id)->with(['values', 'attributes'])->get();
        
        $l10n_list = Selectlist::where('name', '_translation_')->first();
        $translations = Element::where('list_fk', $l10n_list->list_id)->get();
        
        // Save item_type ID to session
        $request->session()->put('item_type', $request->item_type);
        
        return view('admin.item.create', compact('colmap', 'lists', 'data_types', 'translations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation_rules['fields'] = 'required|array';
        foreach ($request->input('fields') as $column_id => $value) {
            $validation_rules['fields.'.$column_id] = 'required|'. Column::find($column_id)->getValidationRule();
        }
        // Validate uploaded files
        foreach ($request->file('fields') as $column_id => $value) {
            $validation_rules['fields.'.$column_id] = 'required|'. Column::find($column_id)->getValidationRule();
        }
        
        $request->validate($validation_rules);
        
        // Get item_type ID from session
        $item_type = $request->session()->get('item_type');
        
        // Save new item to database
        $item_data = [
            'item_type_fk' => $item_type,
        ];
        $item = Item::create($item_data);
        
        // Save the details for all columns that belong to the item
        foreach ($request->input('fields') as $column_id => $value) {
            $data_type = Column::find($column_id)->getDataType();
            
            $detail_data = [
                'item_fk' => $item->item_id,
                'column_fk' => $column_id,
            ];
            switch ($data_type) {
                case '_list_':
                    $detail_data['element_fk'] = intval($value);
                    break;
                case '_integer_':
                    $detail_data['value_int'] = intval($value);
                    break;
                case '_float_':
                    $detail_data['value_float'] = floatval($value);
                    break;
                case '_date_':
                    $detail_data['value_date'] = $value;
                    break;
                case '_string_':
                case '_url_':
                    $detail_data['value_string'] = $value;
                    break;
            }
            Detail::create($detail_data);
        }
        foreach ($request->file('fields') as $column_id => $value) {
            $data_type = Column::find($column_id)->getDataType();
            
            $detail_data = [
                'item_fk' => $item->item_id,
                'column_fk' => $column_id,
            ];
            
            $file = $request->file('fields.'.$column_id);
            switch ($data_type) {
                case '_image_':
                    if($file->isValid()) {
                        $path = 'public/images/';
                        $name =  $column_id ."_". date('YmdHis') .".". $file->extension();
                        $file->storeAs($path, $name);
                        $detail_data['value_string']  = $name;
                    }
                    break;
            }
            Detail::create($detail_data);
        }
        
        return Redirect::to('admin/item')
            ->with('success', __('items.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        $details = Detail::where('item_fk', $item->item_id)->get();
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)->get();
        
        foreach($colmap as $cm) {
            $list_id = $cm->column->list_fk;
            if($list_id) {
                $constraint = function (Builder $query) use ($list_id) {
                    $query->where('parent_fk', 0)->where('list_fk', $list_id);
                };
                $lists[$list_id] = Element::treeOf($constraint)->depthFirst()->get();
            }
        }
        
        $dt_list = Selectlist::where('name', '_data_type_')->first();
        $data_types = Element::where('list_fk', $dt_list->list_id)->with(['values', 'attributes'])->get();
        
        $l10n_list = Selectlist::where('name', '_translation_')->first();
        $translations = Element::where('list_fk', $l10n_list->list_id)->get();
        
        return view('admin.item.edit', compact('item', 'details', 'colmap', 'lists', 'data_types', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $validation_rules['fields'] = 'required|array';
        foreach ($request->input('fields') as $column_id => $value) {
            $validation_rules['fields.'.$column_id] = 'required|'. Column::find($column_id)->getValidationRule();
        }
        // Validate uploaded files
        foreach ($request->file('fields') as $column_id => $value) {
            $validation_rules['fields.'.$column_id] = Column::find($column_id)->getValidationRule();
        }
        
        $request->validate($validation_rules);
        
        $details = Detail::where('item_fk', $item->item_id)->get();
        
        // Save the details for all columns that belong to the item
        foreach ($request->input('fields') as $column_id => $value) {
            $detail = $details->where('column_fk', $column_id)->first();
            
            $data_type = Column::find($column_id)->getDataType();
            
            switch ($data_type) {
                case '_list_':
                    $detail->element_fk = intval($value);
                    break;
                case '_integer_':
                    $detail->value_int = intval($value);
                    break;
                case '_float_':
                    $detail->value_float = floatval($value);
                    break;
                case '_date_':
                    $detail->value_date = $value;
                    break;
                case '_string_':
                case '_url_':
                    $detail->value_string = $value;
                    break;
            }
            $detail->save();
        }
        foreach ($request->file('fields') as $column_id => $value) {
            $detail = $details->where('column_fk', $column_id)->first();
            
            $data_type = Column::find($column_id)->getDataType();
            
            $file = $request->file('fields.'.$column_id);
            switch ($data_type) {
                case '_image_':
                    if($file->isValid()) {
                        $path = 'public/images/';
                        $name = $column_id ."_". date('YmdHis') .".". $file->extension();
                        $file->storeAs($path, $name);
                        $detail->value_string  = $name;
                    }
                    break;
            }
            $detail->save();
        }
        
        return Redirect::to('admin/item')
            ->with('success', __('items.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();
        
        return Redirect::to('admin/item')
            ->with('success', __('items.deleted'));
    }
}
