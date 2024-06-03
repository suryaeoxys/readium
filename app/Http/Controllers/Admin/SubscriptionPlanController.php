<?php

namespace App\Http\Controllers\Admin;
use App\Helper\ResponseBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Helper\Helper;
use Auth;
use Stripe; 
use Stripe\StripeClient;
use Carbon\Carbon;
use stdClass;
// use Stripe\Stripe;
use Stripe\Customer;

class SubscriptionPlanController extends Controller
{
    // private $stripe;
    // public function __construct()
    // {
    //     $this->stripe = new StripeClient(config('stripe.api_keys.secret_key'));
    //     $this->response = new stdClass();
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = SubscriptionPlan::query();
        
        $keyword = $request->input('keyword', '');

        $query->where(function ($query) use ($keyword) {
            $query->where('title', 'like', '%'.$keyword.'%')
            ->orwhere('features', 'like', '%'.$keyword.'%')
            ->orwhere('amount', 'like', '%'.$keyword.'%');
        });
        
        $data['data'] = $query->orderBy('id', 'DESC')->get();

        return view('admin.subscription-plan.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        return view('admin.subscription-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $subscriptionplan = SubscriptionPlan::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'amount' => $request->amount,
                'features' => $request->features,
                'description' => $request->description,
            ]
        );
        
        $result =  $subscriptionplan->update();

        if($result)
        {
            return redirect()->route('admin.subscription-plan.index');
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
        $data['data'] = SubscriptionPlan::where('id',$id)->first();
        return view('admin.subscription-plan.create',$data);
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
            $data= SubscriptionPlan::where('id',$id)->delete();
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

    // public function upgradeplan(Request $request)
    // {

    //     // $data['user'] = Auth::user();
    //   $data['Silvar'] = Subscriptionplan::where('title','Silvar Plan')->select('id','title','amount','features')->get()->map(function($data) {;
    //     return [
    //         'id'     => $data->id,
    //         'title' => $data->title,
    //         'amount' => $data->amount,
    //         'features' => $data->features,
    //     ];
    //    });

    //    $data['BronzePlan'] = Subscriptionplan::where('title','Bronze Plan')->select('id','title','amount','features')->get()->map(function($data) {;
    //     return [
    //         'id'     => $data->id,
    //         'title' => $data->title,
    //         'amount' => $data->amount,
    //         'features' => $data->features,
    //     ];
    //    });
       
    //    return view('admin.subscriptionplan.plan',$data);

    // }

    

    //  public function buyplan(Request $request)
    //  { 
    //     return $request;
    //      $validSet = [
    //              //'token'      => 'required',
    //              'amount'     => 'required'
    //         ];
    //         $isInValid = $this->isValidPayload($request, $validSet);
    //         if ($isInValid) {
    //             return ResponseBuilder::error($isInValid, $this->badRequest);
    //         }
    //            $data = User::where('id',$request->user_id)->first();
    //            $amount = $request->amount*100;
    //          return   $charge = $this->createCharge($request->token, $amount, NULL);
           
    //         if (!empty($charge) && $charge['status'] == 'succeeded') {
    //             Transaction  ::create([
    //                 'user_id'                   => $data->id,
    //                 'transaction_id'            => $charge['id'],
    //                 'subscription_id'           => $request->subscription_id,
    //                 'amount'                    => $charge['amount']/100,
    //                 'currency'                  => $charge['currency'],
    //                 'remark'                    => $charge['description'],
    //                 'response'                  => $charge,
    //                 'status'                    => $charge['status'],
    //                 'subscription_start_date'   => Carbon::today(),
    //                 'subscription_expiry_date'  =>  Carbon::today()->addDays(30),
    //                 // 'subscription_expiry_date' => 14,
    //                 'type'                      => 'subscription',
    //             ]);
    //              $data->subscription_expiry_date =  Carbon::today()->addDays(14);
    //              $data->status_payment  = true ;
    //              $data->save();
    //             return response()->json(['status' => true,'message'=>"payment successfully"]);
    //             // return ResponseBuilder::successMessage(trans('global.PAYMENT_SUCCESS'), $this->success);
    //         }
    //         else {
    //             return response()->json(['status' => false,'message'=>"payment Failed"]);
    //             // return ResponseBuilder::error(trans('global.PAYMENT_FAILED'), $this->badRequest);    
    //         }
       
    //  }
   
    //  private function createCharge($tokenId, $amount)
    // {
        
    //     $charge = null;
    //     try {
    //         // $stripe = new \Stripe\StripeClient(
    //         //     'sk_test_51EgyRrItQT8ZzyO1I06TwbGRQh8DTchlm51IBEGXL1AJWftWcuQqRG33A1q4BB8fipdPA398bM9NzU2flKii2NBf00L14WjNyA'
    //         // );
    //  $charge = $this->stripe->charges->create([
    //             'amount' => $amount,
    //             'currency' => 'usd',
    //             'source' => $tokenId,
    //             'description' => 'Subscription Charge'
    //         ]);
    //            return $charge;  
    //     } catch (Exception $e) {
    //         return$e;
    //         return ResponseBuilder::error($e->getMessage() . ' at line ' . $e->getLine() . ' at file ' . $e->getFile(), $this->badRequest);
    //         return $e->getMessage();
    //     }
        
    // }
     
}