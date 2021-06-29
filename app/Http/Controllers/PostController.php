<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use App\Models\Follower;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\LikedPost;

class PostController extends Controller
{
    public function createPost(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'description' => 'nullable|min:2|max:255'
        ]);

        $request->user()->posts()->create([
            'post_image_url' => $request->file('image')->store('images', 'public'),
            'description' => $request->description ?? ""
        ]);

        return response()->json(['success' => 'Post created successfully'], 201);
    }

    public function destroyPost(Request $request, Post $post)
    {
        if($post->user_id == $request->user()->id)
        {
            Storage::disk('public')->delete($post->post_image_url);
            $post->delete();
            return response()->json(['success' => 'Post deleted successfully']);
        }
    }

    public function getOwnPosts(Request $request)
    {
        return User::where('users.id', $request->user()->id)
            ->join('posts', 'posts.user_id', 'users.id')
            ->select(
                DB::raw('(select count(*) from liked_posts where liked_posts.post_id = posts.id) as totalLikes'),
                DB::raw('false as isLiked'),
                'posts.description',
                'posts.post_image_url as postImageUrl',
                'posts.id',
                'posts.created_at as createdAt',
                'users.name',
                'users.profile_image_url as profileImageUrl'
            )
            ->skip($request->query('start', 0))
            ->take($request->query('limit', 10))
			->orderBy('posts.created_at', 'desc')
            ->get();
    }

    public function getPosts(Request $request)
    {
        return Follower::where('followers.follower_id', $request->user()->id)
            ->join('users', 'users.id', 'followers.user_id')
            ->join('posts', 'posts.user_id', 'users.id')
            ->select(
                DB::raw('(select count(*) from liked_posts where liked_posts.post_id = posts.id) as totalLikes'),
                DB::raw('if(exists(select 1 from liked_posts where liked_posts.post_id = posts.id and liked_posts.user_id = followers.follower_id), true, false) as isLiked'),
                'posts.description',
                'posts.post_image_url as postImageUrl',
                'posts.id',
                'posts.created_at as createdAt',
                'users.name',
                'users.profile_image_url as profileImageUrl'
            )
			
            ->skip($request->query('start', 0))
            ->take($request->query('limit', 10))
			->orderBy('posts.created_at', 'desc')
            ->get();
    }

    public function like(Request $request, Post $post)
    {
        $post->likes()->firstOrCreate([
            'user_id' => $request->user()->id 
        ]);
		
        return response()->json();
    }

    public function removeLike(Request $request, Post $post)
    {
        $post->likes()->where('user_id', $request->user()->id)->delete();
        return response()->json();
    }
}
