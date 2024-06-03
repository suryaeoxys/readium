<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class GroupChat extends Model
{

    public $table = 'group_chats';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'reply_id',
        'sender_id',
        'group_id',
        'message',
        'file',
        'forward',
        'forward',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }
    public function scopeUnseenForUser($query, $groupId, $userId)
    {
        return $query->where('group_id', $groupId)
                    ->whereDoesntHave('seenBy', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
    }

    public function seenBy()
    {
        return $this->belongsToMany(User::class, 'message_seen', 'message_id', 'user_id');
    }

    public function isSeen($gid,$mid){
        return MessageSeen::where(['message_id'=>$mid,'group_id'=>$gid,'user_id'=>auth()->user()->id])->exists();
    }
}