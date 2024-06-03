<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helper\Helper;
use Carbon\Carbon;

class GroupListCollection extends ResourceCollection
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
                'id'           => $data->id,
                'name'         => $data->name,
                'image'        => $data->image,
                'desc'         => $data->desc,
                'is_group'     => $data->is_group,
                'last_msg'     => $data->lastMsg ?Helper::decryptMessage($data->lastMsg->message): null,
                'last_msg_time'=> $data->lastMsg ? $data->lastMsg->created_at->format('Y-m-d H:i:s') : null,
            ];
        });
    }
}
