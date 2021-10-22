<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;
use Auth;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = Cart::myOwn(Auth::user()->id)->with('item')
            ->latest()
            ->paginate(config('ui.cart_items'));
        
        return view('cart', compact('cart'));
    }
}
