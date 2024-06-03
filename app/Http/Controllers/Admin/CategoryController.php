<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Tax;
use Illuminate\Support\Str;
use App\Helper\Helper;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    //  public function getChildCat($id){
    //     $data = Category::getChildCategoryById($id);
    //     return response()->json(['data'=>$data,'status' => 200]);
    // }
        /**
     * Get Sub Category resource from storage based on category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getChildCat($id,Request $request)
    {
        $subCategory = [];
        if($id != 0) {
            $subCategory = Category::getChildCategoryById($id);
        }
        
        if(count($subCategory)>0){
            $output= "<option value=''>Select Parent Sub Category</option>";
            foreach($subCategory as $k => $item){
                $select = (isset($request->selected_sub_category) && in_array($k, json_decode($request->selected_sub_category,1))) ? 'selected' : '';
                $output .= '<option value="'.$k.'" '.$select.'>'.$item.'</option>';
            }
        }
        else {
            $output= "<option value=''>Not Available</option>";
        }
        return response()->json(['success' => true, 'data' => $output]);
    }

    public function index(Request $request)
    {
        $q = Category::query();

        if($request->keyword){
            $data['keyword'] = $request->keyword;

            $q->where('name', 'like', '%'.$data['keyword'].'%');
        }
        if($request->type == 'category'){
            $q->where('parent_id',null);
        }else{
            $q->whereNotNull('parent_id');
        }

        if($request->status){
            $data['status'] = $request->status;

            if($request->status == 'active'){
                $q->where('status', '=', 1);
            }
            else {
                $q->where('status', '=', 0);
            }
        }

        if($request->items){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }

        $data['data'] = $q->orderBy('created_at','DESC')->paginate($data['items']);

        return view('admin.category.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $data['tax'] = Tax::getAllActiveTaxes();
        $data['parant_cal'] = Category::getParentCategory();
        return view('admin.category.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $request->id,
            'status' => 'required',
            'image' => !empty($request->id) ? 'nullable|mimes:jpeg,png,jpg' : 'required|mimes:jpeg,png,jpg',
        ]);

        if (empty($request->slug)) {
            $request['slug'] = Str::slug($request->name);
            $request['new_slug'] = $request['slug'];

            $count = 1;
            while (Category::where('slug', '=', $request['new_slug'])->exists()) {
                $request['new_slug'] = $request['slug'] . '-' . $count;
                $count++;
            }
        } else {
            $request['new_slug'] = $request->slug;
        }

        $imagePath = config('app.category_image');

        $categoryData = [
            'name' => $request->name,
            'slug' => $request->new_slug,
            'status' => $request->status,
            'parent_id' => $request->parent_cat ?? null,
        ];

        if ($request->hasFile('image')) {
            $categoryData['image'] = Helper::imageThumbnail($request->file('image'), $imagePath, $request->imageOld);
        } elseif (isset($request->imageOld)) {
            $categoryData['image'] = $request->imageOld;
        }

        $category = Category::updateOrCreate(['id' => $request->id], $categoryData);

        if ($category) {
            return redirect()->route('admin.categories.index','type='.$request->type)->with('success', 'Category saved successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong, please try again.');
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
        $data['parant_cal'] = Category::getParentCategory();
        $data['data'] = Category::where('id', $id)->first();
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
