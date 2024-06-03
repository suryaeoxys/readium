<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Admin\SliderCollection;
use App\Http\Resources\Admin\ProductCollection;
use App\Http\Resources\Admin\MediaPostCollection;
use App\Http\Resources\Admin\CategoryCollection;
use App\Http\Resources\Admin\UserCollection;
use App\Http\Controllers\Controller;
use App\Helper\ResponseBuilder;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use App\Models\Notification;
use Auth;

class HomeController extends Controller
{
    /**
     * Display Search Result .
     *
     * @return \Illuminate\Http\Response
     */
    public function searchResult(Request $request){
        try {
            $user = Auth::guard('api')->user();
            
            $pagination = $request->pagination ?? 10;
            
            // Validation start
            $validSet = [
                'type'    => 'required|in:socialMedia,library',
                'keyword' => 'required'
            ]; 
            
            $isInValid = $this->isValidPayload($request, $validSet);
            if($isInValid){
                return ResponseBuilder::error($isInValid, $this->badRequest);
            }
            // Validation end
            if($request->type == "library"){
                $data = Product::getUser($request->keyword);
                $this->response = new ProductCollection($data);
            }else{
                $data = MediaPost::getMediaPost($request->keyword);
                $this->response = new MediaPostCollection($data);
            }
            if(empty($data)) {
                return ResponseBuilder::successWithPagination($data, [], trans('global.no_data_found'), $this->success);
            }
            return ResponseBuilder::successWithPagination($data, $this->response, trans('global.data'), $this->success);
        } catch (\Exception $e) {
            // return ResponseBuilder::error($e->getMessage().' at line '.$e->getLine() .' at file '.$e->getFile(),$this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    public function appVersion(Request $request)
    {
        $valid = [
            'version_code' => 'required'
        ];

        $isInvalid = $this->isValidPayload($request, $valid);
        if ($isInvalid) {
            return ResponseBuilder::error($isInvalid, $this->badRequest);
        }

        try {
            $setting = Setting::where('key', 'app_version')->update(['value' => $request->version_code]);
            return ResponseBuilder::successMessage('Version Updated', $this->success);
        } catch (\Throwable $e) {
            // return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

    public function getVersion(Request $request)
    {
        $valid = [
            'version_code' => 'required',
            'type' => 'required|in:ios,android'
        ];

        $isInvalid = $this->isValidPayload($request, $valid);
        if ($isInvalid) {
            return ResponseBuilder::error($isInvalid, $this->badRequest);
        }

        try {
            // $content = Setting::where('key', 'update_content')->pluck('value')->first();
            if($request->type == 'ios'){
                $setting = Setting::where('key', 'app_version_ios')->first();
                $link = 'https://apps.apple.com/in/app/recs/id6473147636';
            }else{
                $setting = Setting::where('key', 'app_version')->first();
                $link = 'https://apps.apple.com/in/app/recs/id6473147636';
            }
            
            if (isset($setting) && $setting->value == $request->version_code) {
                return ResponseBuilder::successMessage('Success', $this->success,['link' => $link, 'is_updated' => true]);
            }else{
                return ResponseBuilder::successMessage('Success', $this->success,['link' => $link, 'is_updated' => false]);
            }
        } catch (\Throwable $e) {
            // return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }
}
