<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class AjaxCommentController extends Controller
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
    public function store(Request $request, $item_id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        
        $data = [
            'item_fk' => $item_id,
            'message' => $request->input('message'),
            'public' => 0,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];
        Comment::create($data);
        
        return response()->json(['success' => __('comments.created')]);
    }
}
