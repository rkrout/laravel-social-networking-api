<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;
use App\Models\LikedPost;

class Post extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'post_image_url',
        'description'
    ];
	
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}
	
	public function likes()
	{
		return $this->hasMany(LikedPost::class);
	}
}
