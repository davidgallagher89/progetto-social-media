<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $userId = auth()->id() ?? 1;
        $postId = $validated['post_id'];

        $existingLike = Like::where('post_id', $postId)
                            ->where('user_id', $userId)
                            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
            $liked = true;
        }

        $totalLikes = Like::where('post_id', $postId)->count();

        return response()->json([
            'liked'       => $liked,
            'likes_count' => $totalLikes,
        ], 200);
    }
}
