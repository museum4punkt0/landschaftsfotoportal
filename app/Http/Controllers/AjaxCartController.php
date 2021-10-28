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
     * @param  int  $item_id  ID of the item owning this cart
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $item_id)
    {
        $this->authorize('add', Cart::class);

        // Prevent duplicate items per user
        if (Cart::where([['item_fk', $item_id], ['created_by', $request->user()->id]])->get()->isEmpty()) {
            $data = [
                'item_fk' => $item_id,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ];
            Cart::create($data);
        }
        
        return response()->json(['success' => __('cart.added')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function remove(Cart $cart)
    {
        $this->authorize('remove', $cart);

        $cart->delete();
        
        return response()->json(['success' => __('cart.removed')]);
    }
}
