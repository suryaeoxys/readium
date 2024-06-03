<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\MediaPost;
use App\Models\Product;
use App\Models\VendorProfile;
use Carbon\Carbon;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function dashboard() 
    {   
        if(Auth::user()->roles->contains('5')){
            return redirect()->route('admin.users.index');
        }
        $data['total_post'] = MediaPost::selectRaw('COUNT(*) as total_post')->first();
        $data['total_users'] = User::whereHas('roles', function($q){ $q->where('name', 'User');})->selectRaw('COUNT(*) as total_users')->first();
        $data['total_product'] = Product::selectRaw('COUNT(*) as total_product')->first();
        $data['recent_posts'] = MediaPost::orderBy('created_at', 'DESC')->take(5)->get();
        $data['recent_products'] = Product::orderBy('created_at', 'DESC')->take(5)->get();
        $data['total_user'] = User::orderBy('created_at', 'DESC')->where('id','!=',1)->take(5)->get();

        $temp_data = [];
        $temp_total_orders = [];
        
        $data['top_categories'] = $temp_data;
        
        $yearly_data =  MediaPost::select(DB::raw('month(created_at) as month'))->selectRaw('COUNT(*) as total_post')->whereYear('created_at', Carbon::now()->year)->groupBy(DB::raw('month(created_at)'))->get();
        $months = ['0','0','0','0','0','0','0','0','0','0','0','0'];
        $total_amount = 0;
        foreach ($months as $month_key => $month) {
            $mKey = $month_key+1;
            foreach ($yearly_data as $year_data) {
                if($mKey == $year_data->month){
                    $months[$month_key] = (string)$year_data->total_post;
                    // $total_amount += $year_data->total_earning;
                }
            }
        }
        $data['yearly_data'] = implode('","',$months);
       
        $this_week_datas = MediaPost::select(DB::raw('weekday(created_at) as weekday'))->selectRaw('COUNT(*) as total_post')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy(DB::raw('weekday(created_at)'))->get();
        $this_weeks = ['0','0','0','0','0','0','0'];
        foreach ($this_weeks as $this_week_key => $this_week) {
            foreach ($this_week_datas as $this_week_data) {
                if($this_week_key == $this_week_data->weekday){
                    $this_weeks[$this_week_key] = (string)$this_week_data->total_post;
                } 
            }
        }
        $data['this_week_data'] = implode('","',$this_weeks);
        $last_week_datas = MediaPost::select(DB::raw('weekday(created_at) as weekday'))->selectRaw('COUNT(*) as total_post')->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->groupBy(DB::raw('weekday(created_at)'))->get();
        $last_weeks = ['0','0','0','0','0','0','0'];
        foreach ($last_weeks as $last_week_key => $last_week) {
            foreach ($last_week_datas as $last_week_data) {
                if($last_week_key == $last_week_data->weekday){
                    $last_weeks[$last_week_key] = (string)$last_week_data->total_post;
                } 
            }
        }
        $data['last_week_data'] = implode('","',$last_weeks);

        return view('admin.index', $data);
    }
}