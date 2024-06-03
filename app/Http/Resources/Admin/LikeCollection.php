<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class FollowingCollection extends ResourceCollection
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
                'user_id'       => $data->user_id ?? '',
                'following'  => new UserResource($data->followingUser),
                'date'          => Carbon::parse($data->created_at)->format('d F,Y - H:i A')
            ];
        });
    }
}
