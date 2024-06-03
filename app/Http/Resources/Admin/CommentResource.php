<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => new UserResource($this->postUser),
            'post_id' => $this->post_id,
            'type' => (string)$this->type,
            'comment' => (string)$this->comment,
            'date'         => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
