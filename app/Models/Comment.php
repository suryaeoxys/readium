<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'comment'
    ];

    protected $appends = ['is_liked'];

    public function postUser() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function getIsLikedAttribute(): bool
    {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'media_post_id')->where('type', 'comment');
    }

}
