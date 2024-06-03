<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\FollowingCollection;
use App\Http\Resources\Admin\FollowerCollection;
use App\Http\Resources\Admin\FriendsCollection;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Helper\ResponseBuilder;
use App\Helper\Helper;
use App\Models\User;
use App\Models\Following;
use App\Models\Notification;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Mail\NewSignUp;
use Auth;
use DB;

class FollowingController extends Controller
{
     /**
     * User Add/Remove Follower Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function addRemoveFollower(Request $request) {
        DB::beginTransaction();
        try {
            
            $user = Auth::guard('api')->user();

            $validSet = [
                'following_id' => 'required | integer',
            ]; 
            $isInValid = $this->isValidPayload($request, $validSet);
            
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            $following = User::find($request->following_id);
            if(!$following) {
                return ResponseBuilder::error(trans('global.INVALID_FOLLOWING_ID'),$this->success);
            }

            $followingID = Following::getDataByUserAndStoreId($user->id, $request->following_id);

            if($followingID) {
                $followingID->delete();
                DB::commit();
                return ResponseBuilder::successMessage(trans('global.FOLLOWING_REMOVED'), $this->success); 
            }

            Following::create([
                'user_id' => $user->id,
                'following_id' => $request->following_id,
            ]);

            $arr1 = array('{user}');
            $arr2 = array($user->name);
            $msg = str_replace($arr1, $arr2, trans('notifications.FOLLOWING_ADDED'));
            
            $notification = Notification::create([
                'user_id' => $request->following_id,
                'post_id' => $user->id,
                'title' => 'New Follow',
                'body' => $msg
            ]);
            
            Helper::fireBasePushNotification($request->following_id,$user->id,'Following', $msg);

            $data = Setting::getDataByKey('logo_1');
            $image = url('/' . config('app.logo') . '/' . $data->value);
            $frnd = User::where('id',$request->following_id)->first();
            $mailData = EmailTemplate::getMailByMailCategory(strtolower('follow'));
            if(isset($mailData)) {

                $arr1 = array('{image}','{name}', '{userName}');
                $arr2 = array($image,$frnd->nickname, $user->nickname);

                $email_content = $mailData->email_content;
                $email_content = str_replace($arr1, $arr2, $email_content);
            
                $config = [
                    'from_email' => isset($mailData->from_email) ? $mailData->from_email : env('MAIL_FROM_ADDRESS'),
                    'name' => isset($mailData->from_name) ? $mailData->from_name : env('MAIL_FROM_NAME'),
                    'subject' => $mailData->email_subject, 
                    'message' => $email_content,
                ];
                
                Mail::to($frnd->email)->send(new NewSignUp($config));
            }

            DB::commit();
            return ResponseBuilder::successMessage(trans('global.FOLLOWING_ADDED'), $this->success); 
        } catch (\Exception $e) {
            DB::rollback();
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

     /**
     * User Following List Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function followingList(Request $request) {
        try {
            if($user = Auth::guard('api')->user()){
                $follow = Following::getIdByUser($user->id);
                $this->response = new FollowingCollection($follow);
            }
            if($request->user_id)
            {
                $follow = Following::getIdByUser($request->user_id);
                $this->response = new FollowingCollection($follow);
                if($request->user_id == Auth::user()->id){
                    $this->response->follow_button = false;
                }
            }
            // return ResponseBuilder::successWithPagination($follow, $this->response, trans('global.all_following_list'), $this->success);
            return ResponseBuilder::successMessage(trans('global.all_following_list'), $this->success,$this->response); 
        } catch (\Exception $e) {
            // return $e->getMessage();
            return ResponseBuilder::error(trans('SOMETHING_WENT'), $this->badRequest);
        }
    }

     /**
     * User Followers List Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function followersList(Request $request) {
        try {
            if($user = Auth::guard('api')->user()){
                $follow = Following::getFollowers($user->id);
                $page = ($request->pagination) ? $request->pagination : 10;
                $this->response = new FollowerCollection($follow);
            }

            if($request->user_id){
                $follow = Following::getFollowers($request->user_id);
                $page = ($request->pagination) ? $request->pagination : 10;
                $this->response = new FollowerCollection($follow);
            }
            return ResponseBuilder::successWithPagination($follow, $this->response, trans('global.all_followers_list'), $this->success);
            // return ResponseBuilder::successMessage(trans('global.all_followers_list'), $this->success,$this->response); 
        } catch (\Exception $e) {
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'), $this->badRequest);
        }
    }

     /**
     * User FriendList Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function friendsList()
    {
        try {
            $user = Auth::guard('api')->user();

            $friends = Following::where('user_id', $user->id)->orWhere('following_id', $user->id)->pluck('user_id', 'following_id');
            $data = [];

            foreach ($friends as $key => $value) {
                $data[] = $key;
                $data[] = $value;
            }
            
            $data = array_unique($data);
            $userKey = array_search($user->id, $data);
            if ($userKey !== false) {
                unset($data[$userKey]);
            }

            $this->response = User::whereIn('id', $data)->select('id','first_name','last_name','nickname','profile_image')->get();

            $this->response->map(function ($item) {
                $item->profile_image = !empty($item->profile_image) ? url(config('app.profile_image') . '/' . $item->profile_image) : '';
                // return $item;
            });
 
            return ResponseBuilder::success(trans('global.friends_list'), $this->success, $this->response);
        } catch (\Exception $e) {
            // return $e;
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'), $this->badRequest);
        }
    }
    
}
