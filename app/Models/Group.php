<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Group extends Model
{

    public $table = 'groups';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'creator_id',
        'name',
        'image',
        'limit',
        'is_admin_post',
        'is_group',
        'is_blocked',
        'desc',
    ];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupChat::class);
    }

    public function lastMsg(){
        return $this->hasOne(GroupChat::class)->latest();
    }

}