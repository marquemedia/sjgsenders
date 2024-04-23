<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\PricingPlan;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Carbon\Carbon;
use App\Http\Utility\PaymentInsert;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlanController extends Controller
{

    /**
     * @return View
     */
    public function create(): View
    {
    	$title = "All Plan";
    	$plans = PricingPlan::where('status', 1)
            ->where('amount', '>=','1')
            ->orderBy('amount', 'ASC')
            ->get();
       
    	$paymentMethods = PaymentMethod::where('status', 1)->get();
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', '!=', 0)
            ->latest()->first();
       
    	return view('user.plan.create',compact('title', 'plans', 'paymentMethods', 'subscription'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
       
    	$request->validate([
    		'id' => 'required|exists:pricing_plans,id',
    		'payment_gateway' => 'required|exists:payment_methods,id',
    	]);

    	$user = Auth::user();
        PaymentLog::where('user_id',$user->id)->where('status', 0)->delete();
        Subscription::where('user_id',$user->id)->where('status', 0)->delete();

    	$plan          = PricingPlan::where('id', $request->input('id'))->where('status', 1)->firstOrFail();
        $subscription  = Subscription::where('user_id', $user->id)->where('status', '!=', 0)->first();
        $paymentMethod = PaymentMethod::where('id', $request->input('payment_gateway'))->where('status', 1)->first();
        $subscription  = Subscription::create([
            'user_id'      => $user->id,
            'plan_id'      => $plan->id,
            'expired_date' => Carbon::now()->addDays($plan->duration),
            'amount'       => $plan->amount,
            'trx_number'   => trxNumber(),
            'status'       => $request->input("status"),
        ]);
        
    	session()->put('subscription_id', $subscription->id);
        PaymentInsert::paymentCreate($paymentMethod->unique_code);
        return redirect()->route('user.payment.preview');
    }

    /**
     * @return View
     */
    public function subscription(): View
    {
        $title = "Current Subscription Plan";
        $user = Auth::user();

        $paymentMethods = PaymentMethod::where('status', 1)->get();
        $subscriptions  = Subscription::where('user_id', $user->id)->orderBy('status', 'ASC')->where('status', '!=', 0)->with('plan')->paginate(paginateNumber());

        return view('user.subscription', compact('title', 'subscriptions', 'paymentMethods'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function subscriptionRenew(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $subscriptionPlan = Subscription::where('id', $request->input('id'))->where('user_id', $user->id)->firstOrFail();
        session()->put('subscription_id', $subscriptionPlan->id);
        $paymentMethod = PaymentMethod::where('id', $request->input('payment_gateway'))->where('status', 1)->first();
        PaymentInsert::paymentCreate($paymentMethod->unique_code);

        return redirect()->route('user.payment.preview');
    }
}
