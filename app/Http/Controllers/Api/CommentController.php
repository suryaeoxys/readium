<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CommentResource;
use App\Http\Resources\Admin\CommentCollection;
use App\Http\Resources\Admin\BlockUserCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Helper\ResponseBuilder;
use App\Helper\Helper;
use App\Models\MediaPost;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\BlockUser;
use App\Mail\NewSignUp;
use Auth;
use Carbon\Carbon;
use DB;

class CommentController extends Controller
{

    /**
     * Add Comment on social media post
     * @return \Illuminate\Http\Response
     */
    public function addComment(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            // Validation start
            $validSet = [
                'post_id' => 'required | integer',
                'comment' => 'required'
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            $post = MediaPost::find($request->post_id);
    
            if (!$post) {
                return ResponseBuilder::error(trans('global.INVALID_POST_ID'), $this->success);
            }

            $data = Comment::updateOrCreate(
                [
                    'id' => $request->id,
                ],
                [
                    'user_id' => $user->id,
                    'post_id' => $request->post_id,
                    'comment' => $request->comment
                ]
            );

            if(!empty($post)){
                $arr1 = array('{user}','{askRecommendation}');
                $arr2 = array($user->name,$post->title);
                $msg = str_replace($arr1, $arr2, trans('notifications.COMMENT_ADD'));

                $notification = Notification::create([
                    'user_id' => $post->user_id,
                    'post_id' => $post->id,
                    'title' => 'New Comment',
                    'body' => $msg,
                ]);

                Helper::fireBasePushNotification($post->user_id, $post->id, 'Comment',$msg);
            }

            $this->response = new CommentResource($data);
            return ResponseBuilder::success($request->id ? trans('global.COMMENT_UPDATED') : trans('global.COMMENT_ADD'), $this->success, $this->response);

        } catch (\Exception $e) {
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
            // return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
        }
    }

    /**
     * Comments list on behalf of post id
     * @return \Illuminate\Http\Response
     */
    public function getComments(Request $request)
    {
        try{
            // Validation start
            $validSet = [
                'post_id' => 'required | integer',
            ];

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            $comment = Comment::where('post_id',$request->post_id)->get();

            $this->response = new CommentCollection($comment);
            return ResponseBuilder::success(trans('global.COMMENT_LIST'), $this->success, $this->response);
        } catch (\Exception $e) {
            // return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * block/unblock User Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function blockUser(Request $request) {
        DB::beginTransaction();
        try {
            $user = Auth::guard('api')->user();

            $validSet = [
                'block_user_id' => 'required | integer',
            ]; 
            $isInValid = $this->isValidPayload($request, $validSet);
            
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            if(isset($request->block_user_id)) {
                $blockedUser = User::find($request->block_user_id);
                if(!$blockedUser) {
                    return ResponseBuilder::error(trans('global.INVALID_USER_ID'),$this->success);
                }
            }

            $blocked = BlockUser::getDataByUserId($user->id, $request->block_user_id);

            if($blocked) {
                $blocked->delete();
                DB::commit();
                return ResponseBuilder::successMessage(trans('global.USER_UNBLOCKED'), $this->success); 
            }

            BlockUser::create([
                'user_id' => $user->id,
                'block_user_id' => $request->block_user_id
            ]);

            DB::commit();
            return ResponseBuilder::successMessage(trans('global.USER_BLOCKED'), $this->success); 
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }


    /**
     * blocked users list Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function blockedUserList(Request $request) {
        try {
            $user = Auth::guard('api')->user();
            $blocked = BlockUser::getIdByUser($user->id);
            
            $this->response = new BlockUserCollection($blocked);
    
            return ResponseBuilder::success(trans('global.BLOCKED_USER'), $this->success, $this->response);
        } catch (\Exception $e) {
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'), $this->badRequest);
        }
    }

    /**
     * delete comment of auth user on behalf of comment id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteComment(Request $request){
        try{
            $user = Auth::guard('api')->user();
            $comment = Comment::where('id',$request->comment_id)->where('user_id',$user->id)->first();
            if(!empty($comment)){
                $comment->delete();
                return ResponseBuilder::successMessage(trans('global.COMMENT_DELETED'), $this->success);
            }
            else
                return ResponseBuilder::error(trans('global.INVALID_COMMENT'),$this->badRequest);
        }catch(\Exception $e){
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'), $this->badRequest);
        }
    }

    public function reportOnComment(Request $request){
        try{
            $user = Auth::guard('api')->user();
            $comment = Comment::where('id',$request->comment_id)->first();
            $users = User::where('id',$comment->user_id)->first();
 
            $setting = Setting::getAllSettingData();
            $admin_mail = $setting['admin_mail'];
           
            if(!empty($comment)){
                $mailData = EmailTemplate::getMailByMailCategory(strtolower('report admin'));
                if(isset($mailData)) {
   
                    $arr1 = array('{reportingUser}','{commentingUser}', '{comment}');
                    $arr2 = array($user->name, $users->name, $comment->comment);
   
                    $email_content = $mailData->email_content;
                    $email_content = str_replace($arr1, $arr2, $email_content);
               
                    $config = [
                        'from_email' => isset($mailData->from_email) ? $mailData->from_email : env('MAIL_FROM_ADDRESS'),
                        'name' => isset($mailData->from_name) ? $mailData->from_name : env('MAIL_FROM_NAME'),
                        'subject' => $mailData->email_subject,
                        'message' => $email_content,
                    ];
                   
                    Mail::to($admin_mail)->send(new NewSignUp($config));
                }
                return ResponseBuilder::successMessage(trans('global.REPORTED'),$this->success);
            }
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
