<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Auth;
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
        // Get the currently authenticated user
        $user = Auth::user();
        
        // Get some statistics for admin dashboard
        if (Gate::allows('show-admin')) {
            // Number of unpublished items
            $items = Item::where('public', 0)->count();
            // Number of unpublished items
            $comments = Comment::where('public', 0)->count();
            
            return view('admin.home', compact('user', 'items', 'comments'));
        }
        // User dashboard
        else {
            return view('home', compact('user'));
        }
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
        if ($item) {
            return Redirect::to('item/'.$item->item_id);
        } else {
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
