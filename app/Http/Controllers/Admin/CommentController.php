<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use App\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * @param  int  $item_id  ID of the item owning this comment
     * @return \Illuminate\Http\Response
     */
    public function index($item_id)
    {
        $comments = Comment::where('item_fk', $item_id)->orderBy('comment_id')->paginate(10);
        $item = Item::find($item_id);
        
        return view('admin.comment.list', compact('comments', 'item'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $item_id  ID of the item owning this comment
     * @return \Illuminate\Http\Response
     */
    public function create($item_id)
    {
        $item = Item::find($item_id);
        
        return view('admin.comment.create', compact('item'));
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
            'public' => 'required|integer',
        ]);
        
        $data = [
            'item_fk' => $item_id,
            'message' => $request->input('message'),
            'public' => $request->input('public'),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];
        Comment::create($data);
        
        return Redirect::to('admin/item/'.$item_id.'/comment')
            ->with('success', __('comments.created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        return view('admin.comment.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'message' => 'required|string',
            'public' => 'required|integer',
        ]);
        
        $comment->message = $request->input('message');
        $comment->public = $request->input('public');
        $comment->updated_by = $request->user()->id;
        $comment->save();
        
        return Redirect::to('admin/item/'.$comment->item_fk.'/comment')
            ->with('success', __('comments.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        
        return Redirect::to('admin/item/'.$comment->item_fk.'/comment')
            ->with('success', __('comments.deleted'));
    }
}
