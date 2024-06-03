<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Tax;
use App\Helper\Helper;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['data'] = Setting::getAllSettingData();

        return view('admin.site-setting.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'admin_mail' => 'required | email',
            // 'mobile_number' => 'required | digits:10 | integer',
            // 'landline_number' => 'nullable | integer',
            // 'support_email' => 'required | email',
            // 'whatsapp_number' => 'nullable | integer',
        ]);
        
        $imagePath = config('app.logo');

        $data[] = [
            'logo_1' => $request->hasfile('logo_1') ? Helper::storeImage($request->file('logo_1'),$imagePath) : (isset($request->logo_1_old) ? $request->logo_1_old : ''),
            'logo_2' => $request->hasfile('logo_2') ? Helper::storeImage($request->file('logo_2'),$imagePath) : (isset($request->logo_2_old) ? $request->logo_2_old : ''),
            'admin_mail' => $request->admin_mail,
            // 'mobile_number' => $request->mobile_number ?? NULL,
            // 'landline_number' => $request->landline_number ?? NULL,
            // 'support_email' => $request->support_email ?? NULL,
            // 'whatsapp_number' => $request->whatsapp_number ?? NULL,
        ];

        foreach ($data[0] as $key => $value) {
            Setting::updateOrCreate(
                [
                    'name' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
}
