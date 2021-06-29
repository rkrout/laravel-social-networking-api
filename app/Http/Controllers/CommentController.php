<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function getComments(Request $request, Post $post)
    {
		$userId = $request->user()->id;
        return $post->comments()
            ->join('users', 'users.id', 'comments.user_id')
            ->select(
                DB::raw("if(comments.user_id = $userId, true, false) as isMyComment"),
                'comments.id',
                'comments.comment',
                'users.name',
                'users.profile_image_url as profileImageUrl'
            )
            ->skip($request->query('start', 0))
            ->take($request->query('limit', 10))
            ->get();
    }

    public function createComment(Request $request, Post $post)
    {
        $request->validate([
            'comment' => 'required|min:2|max:255'
        ]); 

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $request->comment
        ]);

        return response()->json([
            'id' => $comment->id,
            'comment' => $comment->comment,
            'name' => $request->user()->name,
            'profileImageUrl' => $request->user()->profile_image_url,
            'isMyComment' => true
        ], 201);
    }

    public function destroyComment(Request $request, Comment $comment)
    {
        if($comment->user_id == $request->user()->id)
        {
            $comment->delete();
            return response()->json(['success' => 'Comment deleted successfully']);
        }
    }
}
