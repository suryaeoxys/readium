<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Likes;
use App\Models\Comment;
use Auth;

class ReviewCollection extends ResourceCollection
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
            $count = Likes::where('recommended_id',$data->id)->count();
            $commentCount = Comment::where('post_id',$data->id)->where('type','recommandation')->count();
            if($data->user_id == Auth::user()->id){
                $my_account = true;
            }else {
                $my_account = false;
            }

            return [
                'id' => $data->id,
                'user' => new UserResource($data->postUser),
                'review'  => (string)$data->review,
                'title' => (string)$data->title,
                'link' => (string)$data->link,
                'category_id' => (string)$data->category_id,
                'image' => (string)!empty($data->image) ? url(config('app.vendor_product_image').'/'.$data->image) : '',
                'status' => $data->status,
                'wishlist'     =>  boolval($data->favourite),
                'is_like'     =>  boolval($data->like),
                'like_count'  => $count,
                'comment_count' => $commentCount,
                'is_editable' => boolval($data->editable),
                'date'          => Carbon::parse($data->updated_at)->diffForHumans(),
                'my_account' => $my_account
            ];
        });
    }
}