<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\ColumnMapping;
use App\Detail;
use App\Item;
use Auth;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('verified');
    }

    /**
     * Get a single specimen item.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function showSpecimen(Item $item)
    {
        return response()->json(['info' => 'to be implemented...']);
    }
}
