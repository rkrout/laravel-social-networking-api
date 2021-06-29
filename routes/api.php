<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;


Route::middleware('auth:sanctum')->group(function(){
		
	Route::get('/post/home', [PostController::class, 'getPosts']);

	Route::get('/post', [PostController::class, 'getOwnPosts']);

	Route::post('/post/create', [PostController::class, 'createPost']);

	Route::delete('/post/{post}/destroy', [PostController::class, 'destroyPost']);

	Route::post('/post/{post}/like', [PostController::class, 'like']);

	Route::post('/post/{post}/remove-like', [PostController::class, 'removeLike']);


	Route::get('/comment/{post}', [CommentController::class, 'getComments']);

	Route::post('/comment/{post}/create', [CommentController::class, 'createComment']);

	Route::delete('/comment/{comment}/destroy', [CommentController::class, 'destroyComment']);


	Route::get('/account/followers', [AccountController::class, 'getFollowers']);

	Route::get('/account/followings', [AccountController::class, 'getFollowings']);

	Route::get('/account/details', [AccountController::class, 'getAccountDetails']);

	Route::post('/account/edit', [AccountController::class, 'editAccount']); 



	Route::get('/search/{query}', [ProfileController::class, 'searchProfile']);

	Route::get('/profile/{user}/details', [ProfileController::class, 'getProfileDetails']);

	Route::post('/profile/{user}/follow', [ProfileController::class, 'follow']);

	Route::delete('/profile/{user}/un-follow', [ProfileController::class, 'unFollow']);
	
	
	Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
	
	Route::delete('/auth/logout', [AuthController::class, 'logout']);
});


Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/auth/sign-up', [AuthController::class, 'signUp']);