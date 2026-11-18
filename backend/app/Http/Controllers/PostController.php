<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with([
            'user:id,name',
            'comments.user:id,name',
            'likes'
        ])
        ->withCount('likes')
        ->latest()
        ->get();

        return response()->json($posts, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content'   => 'required|string|max:1000',
            'image_url' => 'nullable|url',
        ]);

        // Utente predefinito in assenza di autenticazione
        $userId = auth()->id() ?? 1;

        $post = Post::create([
            'user_id'   => $userId,
            'content'   => $validated['content'],
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $post->load(['user:id,name', 'comments', 'likes']);
        $post->likes_count = 0;

        return response()->json([
            'message' => 'Post pubblicato con successo!',
            'post'    => $post
        ], 201);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post eliminato correttamente.'
        ], 200);
    }
}
