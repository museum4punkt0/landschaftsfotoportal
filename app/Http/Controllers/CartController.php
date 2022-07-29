<?php

namespace App\Http\Controllers;

use App\Cart;
use App\ModuleInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('viewOwn', Cart::class);

        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::firstWhere('name', 'gallery');
        throw_if(
            !$image_module,
            ModuleNotFoundException::class,
            __('modules.not_found', ['name' => 'gallery'])
        );

        $cart = Cart::myOwn(Auth::user()->id)
            ->with('item')
            ->latest()
            ->paginate(config('ui.cart_items'));
        
        return view('cart', compact('cart', 'image_module'));
    }
}
