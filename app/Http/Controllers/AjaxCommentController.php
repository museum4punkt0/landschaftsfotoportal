<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Item;
use App\User;
use App\Http\Controllers\Controller;
use App\Notifications\CommentAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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

        //Show error if comments are disabled
        abort_if(!config('ui.comments'), 403, __('common.module_disabled'));
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
        $this->authorize('create', Comment::class);

        $request->validate([
            'message' => 'required|string|max:4095',
        ]);
        
        $data = [
            'item_fk' => $item_id,
            'message' => $request->input('message'),
            'public' => 0,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];
        $comment = Comment::create($data);

        // Notify all users with moderation privileges
        Notification::send(User::moderators()->get(), new CommentAdded($comment));
        
        return response()->json(['success' => __('comments.created')]);
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
        $this->authorize('update', $comment);

        $request->validate([
            'message' => 'required|string|max:4095',
        ]);
        
        $comment->message = $request->input('message');
        $comment->public = 0;
        $comment->updated_by = $request->user()->id;
        $comment->save();
        
        return response()->json(['success' => __('comments.updated')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();
        
        return response()->json(['success' => __('comments.deleted')]);
    }
}
