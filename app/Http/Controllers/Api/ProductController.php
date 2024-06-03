<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Helper\ResponseBuilder;
use App\Models\Product;
use App\Helper\Helper;
use Carbon\Carbon;
use Auth;
use DB;

class ProductController extends Controller
{

    /**
     * Display All Media Post list .
     *
     * @return \Illuminate\Http\Response
     */

    public function allProducts(Request $request)
    {
        try {
            $paginate = isset($request->pagination) ? $request->pagination : 10;
            $data = Product::orderBy('created_at', 'desc')->paginate($paginate);

            if ($data->count() > 0) {
                $this->response = new ProductCollection($data);

                return ResponseBuilder::successWithPagination($data,$this->response,trans('global.all_media_post'), $this->success);
                // return ResponseBuilder::success(trans('global.all_recommandation'), $this->success, $this->response);
            }

            return ResponseBuilder::successWithPagination($data,$this->response, trans('global.no_media_post'), $this->success);

        } catch (\Exception $e) {
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
            // return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
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
    
}