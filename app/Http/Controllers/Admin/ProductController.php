<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\Publisher;
use App\Models\Author;
use App\Helper\Helper;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if($request->keyword){
            $data['keyword'] = $request->keyword;

            $query->where('name', 'like', '%'.$data['keyword'].'%');
        }

        if($request->status){
            $data['status'] = $request->status;

            if($request->status == 'active'){
                $query->where('status', '=', 1);
            }
            else {
                $query->where('status', '=', 0);
            }
        }

        if($request->category){
            $data['category'] = $request->category;

            $query->where('category_id', '=', $data['category']);
        }

        if($request->items){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }

        $data['data'] = $query->orderBy('created_at','DESC')->with('Category')->paginate($data['items']);
        $data['categories'] = Category::getParentCategory();

        return view('admin.product.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['categories'] = Category::getParentCategory();
        $data['publishers'] = Publisher::where('status',1)->get();
        $data['authors'] = Author::where('status',1)->get();
        return view('admin.product.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *@author Amit shen <email@email.com>
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|string|unique:products,name,'.$request->id,
            'category' => 'required', 
            'author' => 'required|exists:authors,id', 
            'publisher' => 'required|exists:publishers,id', 
            'sub_cat' => 'required', 
            'status' => 'required|in:0,1',
            'yop' => 'required',
            'original_title' => 'required',
            'isbn' => 'required',
            'no_of_page' => 'required',
        ] + (!empty($request->id) ? ['image' => 'mimes:jpeg,png,jpg|max:1024'] : ['pdf_mp3' => 'required|mimes:mp3,pdf|max:10240','image' => 'required|mimes:jpeg,png,jpg|max:1024']));
    
        $imagePath = config('app.product_image');
        $mediaPath = config('app.media_content');
    
        $product = Product::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'name' => $request->title,
                'slug' => Str::slug($request->title),
                'category_id' => $request->category, 
                'publisher_id' => $request->publisher, 
                'author_id' => $request->author, //isset($request->id)?$request->pdf_mp3Old:Helper::uploadFile($request->pdf_mp3,$mediaPath)
                'pdf_mp3' => ($request->hasfile('pdf_mp3'))?Helper::uploadFile($request->pdf_mp3,$mediaPath):$request->pdf_mp3Old, 
                'subcategory_id' => $request->sub_cat, 
                'year_of_publication' => $request->yop, 
                'original_title' => $request->original_title, 
                'isbn' => $request->isbn, 
                'no_of_page' => $request->no_of_page, 
                'discription' => $request->content, 
                'main_image' => $request->hasfile('image') ? Helper::imageThumbnail($request->file('image'), $imagePath, $request->imageOld) : (isset($request->imageOld) ? $request->imageOld : ''),
                'status' => $request->status,
            ]
        );
        $product->subCategories()->sync($request->sub_cat);
        if ($product) {
            return redirect()->route('admin.products.index')->with('success', 'Product saved successfully');
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
        $data['data'] = Product::find($id);

        return view('admin.product.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['categories'] = Category::getParentCategory();
        $data['publishers'] = Publisher::where('status',1)->get();
        $data['authors'] = Author::where('status',1)->get();
        $data['data'] = Product::find($id);
        return view('admin.product.create',$data);
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
            $data= Product::where('id',$id)->first();
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
            $data= Product::where('id',$id)->first();
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

    /**
     * Get Tax of Particular Category
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTaxByCategory($id, Request $request)
    {
        $category_tax = Category::getTaxIdByCategoryId($id);

        return response()->json(['success' => true, 'output' => $category_tax->tax->id ?? '']);
    }
    
}
