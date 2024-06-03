<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Support\Str;
use App\Helper\Helper;

class PublisherController extends Controller
{
    public function index()
    {
        $data['publishers'] = Publisher::paginate(10);
        return view('admin.publisher.index', $data);
    }

    public function create()
    {
        return view('admin.publisher.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|integer',
            // 'image' => 'nullable|string|max:255',
        ] + (!empty($request->id) ? ['image' => 'mimes:jpeg,png,jpg'] : ['image' => 'required | mimes:jpeg,png,jpg'])
    );

        $imagePath = config('app.publisher_image');
       
        $publisher = Publisher::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'name'   => $request->name,
                'image' => $request->hasfile('image') ? Helper::storeImage($request->file('image'), $imagePath, $request->imageOld) : (isset($request->imageOld) ? $request->imageOld : ''),
                // 'image' => $request->hasfile('image') ? Helper::imageThumbnail($request->file('image'),$imagePath,$height = 224 ,$width = 225,$request->imageOld) : (isset($request->imageOld) ? $request->imageOld : ''),
                'status' => $request->status,
            ]
        );

        $result = $publisher->update();
        // Publisher::create($request->all());
        if($result)
            return redirect()->route('admin.publishers.index')->with('success', 'Publisher created successfully.');
        else
        return redirect()->back()->with('error', 'Something went Wrong, Please try again!');
    }

    public function show($id)
    {
        $publisher['data'] = Publisher::where('id',$id)->first();

        return view('admin.publishers.show',$publisher);
    }

    public function edit($id)
    {
        $publisher['data'] = Publisher::where('id', $id)->first();
        return view('admin.publisher.create', $publisher);
    }

    public function update(Request $request, Publisher $publisher)
    {
//   
    }

    public function destroy($id)
    {
        try {
            $data= Publisher::where('id',$id)->first();
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
    public function changeStatus($id, Request $request)
    {
        try {
            $data= Publisher::where('id',$id)->first();
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
