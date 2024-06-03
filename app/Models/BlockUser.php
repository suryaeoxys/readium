<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'block_user_id'
    ];

    public function bookmarkPost(){
        return $this->hasOne(Recommandation::class, 'id', 'post_id');
    }

    public function bookmarkPosts(){
        return $this->hasOne(AskRecommandation::class, 'id', 'post_id');
    }

    public function store() {
        return $this->hasOne(VendorProfile::class, 'id', 'post_id');
    }

    public static function getDataByUserId($userId, $blockedUser) {
        return static::where('user_id', $userId)->where('block_user_id', $blockedUser)->first();
    }

    public static function getIdByUserId($userId) {
        return static::where('user_id', $userId)->pluck('post_id');
    }


    public static function getIdByUser($userId) {
        return static::where('user_id', $userId)->paginate();
    }

    public function postUser() {
        return $this->hasOne(AskRecommandation::class, 'id', 'post_id');
    }

    public function blockUserDetails() {
        return $this->hasOne(User::class, 'id', 'block_user_id');
    }
}
