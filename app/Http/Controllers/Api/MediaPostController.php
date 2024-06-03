<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MediaPostResource;
use App\Http\Resources\Admin\ReviewCollection;
use App\Http\Resources\Admin\MediaPostCollection;
use App\Http\Resources\Admin\MyPostCollection;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\ReportPost;
use App\Models\User;
use App\Helper\ResponseBuilder;
use App\Models\MediaPost;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Helper\Helper;
use App\Models\Notification;
use App\Mail\NewSignUp;
use Carbon\Carbon;
use Auth;
use File;
use DB;

class MediaPostController extends Controller
{

    /**
     * Auth User add Media Post 
     *
     * @return \Illuminate\Http\Response
     */

     public function addMediaPost(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            $validSet = [
                'content' => 'required',
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            $imagePath = config('app.user_post_image');
            $videoPath = config('app.user_post_video');

            $oldImageName = basename($request->imageOld);
            $newImageName = basename($request->imageOld);
            $oldVideoName = basename($request->videoOld);
            $newVideoName = basename($request->videoOld);

            if($request->hasFile('image')) {
                // Handle image upload
                $newImageName = time().'-'.$request->image->getClientOriginalName();
                $request->image->move($imagePath, $newImageName);
            } elseif(isset($request->imageOld)) {
                // Copy old image if exists
                if(File::exists(config('app.user_post_image').'/'.$oldImageName)) {
                    $newImageName = time().'-'.$oldImageName;
                    File::copy(config('app.user_post_image').'/'.$oldImageName, $imagePath.'/'.$newImageName);
                }
            }

            if($request->hasFile('video')) {
                // Handle video upload
                $newVideoName = time().'-'.$request->video->getClientOriginalName();
                $request->video->move($videoPath, $newVideoName);
            } elseif(isset($request->videoOld)) {
                // Copy old video if exists
                if(File::exists(config('app.user_post_video').'/'.$oldVideoName)) {
                    $newVideoName = time().'-'.$oldVideoName;
                    File::copy(config('app.user_post_video').'/'.$oldVideoName, $videoPath.'/'.$newVideoName);
                }
            }

            $data = MediaPost::updateOrCreate(
                [
                    'id' => $request->id,
                ],
                [
                    'user_id' => $user->id,
                    'content' => $request->content,
                    'link' => $request->link,
                    'image' => $newImageName,
                    'video' => $newVideoName,
                ]
            );

            if($request->tag_id) {
                $tagIds = explode(',', $request->tag_id);
                $data->viewers()->sync($tagIds);       
            }
            
            $this->response = new MediaPostResource($data);
            return ResponseBuilder::success($request->id ? trans('global.MEDIA_POST_UPDATED') : trans('global.MEDIA_POST_ADD'), $this->success, $this->response);

        } catch (\Exception $e) {
            
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'), $this->badRequest);
        }
    }

    /**
     * Display All Media Post list .
     *
     * @return \Illuminate\Http\Response
     */

    public function allMediaPost(Request $request)
    {
        try {
            $paginate = isset($request->pagination) ? $request->pagination : 10;
            $data = MediaPost::with('postUser', 'likes', 'comments')->whereNotNull('content')->orderBy('created_at', 'desc')->paginate($paginate);

            if ($data->count() > 0) {
                $this->response = new MediaPostCollection($data);

                return ResponseBuilder::successWithPagination($data,$this->response,trans('global.all_media_post'), $this->success);
                // return ResponseBuilder::success(trans('global.all_recommandation'), $this->success, $this->response);
            }

            return ResponseBuilder::successWithPagination($data,$this->response, trans('global.no_media_post'), $this->success);

        } catch (\Exception $e) {
            return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * Delete a Media Post by its id .
     *
     * @return \Illuminate\Http\Response
     */

     public function deleteMediaPost(Request $request)
     {
         try {
             $user = Auth::guard('api')->user();
             
             $validSet = [
                 'post_id' => 'required|exists:media_post,id',
             ]; 
     
             $isInvalid = $this->isValidPayload($request, $validSet);
             if($isInvalid){
                 return ResponseBuilder::error($isInvalid, $this->badRequest);
             }
     
             $mediaPost = MediaPost::where('id', $request->post_id)
                                   ->Where('user_id', $user->id)
                                   ->exists();
     
             if (!$mediaPost) {
                 return ResponseBuilder::error(trans('global.INVALID_POST_ID'), $this->badRequest);
             }
     
             MediaPost::where('id', $request->post_id)
                      ->orWhere('repost_id', $request->post_id)
                      ->delete();
     
             return ResponseBuilder::successMessage(trans('global.media_post_removed'), $this->success); 
     
         } catch (\Exception $e) {
             return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(), $this->badRequest);
         }
     }
     

    /**
     * Display Single Media Post by providing post_id .
     *
     * @return \Illuminate\Http\Response
     */
    public function singelMediaPost(Request $request) {
        try {
            $users = Auth::guard('api')->user();
            $validSet = [
                'post_id'   => 'required'
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            $mediaPost = MediaPost::where('id', $request->post_id)->first();
            
            $data['media_post'] = new MediaPostResource($mediaPost);

            if($mediaPost->user_id == $users->id){
                $data['my_account'] = true;
            }else {
                $data['my_account'] = false;
            }
           
            return ResponseBuilder::successMessage(trans('global.recommandation'), $this->success,$data); 
        } catch (\Exception $e) {
            // return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * setAuthResponse function .
     *
     * @return \Illuminate\Http\Response
     */
    public function setAuthResponse($user) {
        return $this->response->user =  new UserResource($user);
    }
    
    public function repost(Request $request){
        $validSet = [
            'post_id'   => 'required|exists:media_post,id'
        ]; 

        $isInValid = $this->isValidPayload($request, $validSet);
        if($isInValid) return ResponseBuilder::error($isInValid, $this->badRequest);

        MediaPost::create([
            'user_id'   => auth()->id(),
            'repost_id'   => $request->post_id
        ]);
        return ResponseBuilder::successMessage('Reposted successfully', $this->success); 
    }


    public function reportPost(Request $request){
        $validSet = [
            'post_comment_id'   => 'required',
            'type'      => 'required|in:post,comment'
        ]; 

        $isInValid = $this->isValidPayload($request, $validSet);
        if($isInValid) return ResponseBuilder::error($isInValid, $this->badRequest);
        ReportPost::create([
            'user_id'   => auth()->id(),
            'post_comment_id' => $request->post_comment_id,
            'type'      => $request->type
        ]);
        return ResponseBuilder::successMessage('Post reported successfully', $this->success); 
    }

    // public function reportOnComment(Request $request){
    //     try{
    //         $user = Auth::guard('api')->user();
    //         $comment = Comment::where('id',$request->comment_id)->first();
    //         $users = User::where('id',$comment->user_id)->first();
 
    //         $setting = Setting::getAllSettingData();
    //         $admin_mail = $setting['admin_mail'];
           
    //         if(!empty($comment)){
    //             $mailData = EmailTemplate::getMailByMailCategory(strtolower('report admin'));
    //             if(isset($mailData)) {
   
    //                 $arr1 = array('{reportingUser}','{commentingUser}', '{comment}');
    //                 $arr2 = array($user->name, $users->name, $comment->comment);
   
    //                 $email_content = $mailData->email_content;
    //                 $email_content = str_replace($arr1, $arr2, $email_content);
               
    //                 $config = [
    //                     'from_email' => isset($mailData->from_email) ? $mailData->from_email : env('MAIL_FROM_ADDRESS'),
    //                     'name' => isset($mailData->from_name) ? $mailData->from_name : env('MAIL_FROM_NAME'),
    //                     'subject' => $mailData->email_subject,
    //                     'message' => $email_content,
    //                 ];
                   
    //                 Mail::to($admin_mail)->send(new NewSignUp($config));
    //             }
    //             return ResponseBuilder::successMessage(trans('global.REPORTED'),$this->success);
    //         }
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
    // }

    public function myPost()
    {
        $data = MediaPost::where('user_id', auth()->id())->latest()->get();
        $this->response = new MyPostCollection($data);
        return ResponseBuilder::successMessage('My post list', $this->success, $this->response); 
    }
    

}