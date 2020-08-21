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
use Illuminate\Database\Eloquent\Builder;
#use Illuminate\Http\Request;
use Redirect;

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
        // Check for redirects
        $target = $item->getDetailWhereDataType('_redirect_');
        if($target && $target != __('items.no_detail_with_data_type')) {
            return Redirect::to($target);
        }
        
        // All items for the show blade
        // TODO: children or descendants should be enough
        $items = Item::tree()->depthFirst()->get();
        
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->orderBy('item_id')->get();
        
        // Get the menu path of the requested item
        $ancestors = Item::find($item->item_id)->ancestorsAndSelf()->orderBy('depth', 'asc')->first();
        $path = array_reverse(explode('.', $ancestors->path));
        
        // Details of selected item
        $details = Detail::where('item_fk', $item->item_id)->get();
        
        // Only columns associated with this item's taxon or its descendants
        $taxon_id = $item->taxon_fk;
        $colmap = ColumnMapping::where('item_type_fk', $item->item_type_fk)
            ->where(function (Builder $query) use ($taxon_id) {
                return $query->whereNull('taxon_fk')
                    ->orWhereHas('taxon.descendants', function (Builder $query) use ($taxon_id) {
                        $query->where('taxon_id', $taxon_id);
                });
            })
            ->orderBy('column_order')->get();
        
        // Translations for titles of columns and column groups
        $l10n_list = Selectlist::where('name', '_translation_')->first();
        $translations = Element::where('list_fk', $l10n_list->list_id)->get();
                
        return view('item.show', compact('item', 'items', 'details', 'menu_root', 'path', 'colmap', 'translations'));
    }
}
