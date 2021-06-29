<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Follower;

class AccountController extends Controller
{
    public function getFollowers(Request $request)
    {
        return Follower::where('followers.user_id', $request->user()->id)
            ->join('users', 'users.id', 'followers.follower_id')
            ->select(
                'users.id',
                'users.name',
                'users.profile_image_url as profileImageUrl'
            )
            ->skip($request->query('start', 0))
            ->take($request->query('limit', 10))
            ->get();
    }

    public function getFollowings(Request $request)
    {
        return Follower::where('followers.follower_id', $request->user()->id)
            ->join('users', 'users.id', 'followers.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.profile_image_url as profileImageUrl'
            )
            ->skip($request->query('start', 0))
            ->take($request->query('limit', 10))
            ->get();
    }

    public function getAccountDetails(Request $request)
    {
        return User::where('id', $request->user()->id)
            ->select(
                DB::raw('(select count(*) from posts where posts.user_id = users.id) as totalPosts'),
                DB::raw('(select count(*) from followers where followers.user_id = users.id) as totalFollowers'),
                DB::raw('(select count(*) from followers where followers.follower_id = users.id) as totalFollowings'),
                'users.id',
                'users.name',
                'users.email',
                'users.bio',
                'users.profile_image_url as profileImageUrl'
            )
            ->first();
    }

    public function editAccount(Request $request)
    {
		$request->validate([
			'name' => 'required|min:2|max:255',
			'email' => 'required|min:2',
			'bio' => 'required|min:2|max:255',
			'image' => 'nullable|image'
		]);
		
		$user = $request->user();
		$user->name = $request->name;
		$user->email = $request->email;
		$user->bio = $request->bio;
		
		if($request->hasFile('image'))
		{
			Storage::disk('public')->delete($user->profile_image_url);
			$user->profile_image_url = $request->file('image')->store('images', 'public');
			$user->save();
			return response()->json($user->profile_image_url);
		}
		else
		{
			$user->save();
			return response()->json();
		}
    }
}
