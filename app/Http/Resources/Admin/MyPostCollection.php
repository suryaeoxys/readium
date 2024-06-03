<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class MyPostCollection extends ResourceCollection
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
            $mediaPost = $data->repost_id ? $data->repost : $data;

            return [
                'id' => $data->id,
                'repost_id' => $mediaPost->repost_id,
                'is_repost' => $data->repost_id ? true : false,
                'content' => $mediaPost->content,
                'image' => $mediaPost->image ? url(config('app.user_post_image').'/'.$mediaPost->image) : '',
                'video' => $mediaPost->video ? url(config('app.user_post_video').'/'.$mediaPost->video) : '',
                'link' => $mediaPost->link,
                'repost_count' => $mediaPost->repostcount(),
                'like_count' => $data->likes->count(),
                'comments_count' => $data->comments->count(),
                'date' => Carbon::parse($mediaPost->created_at)->diffForHumans(),
                'is_like' => $mediaPost->is_liked,
            ];
        });
    }
}
