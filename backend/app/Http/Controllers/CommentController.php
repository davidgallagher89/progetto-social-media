<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:500',
        ]);

        $userId = auth()->id() ?? 1;

        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $userId,
            'content' => $validated['content'],
        ]);

        $comment->load('user:id,name');

        return response()->json([
            'message' => 'Commento aggiunto!',
            'comment' => $comment
        ], 201);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Commento eliminato.'
        ], 200);
    }
}
