<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MediaPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->postUser),
            'content' => (string)$this->content,
            'link' => (string)$this->link,
            'image' => (string)!empty($this->image) ? url(config('app.user_post_image').'/'.$this->image) : '',
            'video' => (string)!empty($this->video) ? url(config('app.user_post_video').'/'.$this->video) : '',
            // 'post_viewers_type' => $this->post_viewers_type,
            'date'          => Carbon::parse($this->updated_at)->diffForHumans()
        ];
    }
}