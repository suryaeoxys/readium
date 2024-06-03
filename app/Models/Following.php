<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'following_id',
    ];

    public function store() {
        return $this->hasOne(VendorProfile::class, 'id', 'following_id');
    }

    public static function getDataByUserAndStoreId($userId, $store_id) {
        return static::where('user_id', $userId)->where('following_id', $store_id)->first();
    }

    public static function getIdByUserId($userId) {
        return static::where('user_id', $userId)->pluck('following_id');
    }

    public static function getIdByUser($userId) {
        return static::where('user_id', $userId)->paginate();
    }

    public static function getFollowers($userId) {
        return static::where('following_id', $userId)->paginate();
    }

    public function followerUser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    public function followingUser(){
        return $this->hasOne(User::class, 'id', 'following_id');
    }

    public function friendUser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function friend()
    {
        return $this->belongsTo(User::class, 'following_id', 'id');
    }
}
