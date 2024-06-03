<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaPostTagUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tagged_user_id',
    ];
   
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'media_post_tagged_user');
    }
}
