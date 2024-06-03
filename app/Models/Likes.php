<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'media_post_id',
        'type',
    ];
    public static function getDataByUserAndStoreId($userId, $store_id,$type) {
        return static::where('user_id', $userId)->where('media_post_id', $store_id)->where('type',$type)->first();
    }
    public static function getIdByUserId($userId) {
        return static::where('user_id', $userId)->pluck('media_post_id');
    }
}
