<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signUp(Request $request)
	{
		$request->validate([
			'name' => 'required|min:2|max:255',
			'email' => 'required|email|unique:users,email',
			'bio' => 'required|min:2|max:255',
			'password' => 'required|min:6|max:255'
		]);
			
		User::create([
			'name' => $request->name,
			'email' => $request->email,
			'bio' => $request->bio,
			'password' => Hash::make($request->password)
		]);
		
		return response()->json(['success' => 'User created successfully'], 201);
	}

	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required',
			'password' => 'required'
		]);

		$user = User::where('email', $request->email)->first();

		if($user != null && Hash::check($request->password, $user->password))
		{
			return response()->json($user->createToken($user->id)->plainTextToken, 200);
		}
		else
		{
			return response()->json(['error' => 'Invalid email or password'], 401);
		}	
	}


	public function logout(Request $request)
	{
		$request->user()->tokens()->delete();
		return response()->json(['success' => 'logout successful'], 200);
	}

	public function changePassword(Request $request)
	{
		$request->validate([
			'oldPassword' => 'required',
			'newPassword' => 'required|min:6'
		]);
		
		$user = $request->user();
		
		if(Hash::check($request->oldPassword, $user->password))
		{
			$user->password = Hash::make($request->newPassword);
			$user->save();
			$user->tokens()->delete();
			return response()->json($user->createToken($user->id)->plainTextToken, 200);
		}
		else 
		{
			return response()->json(['error' => 'Invalid password'], 422);
		}
	}
}
