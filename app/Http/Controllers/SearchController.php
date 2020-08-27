<?php

namespace App\Http\Controllers;

use App\Item;
use App\Taxon;
#use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
#use Redirect;

class SearchController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->orderBy('item_id')->get();
        
        return view('search.form', compact('menu_root'));
    }

    /**
     * Show search results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        // First level items for the sidebar menu
        $menu_root = Item::whereNull('parent_fk')->orderBy('item_id')->get();
        
        $search = $request->input('taxon_name');
        $taxa = Taxon::where('full_name', 'LIKE', "%{$search}%")
            ->orWhere('native_name', 'LIKE', "%{$search}%")
            ->with('items')
            ->orderBy('full_name')
            ->get();
        
        return view('search.form', compact('menu_root', 'taxa'));
    }
}
