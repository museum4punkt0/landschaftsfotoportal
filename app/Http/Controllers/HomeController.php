<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use Session;
use Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['locale', 'frontend']);
    }

    /**
     * Show the application dashboard in the admin backend.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    /**
     * Show the home page in the frontend.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function frontend()
    {
        // Get the item which holds the content of the home page
        $item = Item::where('title', config('menu.home_item_title', 'Home'))->first();
        
        // Check if item exists, otherwise redirect to search page
        if($item) {
            return Redirect::to('item/'.$item->item_id);
        }
        else {
            return redirect()->route('search.index')
                ->with('warning', __('items.no_home_page'));
        }
    }
    
    /**
     * Set the user's locale.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function locale($locale)
    {
        Session::put('locale', $locale);
        
        return back()->withInput();
    }
}
