<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\ResponseBuilder;
use App\Models\Notification;
use App\Helper\Helper;
use App\Http\Resources\Admin\NotificationCollection;
use Auth;
class NotificationController extends Controller
{

    /**
     * Display notification list of auth user .
     *
     * @return \Illuminate\Http\Response
     */
    public function notificationList(){
        try {
            $user = Auth::guard('api')->user();
            $data = Notification::getNotificationByuser($user->id);
            foreach($data as $notification_data) {
                $notification_data->seen = 1;
                $notification_data->save();
            }
            $data = new NotificationCollection($data);
            
            $notify = Notification::where('user_id', $user->id)->where('seen', 0)->first();

            if ($notify) {
                $notification = 'true';
            }else{
                $notification = 'false';
            }
            
            Helper::fireBasePushNotification($user->id,"", "", "", "", "");
            $this->response->notificationData = $data;
            $this->response->notification = $notification;

            return ResponseBuilder::success(trans('global.notification_list'), $this->success,$this->response);
        } catch (\Exception $e) {
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }
}
