<?php

namespace App\Helper;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Coupon;
use App\Models\WalletTransaction;
use App\Models\CouponInventory;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Setting;
use Salman\GeoCode\Services\GeoCode;
use PDF;
class Helper
{
    public static function fireBasePushNotification($user_id,$postID, $notification_title, $notification_body)
    {
        try {
            $getToken = User::getUserById($user_id);
            $getToken->device_token;
            $firebaseToken[]= $getToken->device_token;
            
            $SERVER_API_KEY = env('FIREBASE_SERVER_KEY');
            
            $notification_count = Notification::where(['user_id' => $user_id, 'seen' => 0])->count();
            $data = [
                "registration_ids" => $firebaseToken,
                "notification" => [
                    "title" => $notification_title,
                    "body"  => strip_tags($notification_body),
                    "badge" => $notification_count,
                ],
                "data" => [
                    "post_id" => $postID,
                    // "post_type" => $postType,
                    // "parent_id" => $parentID,
                ],
            ];

            $dataString = json_encode($data);
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            
            $response = curl_exec($ch);
            return $response;

            } catch (\Exception $e) {
                return false;
            }
    }

    public static function pushNotification($data,$userID,$title = null,$orderID = null, $notification_type = null)
    { 
       try {
        $userName = !empty(User::getNameById($userID)) ? User::getNameById($userID) :'User';
        $arr1 = array('{user}','{orderNo}');
        $arr2 = array($userName->name,$orderID);
        $msg = str_replace($arr1, $arr2, $data);
   
        return Helper::createNotification($userID,$title,$msg, $notification_type = null);
         
        
        } catch (\Exception $e) {
            return false;
        }
      
    }
    public static function genrateOrderInvoice($orderID)
    {   
        try {
            $data['data'] = Order::where('id', $orderID)->with('orderItem','orderItem.products','coupon')->first();
            $data['numberToWord'] = Helper::numberToWord($data['data']->grand_total);
            $logo = Setting::getDataByKey('logo_1');
            $data['fssai_admin'] = Setting::getDataByKey('fssai')->value ?? '';
            $data['gst_no_admin'] = Setting::getDataByKey('gst_no')->value ?? '';
            $data['cin_no_admin'] = Setting::getDataByKey('cin_no')->value ?? '';
            $data['pan_no_admin'] = Setting::getDataByKey('pan_no')->value ?? '';
            $data['logo'] = url(config('app.logo').'/'.$logo->value);
            $data['sign'] = url(config('app.logo').'/signature.png');
            $delivery_address = '';
            if(isset($data['data']->orderAddress)) {
                if(!empty($data['data']->orderAddress->flat_no)) {
                    $delivery_address .= $data['data']->orderAddress->flat_no.', ';
                }
                if(!empty($data['data']->orderAddress->street)) {
                    $delivery_address .= $data['data']->orderAddress->street.', ';
                }
                if(!empty($data['data']->orderAddress->landmark)) {
                    $delivery_address .= $data['data']->orderAddress->landmark.', ';
                }
                if(!empty($data['data']->orderAddress->location)) {
                    $delivery_address .= $data['data']->orderAddress->location;
                }
            }
            $data['deliveyAddress'] = $delivery_address;
            $getPoints = new GeoCode();
            $getLatiLong = $getPoints->getLatAndLong($data['data']->vendor->vendor->location);
            $data['address_array'] = explode(',', $getLatiLong['formatted_address']);

            $filename = 'invoice-'.$orderID.'-'.time().'.pdf';
            $pdf = PDF::loadView('invoice/order-invoice', $data)->save("uploads/pdf/$filename");
            return $filename ;
            } catch (\Exception $e) {
                return '';
            }
    }

    public static function numberToWord($num = '')
    {
        $num    = ( string ) ( ( int ) $num );
        
        if( ( int ) ( $num ) && ctype_digit( $num ) )
        {
            $words  = array( );
             
            $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
             
            $list1  = array('','one','two','three','four','five','six','seven',
                'eight','nine','ten','eleven','twelve','thirteen','fourteen',
                'fifteen','sixteen','seventeen','eighteen','nineteen');
             
            $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
                'seventy','eighty','ninety','hundred');
             
            $list3  = array('','thousand','million','billion','trillion',
                'quadrillion','quintillion','sextillion','septillion',
                'octillion','nonillion','decillion','undecillion',
                'duodecillion','tredecillion','quattuordecillion',
                'quindecillion','sexdecillion','septendecillion',
                'octodecillion','novemdecillion','vigintillion');
             
            $num_length = strlen( $num );
            $levels = ( int ) ( ( $num_length + 2 ) / 3 );
            $max_length = $levels * 3;
            $num    = substr( '00'.$num , -$max_length );
            $num_levels = str_split( $num , 3 );
             
            foreach( $num_levels as $num_part )
            {
                $levels--;
                $hundreds   = ( int ) ( $num_part / 100 );
                // $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
                $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' : '' );
                $tens       = ( int ) ( $num_part % 100 );
                $singles    = '';
                 
                if( $tens < 20 ) { $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens = ( int ) ( $tens / 10 ); $tens = ' ' . $list2[$tens] . ' '; $singles = ( int ) ( $num_part % 10 ); $singles = ' ' . $list1[$singles] . ' '; } $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' ); } $commas = count( $words ); if( $commas > 1 )
            {
                $commas = $commas - 1;
            }
             
            $words  = implode( ', ' , $words );
             
            $words  = trim( str_replace( ' ,' , ',' , ucwords( $words ) )  , ', ' );
            if( $commas )
            {
                $words  = str_replace( ',' , ' and' , $words );
            }
             
            return $words.' Only';
        }
        else if( ! ( ( int ) $num ) )
        {
            return 'Zero';
        }
        return '';
    }
    // static public function createNotification($userId,$title=null,$body=null,$notification_type=null)
    static public function createNotification($userId, $notification_title, $notification_body)
    {
       if(!isset($userId) || empty($userId)){
            return false;
       }
       $notification = Notification::create([
        'user_id'           => $userId,
        'title'             => $notification_title,
        'body'              => $notification_body,
        // 'notification_type' => $notification_type,
       ]);
       
       
       $getToken = User::getUserById($notification->user_id);
       
       $firebaseToken[]= $getToken->device_token;
       
       $SERVER_API_KEY = env('FIREBASE_SERVER_KEY');
       
       
       $data = [
           "registration_ids" => $firebaseToken,
           "notification" => [
               "title" => $notification->title,
               "body"  => $notification->body,
               "sound" => "default",
               ]
            ];
            
            $dataString = json_encode($data);
            
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            
            $response = curl_exec($ch);
            return $response;
    }
    public static function generateReferCode()
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    
        return strtoupper(substr(str_shuffle($str_result),0, 10));
    }
    
    public static function createTransaction($userID,$previousBalance,$currentBalance,$amount,$status,$remark,$orderId=null,$user_type='C')
    {
       if(!empty($userID) && !empty($previousBalance) && !empty($currentBalance) && !empty($amount) && !empty($status)) {
            $walletTransaction = WalletTransaction::create([
                'user_id'                => $userID,
                'previous_balance'       => $previousBalance,
                'current_balance'        => $currentBalance,
                'amount'                 => $amount,
                'status'                 => $status,
                'order_id'               => $orderId ?? null,
                'remark'                 => $remark ?? '',
                'user_type'              => $user_type ?? 'C',
            ]);
            return $walletTransaction;
       }
       return false;
    }
    // public static function storeImage($image, $destinationPath, $old_image = null,$user=null)
    // {
    //     try {
    //         if(!empty($old_image)) {
    //             if(File::exists($destinationPath.'/'.$old_image)) {
    //                 unlink($destinationPath.'/'.$old_image);
    //             }
    //         }
         
    //         // $file = $image;
    //         // $name =time().'-'.$file->getClientOriginalName();
    //         // $file->move($destinationPath, $name);
    //         $file = $image;
    //         $originalName = $file->getClientOriginalName();
    //         $name = time() . '-' . str_replace(' ', '', $originalName); // Remove spaces from the original file name
    //         $file->move($destinationPath, $name);
    //         if(!is_null($user)){
    //             $path =  config('app.crop_profile_image').'/'.$user->id;
    //             // dd($path);
    //             self::resizeImage($image,50,50,$path);
    //         }
    //         return $name;
    //     } catch (\Exception $e) {
    //         return 0;
    //     }
    // }
    // public static function resizeImage($imagePath, $width, $height, $destinationPath)
    // {
    //     $image = \Intervention\Image\Facades\Image::make($imagePath);
    //     $image->resize($width, $height);

    //     if ($destinationPath) {
    //         $image->save($destinationPath);
    //         // return $destinationPath;
    //     }
    //     dd($imagePath);

    //     // return $image->encode('data-url');
    // }

    // public static function storeImage($image, $destinationPath, $old_image = null, $user = null)
    // {
    //     // dd($destinationPath);
    //     try {
    //         if (!empty($old_image)) {
    //             $oldImagePath = $destinationPath . '/' . $old_image;
    //             if (File::exists($oldImagePath)) {
    //                 unlink($oldImagePath);
    //             }
    //         }

    //         $originalName = $image->getClientOriginalName();
    //         $name = time() . '-' . str_replace(' ', '', $originalName);

    //         $image->move($destinationPath, $name);
            
    //         if (!is_null($user)) {
    //             $resizedName = 'resized_' . $name;
    //             $path = config('app.crop_profile_image') . '/' . $user->id . '/' . $name;

    //             if (!File::isDirectory(dirname($path))) {
    //                 File::makeDirectory(dirname($path), 0755, true, true);
    //             }

    //             // $imageCopy = Image::make($destinationPath . '/' . $name)->resize(50, 50);
    //             $imageCopy = Image::make($destinationPath . '/' . $name);
    //             $imageCopy->resize(50, null, function ($constraint) {
    //                 $constraint->aspectRatio();
    //             });

    //             $imageCopy->save($path);
    //         }

    //         return $name;
    //     } catch (\Exception $e) {
    //         \Log::error("Error storing image: " . $e->getMessage());
    //         return false;
    //     }
    // }
    public static function storeImage($image, $destinationPath, $old_image = null)
    {
        try {
            if(!empty($old_image)) {
                if(File::exists($destinationPath.'/'.$old_image)) {
                    unlink($destinationPath.'/'.$old_image);
                }
            }
            $file = $image;
            $name =time().'-'.$file->getClientOriginalName();
            $file->move($destinationPath, $name);
            
            return $name;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function uploadFile($file, $targetDirectory)
    {
        try {
            $imageName = time().'-'.$file->getClientOriginalName();
            $file->move($targetDirectory, $imageName);
            return $imageName;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    


    public static function imageThumbnail($image, $destinationPath, $old_image = null)
    {
        try {
            if (!empty($old_image)) {
                if (File::exists($destinationPath.'/'.$old_image)) {
                    unlink($destinationPath.'/'.$old_image);
                }
            }
    
            $imageName = time().'-'.$image->getClientOriginalName();
    
            $image->move($destinationPath, $imageName);
    
            return $imageName;
    
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    


    public static function generateOtp()
    {
        return rand(1111,9999);
    }
    public static function dayFromNumber($day)
    {

        $days = [
           '1' => 'Monday',
           '2' => 'Tuesday',
           '3' => 'Wednesday',
           '4' => 'Thursday',
           '5' => 'Friday',
           '6' => 'Saturday',
           '7' => 'Sunday'
        ];

        return $days[$day];

    }
    public static function userCartClear($userID)
    {
        $userCart=Cart::where('user_id',$userID)->first();
        if(!empty($userCart)){
            $userCart->delete();
        }
        $getCartDetails=CartDetail::where('user_id',$userID)->delete();
        return true;
    }



    public static function units() {
        return $units = [
            'kg' => 'kg',
            'grm' => 'grm',
            'ltr' => 'ltr',
            'ml' => 'ml',
            'dozen' => 'dozen',
            'pieces' => 'pieces',
            'Plates' => 'Plates',
            'Plate' => 'Plate',
            'Pkt' => 'Pkt',
            'Piece' => 'Piece',
        ];
    }

    public static function deliveryRange() {
        return $range = [
            '500' => '500 meter',
            '1' => '1 km',
            '2' => '2 km',
            '3' => '3 km',
            '4' => '4 km',
            '5' => '5 km',
            '6' => '6 km',
            '7' => '7 km',
            '8' => '8 km',
            '9' => '9 km',
            '10' => '10 km',
            '15' => '15 km',
            '20' => '20 km',
            '25' => '25 km',
            '30' => '30 km',
            '35' => '35 km',
            '40' => '40 km',
            '45' => '45 km',
            '50' => '50 km',
        ];
    }

    public static function notificationRange() {
        return $range = [
            '5' => '5 km',
            '10' => '10 km',
            '15' => '15 km',
            '20' => '20 km'
        ];
    }
    
    public static function distance($lat1, $lon1, $lat2, $lon2, $unit='K') {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = (float)$lon1 - (float)$lon2;
          $dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) +  cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad((float)$theta));
          $dist = acos((float)$dist);
          $dist = rad2deg((float)$dist);
          $miles = (float)$dist * 60 * 1.1515;
          $unit = strtoupper($unit);
      
          if ($unit == "K") {
            return ($miles * 1.609344);
          }
        }
    }
    
    public static function orderStatus() {
        return $status = [
            'OP' => 'Order Placed',
            'A' => 'Accepted',
            'R' => 'Reject',
            'PC' => 'Pickup',
            'RR' => 'Return Request',
            'RF' => 'Refund',
            'D' => 'Delivered',
            'P' => 'Pending',
        ];
    }
    public static function driverOrderStatus() {
        return $status = [
            'A'   => 'Pickup',
            'PC'  => 'Delivered',
        ];
    }
    public static function walletTransactionsStatus() {
        return $status = [
            'C'  => 'Credit',
            'D'  => 'Debit',
            'RF' => 'Refund',
            'W'  => 'Withdrawal',
            'E'  => 'Earn',
            'F'  => 'Failed'
        ];
    }

      public static function couponValid($userId) {
        
         /**Coupon value */
         $userCart=Cart::userTempCartData($userId);
         $userCartData=Cart::getUserCart($userId);
         $getCartVendor = '';
         $cartCost = 0; 

         foreach($userCartData as $item){
           $getCartVendor=$item->getProductData->vendor_id;
           $cartCost = $cartCost + (($item->getVariantData->price)*$item->qty);
         }

         $todayDate = date('Y-m-d');
         $getCoupon=Coupon::getCouponByVendor($userCart->coupon_code,$getCartVendor);

         /**If coupon code invalid */
         $couponValue=1;

         if(!empty($getCoupon)){

            if($getCoupon->valid_from > $todayDate || $getCoupon->valid_to < $todayDate){
                $couponValue=0;
            }
   
            $couponInventroy=CouponInventory::getCouponInventoryByUser($userId,$getCoupon->coupon_code);
            
            /**if user already used coupon */
            if($getCoupon->max_reedem<=count($couponInventroy)){
                $couponValue=0;
            }
   
            /**if Coupon usage limit has been reached */
            if($getCoupon->remainig_user==0){
                $couponValue=0;
            }
           
   
            if($getCoupon->min_order_value > $cartCost){
                $couponValue=0;
            }
         }else{
            $couponValue=0;
         }

        if($couponValue==0 && !empty($userCart)){
            $userCart->coupon_code = null;
            $userCart->save();
            return false;
        }else{
            return true;
        }
       
    }
    
    public static function vendorOrderFilter() {
        return $filter = [
            'this_week' => 'This Week',
            'last_week' => 'This Week',
            'this_month' => 'This Month',
            'last_month' => 'This Month',
            'custom' => 'Custom',
        ];
    }

    public static function encryptMessage(string $msg){
        return Crypt::encryptString($msg);
    }

    public static function decryptMessage(string $hash){
        return Crypt::decryptString($hash);
    }
}
