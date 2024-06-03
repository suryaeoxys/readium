<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helper\Helper;
use Carbon\Carbon;

class SingleChatMessageCollection extends ResourceCollection
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
                'message'       => isset($data->message)?Helper::decryptMessage($data->message):'',
                'file'       => isset($data->file)?url(config('app.chat_file').'/'.$data->file):'',
                'is_seen'       => $data->checkout,
                'is_edited'       => $data->is_edited,
                'is_deleted'       => is_null($data->deleted_at)?0:1,
            ];
        });
    }
}
