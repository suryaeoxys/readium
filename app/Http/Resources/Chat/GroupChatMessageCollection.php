<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helper\Helper;
use Carbon\Carbon;

class GroupChatMessageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function($data) {
            return [
                'id'            => $data->id,
                'reply_id'            => $data->message_id,
                'sender_id'            => $data->sender_id,
                'sender_name'            => $data->sender->first_name.' '.$data->sender->last_name,
                'message'       =>is_null($data->deleted_at)?(isset($data->message)?Helper::decryptMessage($data->message):''):'This message has been deleted',
                'file'       => is_null($data->deleted_at)?(isset($data->file)?url(config('app.chat_file').'/'.$data->file):''):'This file has been deleted',
                'is_edited'       => $data->is_edited,
                'is_seen'       => $data->isSeen($data->group_id,$data->id),
                'is_deleted'       => is_null($data->deleted_at)?0:1,
            ];
        });
    }
}
