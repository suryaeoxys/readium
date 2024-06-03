<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use App\Models\Following;
use App\Models\MediaPost;
// use Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    
    public function toArray($request)
    {
        $following = Following::where('user_id', $this->id)->count();
        $followers = Following::where('following_id', $this->id)->count();
        $medisPostCount = MediaPost::where('user_id',$this->id)->count();
        return [
            'id' => $this->id,
            'first_name' => (string)$this->first_name,
            'last_name' => (string)$this->last_name,
            'email' => (string)$this->email,
            'nickname' => (string)$this->nickname,
            'profile_image' => (string)!empty($this->profile_image) ? url(config('app.profile_image').'/'.$this->profile_image) : '',
            'following_count' => $following,
            'followers_count' => $followers,
            'post_count' => $medisPostCount,
            // 'is_complete' => boolval($this->is_complete),
        ];
    }
}