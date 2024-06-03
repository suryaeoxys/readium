<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use App\Helper\Helper;
use App\Models\User;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $q = Notification::select('title', 'body')->where('is_admin',true)->groupBy(['title', 'body']);
        if($request->items){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }

        $data['data'] =  $q->paginate($data['items']);
        return view('admin.notification.index', $data);
    }

 

    public function store(Request $request)
    {   
        $request->validate([
        'title' => 'required',
        'body' => 'required',
        ]);
        // $users = User::userDistance($request->latitude, $request->longitude, $request->notificationRange);
        $users = User::where('id', '!=', Auth::id())->where('status',1)->whereNotNull('device_token')->get();
        // $users = User::all();
        
        foreach($users as $item){
            $notification = Notification::create([
                'user_id'  => $item->id,
                'title' => $request->title,
                'body'  => $request->body ,
                'is_admin'  => true ,
            ]);

            $userData = User::where('id',$item->id)->first();
            if(!empty($userData->device_token) && isset($userData->device_token)){
                Helper::fireBasePushNotification($userData->id,NULL,'Admin',NULL,$notification['title'],$notification['body']);
            }
        }
        return redirect()->route('admin.notifications.index');
    }

    public function create()
    {
        $data['user'] = User::all();
        $data['range'] = Helper::deliveryRange();
        return view('admin.notification.create',$data);
    }
    


    
}
