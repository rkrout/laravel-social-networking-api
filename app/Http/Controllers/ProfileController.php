<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function searchProfile(Request $request, $query)
    {
        return User::where('name', 'like', "%$query%")
            ->where('name', '!=', $request->user()->name)
            ->select(
                'users.id',
                'users.name',
                'users.profile_image_url as profileImageUrl'
            )
            ->skip($request->query('start', 0))
            ->take($request->query('limit', 10))
            ->get();
    }

    public function getProfileDetails(Request $request, User $user)
    {
        $currentUserId = $request->user()->id;
        return User::where('users.id', $user->id)
            ->select(
                DB::raw('(select count(*) from posts where user_id = users.id) as totalPosts') ,
                DB::raw('(select count(*) from followers where user_id = users.id) as totalFollowers'),
                DB::raw('(select count(*) from followers where follower_id = users.id) as totalFollowings'),
                DB::raw("if(exists(select 1 from followers where followers.follower_id = $currentUserId and followers.user_id = users.id), true, false) as isFollowing"),
                'users.id',
                'users.name',
                'users.bio',
                'users.profile_image_url as profileImageUrl'
            )
            ->first();
    }

    public function follow(Request $request, User $user)
    {
        $user->followers()->firstOrCreate(['follower_id' => $request->user()->id]);
        return response()->json(['success' => 'Following']);
    }

    public function unFollow(Request $request, User $user)
    {
        $user->followers()->where('follower_id', $request->user()->id)->delete();
        return response()->json(['success' => 'Remove from Following list']);
    }
}
