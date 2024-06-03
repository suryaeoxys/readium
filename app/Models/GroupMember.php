<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class GroupMember extends Model
{

    public $table = 'group_members';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'group_id',
        'user_id',
        'is_block',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}