<?php

namespace App\Http\Controllers;

use App\Item;
#use App\Taxon;
use App\Detail;
#use App\Column;
use App\ColumnMapping;
use App\Selectlist;
use App\Element;
#use App\Http\Controllers\Controller;
#use Illuminate\Database\Eloquent\Builder;
#use Illuminate\Http\Request;
#use Redirect;

class ItemController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        // All items for the sidebar menu
        $items = Item::tree()->depthFirst()->get();
        
        // Details of selected item
        $details = Detail::where('item_fk', $item->item_id)->get();
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)->orderBy('column_order')->get();
        
        $l10n_list = Selectlist::where('name', '_translation_')->first();
        $translations = Element::where('list_fk', $l10n_list->list_id)->get();
        
        return view('item.show', compact('item', 'items', 'details', 'colmap', 'translations'));
    }
}
