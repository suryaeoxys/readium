<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
use Auth;

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
            if($data->following_id == Auth::user()->id){
                $my_account = true;
                // $follow_button = false;
            }else {
                $my_account = false;
                // $follow_button = true;
            }

            return [
                'id'            => $data->id,
                'user_id'       => $data->user_id ?? '',
                'following'  => new UserResource($data->followingUser),
                'date'          => Carbon::parse($data->created_at)->format('d F,Y - H:i A'),
                'my_account' => $my_account
                // 'follow_button' => $follow_button
            ];
        });
    }
}
