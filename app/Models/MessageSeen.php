<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class MessageSeen extends Model
{

    public $table = 'message_seen';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'message_id',
        'group_id',
        'user_id',
    ];
    

}