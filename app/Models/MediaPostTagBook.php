<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaPostTagBook extends Model
{
    use HasFactory;
    protected $fillable = [
        'media_post',
        'tagged_book_id',
    ];
   
    public function viewers()
    {
        return $this->belongsToMany(User::class, 'media_post_tagged_books');
    }
}
