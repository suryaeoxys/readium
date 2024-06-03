<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class CommentCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function($data) {
            if($data->user_id == Auth::user()->id){
                $my_account = true;
                $my_comment = true;
            }else {
                $my_account = false;
                $my_comment = false;
            }
            return [
                'id' => $data->id,
                'user_id' => new UserResource($data->postUser),
                'post_id' => $data->post_id,
                'type' => (string)$data->type,
                'comment' => (string)$data->comment,
                'date'         => Carbon::parse($data->created_at)->diffForHumans(),
                'my_account' => $my_account,
                'my_comment' => $my_comment,
                'is_like'   => $data->is_liked,
                'like_count'   => $data->likes->count(),
            ];
        });
    }
}