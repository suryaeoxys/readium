<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

use Carbon\Carbon;

class NotificationCollection extends ResourceCollection
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
                'id'                => $data->id,
                'user_id'           => $data->user_id ?? '',
                'post_id'           => $data->post_id,
                'post_type'         => $data->post_type,
                'parent_id'         => $data->parent_id,
                'title'             => $data->title ?? '',
                'body'              => $data->body ?? '',
                'seen'              => $data->seen ?? '',
                'time'              => Carbon::parse($data->created_at)->diffForHumans()
            ];
        });
    }
}
