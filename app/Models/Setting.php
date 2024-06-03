<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
    ];

    public static function getDataByKey($key) {
        return static::where('name', $key)->first();
    }

    public static function getAllSettingData() {
        return static::pluck('value','name');
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'ask_recommandation_user', 'ask_recommandation_id', 'user_id');
    }
}
