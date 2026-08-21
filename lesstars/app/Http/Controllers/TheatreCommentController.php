<?php

namespace App\Http\Controllers;

use App\Models\TheatreComment;
use Illuminate\Http\Request;

class TheatreCommentController extends Controller
{
    public function index()
    {
        return view('admin.theatre-comments.index', ['comments' => TheatreComment::with(['theatre', 'parent'])->latest()->paginate(30)]);
    }

    public function approve(TheatreComment $comment)
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Comment approved.');
    }

    public function reject(TheatreComment $comment)
    {
        $comment->update(['status' => 'rejected']);
        return back()->with('success', 'Comment rejected.');
    }

    public function reply(Request $request, TheatreComment $comment)
    {
        $comment->theatre->comments()->create($request->validate(['body' => ['required', 'string', 'max:1500']]) + [
            'parent_id' => $comment->id,
            'name' => 'Les Stars Du Theatre',
            'status' => 'approved',
        ]);
        return back()->with('success', 'Reply published.');
    }
}
