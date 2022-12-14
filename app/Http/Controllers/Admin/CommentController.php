<?php

namespace App\Http\Controllers\Admin;

use App\Comment;
use App\Item;
use App\ModuleInstance;
use App\Http\Controllers\Controller;
use App\Notifications\CommentPublished;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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

        //Show error if comments are disabled
        abort_if(!config('ui.comments'), 403, __('common.module_disabled'));

        // Use app\Policies\CommentPolicy for authorizing ressource controller
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $item_id  ID of the item owning this comment
     * @return \Illuminate\Http\Response
     */
    public function index($item_id)
    {
        $comments = Comment::where('item_fk', $item_id)->orderBy('comment_id', 'desc')->paginate(10);
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
            'message' => 'required|string|max:4095',
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
     * Display a listing of all comments.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_all()
    {
        $this->authorize('vieAny', Comment::class);
        
        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::getByName('gallery');

        $comments = Comment::with('item')->latest('updated_at')->paginate(10);
        
        return view('admin.comment.list_all', compact('comments', 'image_module'));
    }

    /**
     * Display a listing of non-public comments for publishing.
     *
     * @return \Illuminate\Http\Response
     */
    public function list_unpublished()
    {
        $this->authorize('publish', Comment::class);
        
        // Load module containing column's configuration and naming
        $image_module = ModuleInstance::getByName('gallery');

        $comments = Comment::where('public', '<', 1)->with('item')->latest('updated_at')->paginate(10);
        
        return view('admin.comment.publish', compact('comments', 'image_module'));
    }

    /**
     * Publish a single or all non-public comments.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function publish(Comment $comment)
    {
        $this->authorize('publish', $comment);
        
        // Check for single comment or batch
        if ($comment->comment_id) {
            $comments = [Comment::find($comment->comment_id)];
        } else {
            $comments = Comment::where('public', '<', 1)->orderBy('comment_id')->get();
        }
        
        $count = 0;
        // Set public flag on all given comments
        foreach ($comments as $comment) {
            $comment->public = 1;
            $comment->save();
            $count++;
            // Notify the owner of the comment
            Notification::send($comment->creator, new CommentPublished($comment));
        }
        
        return Redirect::to('admin/comment/unpublished')
            ->with('success', __('comments.published', ['count' => $count]));
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
            'message' => 'required|string|max:4095',
            'public' => 'required|integer',
        ]);
        
        $comment->message = $request->input('message');
        $comment->public = $request->input('public');
        $comment->updated_by = $request->user()->id;
        $comment->save();
        
        // Notify the owner of the comment if comment was published
        if ($comment->public == 1) {
            Notification::send($comment->creator, new CommentPublished($comment));
        }

        // Note: route to comments belonging to same item: 'admin/item/'.$comment->item_fk.'/comment'
        return redirect()->route('comment.all')
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

        return back()->with('success', __('comments.deleted'));
    }
}
