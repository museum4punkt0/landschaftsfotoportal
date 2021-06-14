<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;

class CommentController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::myOwn(Auth::user()->id)->with('item')->latest()->paginate(10);
        
        return view('comment', compact('comments'));
    }
}
