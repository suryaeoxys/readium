<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryCollection;
use App\Http\Resources\Admin\CategoryResource;
use Illuminate\Http\Request;
use App\Helper\ResponseBuilder;
use App\Models\Category;
use App\Models\User;
use Auth;
use Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {
            $pagination = isset($request->pagination) ? $request->pagination : 12;
            $data = Category::where('status', 1)->paginate($pagination); 
            // $data = Category::where('status', 1)->get(); 
            if(count($data) > 0) {
                $this->response = new CategoryCollection($data);

                // return ResponseBuilder::successMessage(trans('global.all_categories'), $this->success, $this->response);
                return ResponseBuilder::successWithPagination($data, $this->response, trans('global.all_categories'), $this->success);
            }
            // return ResponseBuilder::successMessage(trans('global.no_categories'), $this->success, $this->response);
            return ResponseBuilder::successWithPagination($data, [], trans('global.no_categories'), $this->success);

        } catch (\Exception $e) {
            return ResponseBuilder::error(trans('global.SOMETHING_WENT'),$this->badRequest);
        }
    }

}
