<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserReferal;
use App\Models\Role;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Permission;
use App\Models\DriverProfile;
use App\Models\VendorProfile;
use App\Models\VendorAvailability;
use App\Helper\Helper;
use App\Models\StaffPermissions;
use App\Models\MarketingPartnerProfile;
use App\Models\MarketingPartnerBankAccount;
use App\Models\MarketingPartnerReferral;
use Validator;
use Hash;
use DB;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::query();
        // if(Auth::user()->roles->contains('5')){
        //     $query->where('id',Auth::user()->id);
        // }
        if(Auth::user()->roles->contains('2')){
            $query->where('id',Auth::user()->id);
        } else {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('id', 1);
            });
        }

        $keyword = $request->input('keyword', '');
        $query->where(function ($query1) use ($keyword) {
            $query1->where('nickname', 'like', '%'.$keyword.'%')
            ->orwhere('email', 'like', '%'.$keyword.'%');
        });
        
        if(isset($request->role)){
            $requestRole = $request->role;
            $query->whereHas('roles', function($q) use ($requestRole)
                            {
                                $q->where('id', $requestRole);
                            });
        }
        
        if(isset($request->status)){
            $query->where('status', $request->status);
        }

        if(isset($request->items)){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }
        
        $data['roles'] = Role::pluck('name','id');
        $data['data'] = $query->orderBy('created_at','DESC')->paginate($data['items']);

        return view('admin.user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['roles'] = Role::all()->pluck('name', 'id');
       
        return view('admin.user.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $rules = [
            'nickname' => 'nullable | string',
            'email' => 'nullable | email',
            // 'profileImage' => 'mimes:jpeg,png,jpg',
        ];

        $request->validate($rules);
        $userData = 1;
        DB::beginTransaction();
        $imagePath = config('app.profile_image');

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nickname' => $request->name,
            'email' => $request->email,
            'profile_image' => $request->hasfile('profileImage') ? Helper::storeImage($request->file('profileImage'), $imagePath, $request->profileImageOld) : (isset($request->profileImageOld) ? $request->profileImageOld : ''),
        ];

        if(!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $userData = User::updateOrCreate(['id' => $request->id,],$data);

        $user_role = $userData->roles()->sync(2);
       
        if($userData) {
            DB::commit();
            return redirect()->route('admin.users.index');
        }
        else {
            DB::rollback();
            return redirect()->back()->with('error', 'Something Went Wrong');
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
        $data['data'] = User::where('id',$id)->first();
        if(!$data['data']) {
            return redirect()->back()->with('error', 'Invalid User');
        }
        // $data['week_arr'] = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday']; 
        return view('admin.user.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['data'] = User::where('id',$id)->first();
        if(!$data['data']) {
            return redirect()->back()->with('error', 'Invalid User');
        }
        // $data['range'] = Helper::deliveryRange();
        $data['roles'] = Role::all()->pluck('name', 'id');
        $data['data']->load('roles');
        // $data['banks'] = Bank::allActiveBanks();
        $data['data']->staffPermissions;
        $data['staffPermissionsData'] = $data['data']->staffPermissions->pluck('staff_permission')->toArray();
        // $data['week_arr'] = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday']; 
        $data['staffPermissonsArray']= $this->staffPermissonsArray();

        return view('admin.user.create',$data);
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
            $data= User::where('id',$id)->delete();
            // $user_role = DB::table('role_user')->where('user_id',$id)->delete();
            if($data) {
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
            $data= User::where('id',$id)->first();
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

    public function addFund($id, Request $request)
    {
        $data = User::where('id', $id)->first();
        if(!$data) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
        $previous_wallet_balance = $data->wallet_balance;
        $data->wallet_balance += $request->amount;
        $result = $data->save();
        if($result) {
            $this->createWalletTransaction(NULL, $user_type = 'C', $data->id, $previous_wallet_balance, $data->wallet_balance, $request->amount, NULL, 'C', (isset($request->remark) ? $request->remark : ''));
        }
        return redirect()->route('admin.users.index');
    }

    public function revokeFund($id, Request $request)
    {
        $data = User::where('id', $id)->first();
        if(!$data) {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
        $previous_wallet_balance = $data->wallet_balance;
        $data->wallet_balance -= $request->amount;
        $result = $data->save();
        if($result) {
            $this->createWalletTransaction(NULL, $user_type = 'C', $data->id, $previous_wallet_balance, $data->wallet_balance, $request->amount, NULL, 'D', (isset($request->remark) ? $request->remark : ''));
        }
        return redirect()->route('admin.users.index');
    }

    /**
     * Display a listing of the User Referals.
     *
     * @return \Illuminate\Http\Response
     */
    public function userReferal(Request $request)
    {
        $query = UserReferal::query()->join('users', 'user_referals.user_id', '=', 'users.id')
                        ->join('users as referred_user', 'user_referals.referred_user_id', '=', 'referred_user.id')
                        ->select('user_referals.*', 'users.name as user_name', 'referred_user.name as referred_user_name', 'referred_user.referal_code as referred_user_code');
        
        if(isset($request->keyword)){
            $data['keyword'] = $request->keyword;
    
            $query->where(function ($query_new) use ($data) {
                $query_new->where('users.name', 'like', '%'.$data['keyword'].'%')
                ->orwhere('referred_user.name', 'like', '%'.$data['keyword'].'%')
                ->orwhere('referred_user.referal_code', 'like', '%'.$data['keyword'].'%');
            });
        }

        if(isset($request->items)){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }

        $data['data'] = $query->orderBy('created_at','DESC')->paginate($data['items']);

        return view('admin.user.user-referal', $data);
    }

    /**
     * Display a listing of the Marketing Partner Referrals.
     *
     * @return \Illuminate\Http\Response
     */
    public function marketingPartnerReferral(Request $request)
    {
        $query = MarketingPartnerReferral::query()->join('users', 'marketing_partner_referrals.user_id', '=', 'users.id')
                        ->join('users as referred_user', 'marketing_partner_referrals.referred_user_id', '=', 'referred_user.id')
                        ->select('marketing_partner_referrals.*', 'users.name as user_name', 'referred_user.name as referred_user_name', 'referred_user.referal_code as referred_user_code');
        
        if(isset($request->keyword)){
            $data['keyword'] = $request->keyword;
    
            $query->where(function ($query_new) use ($data) {
                $query_new->where('users.name', 'like', '%'.$data['keyword'].'%')
                ->orwhere('referred_user.name', 'like', '%'.$data['keyword'].'%')
                ->orwhere('referred_user.referal_code', 'like', '%'.$data['keyword'].'%');
            });
        }

        if(isset($request->items)){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }

        $data['data'] = $query->orderBy('created_at','DESC')->paginate($data['items']);

        return view('admin.user.marketing-partner-referrals', $data);
    }

    public function deletedUser(Request $request)
    {
        $query = User::onlyTrashed();
        if(Auth::user()->roles->contains('5')){
            $query->where('id',Auth::user()->id);
        }

        $keyword = $request->input('keyword', '');
        $query->where(function ($query1) use ($keyword) {
            $query1->where('name', 'like', '%'.$keyword.'%')
            ->orwhere('email', 'like', '%'.$keyword.'%')
            ->orwhere('phone', 'like', '%'.$keyword.'%');
        });
        
        if(isset($request->role)){
            $requestRole = $request->role;
            $query->whereHas('roles', function($q) use ($requestRole)
                            {
                                $q->where('id', $requestRole);
                            });
        }
        
        if(isset($request->status)){
            $query->where('status', $request->status);
        }

        if(isset($request->items)){
            $data['items'] = $request->items;
        }
        else{
            $data['items'] = 10;
        }
        
        $data['roles'] = Role::pluck('name','id');
        $data['data'] = $query->orderBy('created_at','DESC')->paginate($data['items']);

        return view('admin.user.index2', $data);
    }

    public function restoreUser($id, Request $request){
        try {
            $data= User::where('id',$id)->withTrashed()->first();
            if($data) {
                $data->deleted_at = NULL;
                $data->save();
                return redirect()->back()->with('success', 'User account is restored!!!');   
            }
            else {
                return redirect()->back()->with('error', 'No User is found with this UserID!!!');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message'  => "Something went wrong, please try again!",
                'error_msg' => $e->getMessage(),
            ], 400);
        }
    } 

    public function forceDeleteUser($id, Request $request){
        try {
            $data= User::where('id',$id)->withTrashed()->first();
            if($data) {
                $data->forceDelete();
                return redirect()->back()->with('success', 'User account is deleted!!!');   
            }
            else {
                return redirect()->back()->with('error', 'No User is found with this UserID!!!');
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
