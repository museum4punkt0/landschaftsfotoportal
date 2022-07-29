<?php

namespace App\Http\Controllers;

use App\Comment;
use App\ModuleInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('viewOwn', Comment::class);

        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::firstWhere('name', 'gallery');
        throw_if(
            !$image_module,
            ModuleNotFoundException::class,
            __('modules.not_found', ['name' => 'gallery'])
        );

        $comments = Comment::myOwn(Auth::user()->id)->with('item')->latest()->paginate(10);

        return view('comment', compact('comments', 'image_module'));
    }
}
