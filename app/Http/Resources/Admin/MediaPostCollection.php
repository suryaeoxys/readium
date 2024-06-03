<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class MediaPostCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            $tagged_users_name = [];
            foreach($data->viewers as $user) {
                $tagged_users_name[] = $user->first_name;
            }
            return [
                'id' => $data->id,
                'user' => new UserResource($data->postUser),
                'content' => $data->content,
                'image' => $data->image ? url(config('app.user_post_image').'/'.$data->image) : '',
                'video' => $data->video ? url(config('app.user_post_video').'/'.$data->video) : '',
                'link' => $data->link,
                'repost_count' => $data->repostcount(),
                'like_count' => $data->likes->count(),
                'comments_count' => $data->comments->count(),
                'is_following' => DB::table('followings')->where(['user_id' => auth()->user()->id, 'following_id' => $data->postUser->id])->exists(),
                'date' => Carbon::parse($data->updated_at)->diffForHumans(),
                'is_like'   =>  $data->is_liked,
                'is_mypost'   => (auth()->id() == $data->postUser->id)?true:false,
                'tagged_users' => $tagged_users_name
            ];
        });
    }
}
