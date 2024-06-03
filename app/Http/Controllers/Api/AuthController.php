<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\ResponseBuilder;
use App\Helper\Helper;
use App\Models\User;
use App\Models\Setting;
use App\Models\MediaPost;
// use Craftsys\Msg91\Facade\Msg91;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\BlockUser;
use App\Models\Following;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\CategoryCollection;
use App\Http\Resources\Admin\UserCollection;
use App\Http\Resources\Admin\MediaPostCollection;
use App\Http\Resources\Admin\MyPostCollection;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Validator;
use Hash;
use Auth;
use DB;
use App\Mail\NewSignUp;

class AuthController extends Controller
{
    /**
     * User Register Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */


    public function deleteAccount(){
        $user = auth()->user();
        $user->update(['deleted_at'=> now()]);
        return ResponseBuilder::successMessage('Account deleted successfully', $this->success);
    }

    public function register(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => ['required', 'regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%]).{8,}$/'],
                'confirm_password' => ['required', 'same:password'],
                'agree_terms' => 'required|in:1',
            ],[
                'password.regex' => 'Password must contain at least 8 characters with 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character',
                'password.required' => 'Password is required', 
                'confirm_password.same' => 'The password and confirm password must match',
            ]);

            if ($validator->fails()) {
                return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'nickname' => $request->nickname,
                'password' => Hash::make($request->password),
            ]);

            $user->roles()->sync(2);

            $data_otp = Helper::generateOtp($request->email);
            $user->otp = isset($data_otp) ? $data_otp : NULL;
            $user->otp_created_at = now();
            // $user->otp_verified = 0;
            $user->save();
            // mail to user
            $mail_data = EmailTemplate::getMailByMailCategory('signup');

            $data = Setting::getDataByKey('logo_1');
            $img = url('/'.config('app.logo').'/'.$data->value);

            $arr1 = array('{image}','{otp}');
            $arr2 = array($img,$data_otp);

            $msg = $mail_data->email_content;
            $msg = str_replace($arr1, $arr2, $msg);

            $config = ['from_email' => isset($mail_data->from_email) ? $mail_data->from_email : env('MAIL_FROM_ADDRESS'),
                'name' => isset($mail_data->from_name) ? $mail_data->from_name : env('MAIL_FROM_NAME'),
                'subject' => $mail_data->email_subject, 
                'message' => $msg,
            ];

            Mail::to($request->email)->send(new NewSignUp($config));

            DB::commit();
            return ResponseBuilder::successMessage(trans('global.OTP_SENT_VARIFY'), $this->success);

        } catch (\Exception $e) {
            DB::rollback();
            // return $e;
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

     /**
     * User Login Function
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
            'password' => ['required'],
            // 'password' => ['required', 'regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%]).{8,}$/'],
        ],[
            // 'password.regex' => 'Password must contain at least 8 characters with 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character',
            'password.required' => 'Password is required', 
        ]);

        if ($validator->fails()) return ResponseBuilder::error($validator->errors()->first(), $this->badRequest);

        DB::beginTransaction();
        try {
    
            $user = User::where('email',$request->email)->where('deleted_at',null)->first();

            if(!$user) {
                return ResponseBuilder::error(trans('global.NOT_REGISTERED'), $this->badRequest);
            }

  

            if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                return ResponseBuilder::error(trans('global.INCORRECT_PASSWORD'), $this->badRequest);
            }

            $user = Auth::user();
            // $user->device_id = $request->device_id ?? null;
            // $user->device_token = $request->device_token ?? null;
            // $user->save();
            $token = auth()->user()->createToken('API Token')->accessToken;
            $this->response->user_data = new UserResource($user);

            DB::commit();
            return ResponseBuilder::successwithToken($token, $this->response, trans('global.LOGIN_SUCCESS'), $this->success);

        } catch (\Exception $e) {
            DB::rollback();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * OTP Verification
     * @param \Illuminate\Http\Request $request, phone, otp
     * @return \Illuminate\Http\Response
     */
    public function verifyOtp(Request $request) {
        try {
            // Validation start
            $validSet = [
                'email'     => 'required | exists:users,email',
                'otp'       => 'required | digits:4',
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
            // Validation end

            // $user = User::findByPhone($request->email);
            $user = User::findByEmail($request->email);

          

            if((isset($user->otp_created_at)) && ((strtotime($user->otp_created_at) + 900) < strtotime(now()))) {
                return ResponseBuilder::error(trans('global.OTP_EXPIRED'), $this->success);
            }
            if((isset($user->otp)) && ($request->otp != $user->otp)) {
                return ResponseBuilder::error(trans('global.INVALID_OTP'), $this->success);
            }
            
            $user->otp = NULL;
            $user->otp_created_at = NULL;
            $user->otp_verified_at = 1;
            $user->status = 1;
            $user->save();

            $mail_data = EmailTemplate::getMailByMailCategory('verification');

            $data = Setting::getDataByKey('logo_1');
            $img = url('/'.config('app.logo').'/'.$data->value);

            $arr1 = array('{image}');
            $arr2 = array($img);

            $msg = $mail_data->email_content;
            $msg = str_replace($arr1, $arr2, $msg);

            $config = ['from_email' => isset($mail_data->from_email) ? $mail_data->from_email : env('MAIL_FROM_ADDRESS'),
                'name' => isset($mail_data->from_name) ? $mail_data->from_name : env('MAIL_FROM_NAME'),
                'subject' => $mail_data->email_subject, 
                'message' => $msg,
            ];

            Mail::to($request->email)->send(new NewSignUp($config));
 
            $token = $user->createToken('Token')->accessToken;
            $data = $this->setAuthResponse($user);
            
            return ResponseBuilder::successwithToken($token, $data, trans('global.EMAIL_VERIFIED'), $this->success);

        } catch (\Exception $e) {
            // return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * User Resend Otp Verify Function
     * @param \Illuminate\Http\Request $request, phone, otp
     * @return \Illuminate\Http\Response
     */
    public function resendOtp(Request $request) {
        try {
            // Validation start
            $validSet = [
                'email' => 'required | email'
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
         
            $user = User::findByEmail($request->email);
            if($user) {
                // if(!$user->status) {
                //     return ResponseBuilder::error(trans('global.USER_BLOCKED'),$this->badRequest);
                // }
            
                $data_otp = $this->sendOtp($request->email);
                $user->otp = isset($data_otp['otp']) ? $data_otp['otp'] : NULL;
                $user->otp_created_at = Carbon::now();
                $user->otp_verified_at = 0;
                $user->save();

                $mail_data = EmailTemplate::getMailByMailCategory('resend otp');

                $data = Setting::getDataByKey('logo_1');
                $img = url('/'.config('app.logo').'/'.$data->value);
    
                $arr1 = array('{image}','{otp}');
                $arr2 = array($img,$data_otp['otp']);
    
                $msg = $mail_data->email_content;
                $msg = str_replace($arr1, $arr2, $msg);
    
                $config = ['from_email' => isset($mail_data->from_email) ? $mail_data->from_email : env('MAIL_FROM_ADDRESS'),
                    'name' => isset($mail_data->from_name) ? $mail_data->from_name : env('MAIL_FROM_NAME'),
                    'subject' => $mail_data->email_subject, 
                    'message' => $msg,
                ];
    
                Mail::to($request->email)->send(new NewSignUp($config));
             
                return ResponseBuilder::successMessage(trans('global.OTP_SENT'), $this->success, $data_otp); 
            }
            $data_otp = $this->sendOtp($request->email);

            return ResponseBuilder::successMessage(trans('global.OTP_SENT'), $data_otp['otp']); 

        } catch (\Exception $e) {
            // return $e;
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * User Profile Update
     * @param \Illuminate\Http\Request $request, name, email, phone
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request) {
        try {
            $user = Auth::guard('api')->user();
            // Validation start
            $validSet = [
                'nickname' => 'required',
                'email' => 'nullable | email',
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
            // Validation end

            $imagePath = config('app.profile_image');
            $profileImageOld = $user->profile_image;

            $user->first_name = isset($request->first_name) ? ucfirst($request->first_name) : '';
            $user->last_name = isset($request->last_name) ? ucfirst($request->last_name) : '';
            $user->nickname = isset($request->nickname) ? ucfirst($request->nickname) : '';
            $user->email = isset($request->email) ? $request->email : '';
            $user->profile_image = $request->hasfile('profile_image') ? Helper::storeImage($request->file('profile_image'), $imagePath, $profileImageOld,$user) : (isset($profileImageOld) ? $profileImageOld : '');
            // $user->is_complete = 1;
            $user->update();

            $data = $this->setAuthResponse($user);

            return ResponseBuilder::successMessage(trans('global.profile_updated'), $this->success, $data); 
            
        } catch (\Exception $e) {
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * User Profile
     * @return \Illuminate\Http\Response
     */
    // public function userProfile($id)
    // {
    //     try{
    //         $user = User::where('id',$id)->first();
    //         if(!empty($user)){
    //             $data['user'] = $this->setAuthResponse($user);
    //         }else{
    //             return ResponseBuilder::error(trans('global.USER_NOT_FOUND'),$this->badRequest);
    //         }
    //         return ResponseBuilder::successMessage(trans('global.profile_detail'), $this->success,$data); 
    //     }catch(\Exception $e){
    //         return $e;
    //         return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
    //     }
    // }
    public function userProfile() {
        try {
            $user = Auth::guard('api')->user(); 
            // $my_post = MediaPost::where('user_id',$user->id)->orderBy('created_at','desc')->get();

            // $data['user'] = ;
            // $data['my_post'] = new MediaPostCollection($my_post);
            $this->response->user_details = new UserResource($user);
            return ResponseBuilder::successMessage(trans('global.profile_detail'), $this->success,$this->response); 
        } catch (\Exception $e) {
            // return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * User logout Function
     * @return \Illuminate\Http\Response
     */
    public function logout() {
        try {
            if(!Auth::guard('api')->check()) {
                return ResponseBuilder::error($this->msg['LOGIN'], $this->badRequest);
            }
            
            Auth::guard('api')->user()->token()->revoke();
            
            return ResponseBuilder::successMessage('Logout successfully', $this->success); 
        } catch (\Exception $e) {
            return $e;
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'), $this->badRequest);
        }
    }


    /**
     * userResource function to provide user information
     * @return \Illuminate\Http\Response
     */
    public function setAuthResponse($user) {
        return $this->response = new UserResource($user);
    }

    /**
     * Single User Profile
     * @return \Illuminate\Http\Response
     */
    public function singleUserProfile(Request $request) {
        if(!Auth::guard('api')->check()) {
            return ResponseBuilder::error('Unauthorized', $this->unauthorized);
        }
        try {
            $authuser = Auth::guard('api')->user();
            $validSet = [
                'user_id'   => 'required|exists:users,id'
            ]; 

            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
           
            $user = User::where('id',$request->user_id)->first();
            // $blockUser = BlockUser::where('user_id',$authuser->id)->where('block_user_id',$user->id)->exists();             
            $blockUser = BlockUser::where('user_id',$authuser->id)->where('block_user_id',$user->id)->exists();             
            $my_post = MediaPost::where('user_id',$user->id)->orderBy('created_at','desc')->get();
            
            $data['user'] = $this->setAuthResponse($user);
            $data['my_post'] = new MyPostCollection($my_post);
            $data['is_block'] = $blockUser;
            $data['is_following'] = DB::table('followings')->where(['user_id' => auth()->id(), 'following_id' => $user->id])->exists();
            $data['library'] = [[
                'name'  => 'Piysuh',
                'description' => 'this is des',
                'author'    => 'Amit shen',
                'release_date' => '02/02/2005',
                'edition'   => '10th',
                'image'     => 'https://readium.eoxyslive.com/uploads/logo/1712842999-Readium%20Logo.png'
            ]];
            
            return ResponseBuilder::successMessage(trans('global.profile_detail'), $this->success,$data); 
        } catch (\Exception $e) {
            return $e->getFile();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * All Users
     * @return \Illuminate\Http\Response
     */

    public function allUsers(Request $request)
    {
        try{
            $userId = Auth::guard('api')->user();
            $user = User::where('id','!=',1)->where('id', '!=', $userId->id)->latest()->whereNotNull('nickname')->get();
            // $user = User::where('id','!=',1)->where('id', '!=', $userId->id)->latest()->whereNotNull('name')->paginate($request->pagination ?: 10);
            if(!empty($user)){
                $this->response = new UserCollection($user);
                return ResponseBuilder::success(trans('global.all_users'),$this->success, $this->response);
                // return ResponseBuilder::successWithPagination($user, $this->response, trans('global.all_users'), $this->success);
            }
            return ResponseBuilder::error(trans('global.no_users'),$this->badRequest);
        } catch(\Exception $e){
            // return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * Delete Auth User
     * @return \Illuminate\Http\Response
     */
    public function deleteUser()
    {
        try {
            $userId = Auth::guard('api')->user();

            if ($userId) {
                // Start a database transaction
                DB::beginTransaction();

                try {
                    $user = User::where('id', $userId->id)->first();
                    $followList = Following::where('user_id', $userId)->orWhere('following_id', $userId)->get();

                    if ($user || $followList->isNotEmpty()) {
                        $user->delete();
                        foreach ($followList as $follow) {
                            $follow->delete();
                        }
                        // Commit the transaction if everything is successful
                        DB::commit();

                        return ResponseBuilder::successMessage(trans('global.USER_DELETED'), $this->success);
                    }

                    return ResponseBuilder::error(trans('global.no_user'), $this->badRequest);
                } catch (\Exception $e) {
                    // Roll back the transaction on exception
                    DB::rollBack();
                    return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
                    // return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
                }
            }

            return ResponseBuilder::error(trans('global.unauthenticated'), $this->unauthorized);
        } catch (\Exception $e) {
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
            // return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
        }
    }


    /**
     * send otp on email
     * @return \Illuminate\Http\Response
     */
    public function sendOtp($email) {
        // Generate a random OTP (you can modify the length as needed)
        $otp = mt_rand(1000, 9999); // 4-digit OTP example
    
        return ['otp' => $otp];
    }

    /**
     * notification count of auth user
     * @return \Illuminate\Http\Response
     */
    public function notificationCount(){
        try {
            $user = Auth::guard('api')->user(); 
            $notification_count = Notification::where('user_id', $user->id)->where('seen', 0)->count();
            return ResponseBuilder::successMessage(trans('Notification Count'), $this->success,$notification_count); 
        } catch (\Exception $e) {
            return $e->getMessage();
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * function to send otp on email when password forgot
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request) {
        try {
            // Validation start
            $validSet = [
                'email' => 'required | email',
            ]; 
            
            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
            // Validation end

            $user = User::findByEmail($request->email);

            if(!$user) {
                return ResponseBuilder::error(trans('global.NOT_REGISTERED'), $this->badRequest);
            }

            if(!$user->status) {
                return ResponseBuilder::error(trans('global.USER_BLOCKED'), $this->badRequest);
            }

            $data_otp = $this->sendOTP($request->email, $user);
            $user->otp = $data_otp['otp'];
            $user->otp_created_at = Carbon::now();
            $user->otp_verified_at = 0;
            $user->save();
            
            $mail_data = EmailTemplate::getMailByMailCategory('forget password otp');
            $data = Setting::getDataByKey('logo_1');
            $img = url('/'.config('app.logo').'/'.$data->value);

            $arr1 = array('{image}','{otp}');
            $arr2 = array($img,$data_otp['otp']);

            $msg = $mail_data->email_content;
            $msg = str_replace($arr1, $arr2, $msg);

            $config = ['from_email' => isset($mail_data->from_email) ? $mail_data->from_email : env('MAIL_FROM_ADDRESS'),
                'name' => isset($mail_data->from_name) ? $mail_data->from_name : env('MAIL_FROM_NAME'),
                'subject' => $mail_data->email_subject, 
                'message' => $msg,
            ];

            Mail::to($request->email)->send(new NewSignUp($config));
        
            $this->response->otp = $data_otp;
            return ResponseBuilder::success(trans('global.OTP_SENT'),$this->success, $this->response);
            
        } catch (\Exception $e) {
            return $e;
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    /**
     * Change Password API
     * @param \Illuminate\Http\Request $request, name, email, phone
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request) {
        try {
            $user = Auth::guard('api')->user();
            // Validation start
            $validSet = [
                'new_password' => ['required', 'regex:/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%]).*$/', 'string', 'min:8', 'same:confirm_password','different:old_password'],
                'confirm_password' => 'required',
            ];
            
            $errorMessage = [
                'new_password.regex' => 'Password must contain at least 8 character with 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character',
            ];

            $isInValid = $this->isValidPayload($request, $validSet, $errorMessage);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }

            $user->password = Hash::make($request->new_password);
            $user->update();

            return ResponseBuilder::successMessage(trans('global.PASSWORD_CHANGED'), $this->success); 
            
        } catch (\Exception $e) {
            return ResponseBuilder::error($e->getMessage(),$this->badRequest);
        }
    }
    
    public function resetPassword(Request $request){
        $validSet = [
            'new_password' => ['required', 'regex:/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%]).*$/', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => 'required',
            'email' => 'required|email',
        ];
        
        $errorMessage = [
            'new_password.regex' => 'Password must contain at least 8 character with 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character',
        ];

        $isInValid = $this->isValidPayload($request, $validSet, $errorMessage);
        if($isInValid) return ResponseBuilder::error($isInValid, $this->badRequest);
        $user = User::where(['email' => $request->email])->first();
        if(!$user)  return ResponseBuilder::error("User not found", $this->notFound);
        if(Hash::check($request->new_password, $user->password)) return ResponseBuilder::error("The new password must be different from the old one.", $this->badRequest);

        $user->password = Hash::make($request->new_password);
        $user->save();
        return ResponseBuilder::successMessage(trans('global.PASSWORD_RESET'), $this->success); 
    }

}