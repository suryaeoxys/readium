<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Following;
use App\Models\Recommandation;
class UserCollection extends ResourceCollection
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
            $following = Following::where('user_id', $data->id)->count();
            $followers = Following::where('following_id', $data->id)->count();
            $posts = Recommandation::where('user_id', $data->id)->count();
            return [
                'id' => $data->id,
                'name' => (string)$data->name,
                'email' => (string)$data->email,
                'phone' => (string)$data->phone,
                'wallet_balance' => (string)$data->wallet_balance,
                'earned_balance' => (string)$data->earned_balance,
                'profile_image' => (string)isset($data->profile_image) ? url(config('app.profile_image').'/'.$data->profile_image) : '',
                'crop_image' => (string)!empty($this->profile_image) ? url(config('app.crop_profile_image').'/'.$this->id.'/'.$this->profile_image) : '',
                'address'       => (string)$data->address,
                'referal_code' => (string)$data->referal_code,
                'is_driver_online' => boolval($data->is_driver_online),
                'is_vendor_online' => boolval($data->is_vendor_online),
                'delivery_range' => $data->delivery_range,
                'self_delivery' => boolval($data->self_delivery),
                'as_driver_verified' => boolval($data->as_driver_verified),
                'as_vendor_verified' => boolval($data->as_vendor_verified),
                'as_marketing_manager_verified' => boolval($data->as_marketing_manager_verified),
                'is_profile_complete' => boolval($data->is_profile_complete),
                'following_count' => $following,
                'followers_count' => $followers,
                'post_count' => $posts,
            ];
        });
    }
}
