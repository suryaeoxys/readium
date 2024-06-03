<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
use Auth;
use DB;

class FollowerCollection extends ResourceCollection
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
            if($data->user_id == Auth::user()->id){
                $my_account = true;
            }else {
                $my_account = false;
            }
            return [
                'id'            => $data->id,
                'user_id'       => $data->user_id ?? '',
                'following'  => new UserResource($data->followerUser),
                'date'          => Carbon::parse($data->created_at)->format('d F,Y - H:i A'),
                'is_following' => DB::table('followings')->where(['user_id' => auth()->user()->id,'following_id' =>$data->followerUser->id])->exists(),
                'my_account' => $my_account
            ];
        });
    }
}
