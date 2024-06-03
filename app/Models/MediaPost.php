<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class MediaPost extends Model
{
    use HasFactory;
    protected $table  = 'media_post';
    protected $fillable = [
        'user_id',
        'content',
        'image',
        'video',
        'link',
        'repost_id',
    ];

    protected $appends = ['is_liked'];

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'media_post_tagged_user');
    }
    
    public function postUser() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public static function getMediaPostById($postId){
        return static::where('id',$postId)->first();
    }

    public static function getMediaPost($keyword = null){
        return static::where('title', 'like', '%'.$keyword.'%')->paginate();
    }

    public function getIsLikedAttribute(): bool
    {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'media_post_id')->where('type', 'post');
    }

    public function repostcount(){
        return MediaPost::where('repost_id', $this->id)->count();
    }

    public function repost()
    {
        return $this->belongsTo(MediaPost::class, 'repost_id');
    }

}
