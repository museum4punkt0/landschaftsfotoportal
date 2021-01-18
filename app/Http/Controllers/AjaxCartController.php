<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class AjaxCartController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $item_id  ID of the item owning this comment
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $item_id)
    {
        $data = [
            'item_fk' => $item_id,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];
        Cart::create($data);
        
        return response()->json(['success' => __('cart.added')]);
    }
}
