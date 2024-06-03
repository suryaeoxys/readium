<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\ResponseBuilder;
use App\Helper\Helper;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Likes;
use App\Models\MediaPost;
use App\Models\Notification;
use Auth;
use DB;

class LikesController extends Controller
{
    /**
     * Add/Remove like on a post .
     *
     * @return \Illuminate\Http\Response
     */
    public function addRemoveLike(Request $request) {
        DB::beginTransaction();
        try {
            $user = Auth::guard('api')->user();

            $validSet = [
                'type' => 'required|in:post,comment',
                'post_id' => 'required_if:type,post|integer|exists:media_post,id',
                'comment_id' => 'required_if:type,comment|integer|exists:comments,id',
            ]; 
            $isInValid = $this->isValidPayload($request, $validSet);
            
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
            $data_id = ($request->type == 'post')?$request->post_id:$request->comment_id;
            $wishlist = Likes::getDataByUserAndStoreId($user->id, $data_id,$request->type);

            if($wishlist) {
                $wishlist->delete();
                DB::commit();
                return ResponseBuilder::successMessage(trans('global.LIKES_REMOVED'), $this->success); 
            }

            Likes::create([
                'user_id' => $user->id,
                'type' => $request->type,
                'media_post_id' => ($request->type == 'post')?$request->post_id:$request->comment_id,
            ]);

            DB::commit();
            if($request->type == 'post'){
                $post =  MediaPost::find($request->post_id);
            }else{
                $post = Comment::find($request->comment_id);
            }
            $arr1 = array('{user}', '{recommendation_title}');
            $arr2 = array($user->name, $post->title);
            $msg = str_replace($arr1, $arr2, trans('notifications.RECOMMENDATION_LIKE'));
      
            $notification = Notification::create([
                'user_id' => $post->user_id,
                'post_id' => $post->id,
                'title' => 'Like',
                'body' => $msg
            ]);

            Helper::fireBasePushNotification($post->user_id, $post->id, 'Like',$msg);
            
            return ResponseBuilder::successMessage(trans('global.LIKES_ADDED'), $this->success); 
        } catch (\Exception $e) {
            return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
            DB::rollback();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

}
