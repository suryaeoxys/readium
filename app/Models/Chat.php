<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Chat extends Model
{

    public $table = 'chats';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'message_id',
        'sender_id',
        'receiver_id',
        'message',
        'file',
        'forward',
        'checkout',
        'is_edited',
    ];

}