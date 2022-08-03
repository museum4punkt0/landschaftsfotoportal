<?php

namespace App\Http\Controllers;

use App\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AjaxMenuController extends Controller
{
    /**
     * Get children of given menu item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getChildren(Request $request)
    {
        $item = Item::find(intval($request->query('item')));

        if ($item) {
            // Item types to be excluded from menu
            $exclude = config('menu.sidebar_exclude_item_type', []);
            // Ordering of menu items on certain menu level
            $order = config('menu.sidebar_item_order', []);
            // Calculate menu level to be fetched from current level
            $level = intval($request->query('level')) + 1;

            $children = $item->children
                ->where('public', 1)
                ->where('item_type_fk', '<>', data_get($exclude, $level, -1))
                ->sortBy(data_get($order, $level, 'title'))
                ->values()
                ->append('route_show_public')
                ->toArray();

            return response()->json(['data' => $children]);
        }
        else {
            $data = ['error' => 'item not found'];
            return response()->json($data, 404);
        }
    }
}
