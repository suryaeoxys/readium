<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MediaPost;
use App\Models\Tax;
use App\Models\Comment;
use Illuminate\Support\Str;
use App\Helper\Helper;

class MediaPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $q = MediaPost::query();

        if ($request->keyword) {
            $data['keyword'] = $request->keyword;
    
            $q->where('content', 'like', '%' . $data['keyword'] . '%');
                // ->orWhere('review', 'like', '%' . $data['keyword'] . '%');
        }

        if($request->items){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }

        $data['data'] = $q->withCount('comments')->orderBy('created_at','DESC')->paginate($data['items']);
        return view('admin.media-post.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $data['tax'] = Tax::getAllActiveTaxes();
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required | string | unique:categories,name,'.$request->id,
                'admin_commission' => 'required_with:admin_commission_type',
                'status' => 'required',
            ] + (!empty($request->id) ? ['image' => 'mimes:jpeg,png,jpg'] : ['image' => 'required | mimes:jpeg,png,jpg']),
            [
                'admin_commission.required_with' => 'The Readium share field is required when Readium share type is present.',
            ]
        );
        
        // if(empty($request->slug)){
        //     $request['slug'] = Str::slug($request->title);
        //     $request['new_slug'] = $request['slug'];

        //     $count=1;
        //     while(Category::where('slug', '=', $request['new_slug'])->exists())
        //         {
        //             $request['new_slug'] = $request['slug'].'-'.$count;
        //             $count++;
        //         }
        // }
        // else
        // {
        //     $request['new_slug'] = $request->slug;
        // }

        $imagePath = config('app.category_image');
        $categories = Category::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'name' => $request->title,
                // 'slug' => $request['new_slug'],
                'image' => $request->hasfile('image') ? Helper::imageThumbnail($request->file('image'),$imagePath,$height = 224 ,$width = 225,$request->imageOld) : (isset($request->imageOld) ? $request->imageOld : ''),
                // 'tax_id' => $request->tax_percent,
                // 'hsn_code' => $request->hsn_code,
                // 'admin_commission_type' => $request->admin_commission_type,
                // 'admin_commission' => $request->admin_commission_type ? $request->admin_commission : NULL,
                'status' => $request->status,
            ]
        );

        $result = $categories->update();

        if($result)
        {
            return redirect()->route('admin.categories.index');
        }
        else
        {
            return redirect()->back()->with('error', 'Something went Wrong, Please try again!');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['data'] = Category::where('id',$id)->first();

        return view('admin.category.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['data'] = Category::where('id', $id)->first();
        // $data['tax'] = Tax::getAllActiveTaxes();

        return view('admin.category.create',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data= Category::where('id',$id)->first();
            $result = $data->delete();
            if($result) {
                return response()->json(["success" => true]);
            }
            else {
                return response()->json(["success" => false]);
            }
        }  catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message'  => "Something went wrong, please try again!",
                'error_msg' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Change the specified resource status from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id, Request $request)
    {
        try {
            $data= Category::where('id',$id)->first();
            if($data) {
                $data->status = $data->status == 1 ? 0 : 1;
                $data->save();
                return response()->json(["success" => true, "status"=> $data->status]);
            }
            else {
                return response()->json(["success" => false]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'  => "Something went wrong, please try again!",
                'error_msg' => $e->getMessage(),
            ], 400);
        }
    }
}
