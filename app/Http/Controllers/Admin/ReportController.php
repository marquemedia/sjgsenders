<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\PaymentLog;
use App\Models\CreditLog;
use App\Models\EmailCreditLog;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\PricingPlan;
use App\Models\GeneralSetting;
use App\Models\WhatsappCreditLog;
use App\Http\Utility\SendMail;
use App\Models\AndroidApi;
use Carbon\Carbon;
use App\Models\Gateway;
use App\Models\WhatsappDevice;
use Illuminate\Support\Arr;

class ReportController extends Controller
{
    public function transaction()
    {
        $title = "All transaction log";
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        $transactions = Transaction::latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.transaction', compact('title', 'transactions', 'paymentMethods'));
    }

    public function transactionSearch(Request $request)
    {
        $title = "Transaction Log Search";
        $search = $request->search;
        $paymentMethod = $request->paymentMethod;
        $searchDate = $request->date;

        if ($search!="") {
            $transactions = Transaction::where('transaction_number', 'like', "%$search%");
        }

        if ($paymentMethod!="") {
            $transactions = Transaction::where('payment_method_id', '=', "$paymentMethod");
        }

        if ($searchDate!="") {
            $searchDate_array = explode('-',$request->date);
            $firstDate = $searchDate_array[0];
            $lastDate = null;
            if (count($searchDate_array)>1) {
                $lastDate = $searchDate_array[1];
            }
            $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
            if ($firstDate && !preg_match($matchDate,$firstDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($lastDate && !preg_match($matchDate,$lastDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($firstDate) {
                $transactions = Transaction::whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $transactions = Transaction::whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate=="" && $paymentMethod=="") {
            $notify[] = ['error','Search data field empty'];
            return back()->withNotify($notify);
        }
        $paymentMethods = PaymentMethod::where('status', 1)->get();

        $transactions = $transactions->latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.transaction', compact('title', 'transactions', 'search', 'searchDate', 'paymentMethods', 'paymentMethod'));
    }


    public function userTransaction()
    {
        $title = "User all transaction log";
        $transactions = Transaction::latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.transaction', compact('title', 'transactions'));
    }

    public function credit()
    {
        $title = "All sms credit log";
        $creditLogs = CreditLog::latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.credit_log', compact('title', 'creditLogs'));
    }

    public function creditSearch(Request $request)
    {
        $title = "Search SMS Credit Log";
        $search = $request->search;
        $searchDate = $request->date;

        if ($search!="") {
            $creditLogs = CreditLog::where('trx_number', 'like', "%$search%");
        }

        if ($searchDate!="") {
            $searchDate_array = explode('-',$request->date);
            $firstDate = $searchDate_array[0];
            $lastDate = null;
            if (count($searchDate_array)>1) {
                $lastDate = $searchDate_array[1];
            }
            $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
            if ($firstDate && !preg_match($matchDate,$firstDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($lastDate && !preg_match($matchDate,$lastDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($firstDate) {
                $creditLogs = CreditLog::whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $creditLogs = CreditLog::whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate=="") {
            $notify[] = ['error','Search data field empty'];
            return back()->withNotify($notify);
        }

        $creditLogs = $creditLogs->latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.credit_log', compact('title', 'creditLogs', 'search'));
    }

    public function whatsappcredit()
    {
        $title = "All whatsapp credit log";
        $whatsAppLogs = WhatsappCreditLog::latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.whatsapp_log', compact('title', 'whatsAppLogs'));
    }
    public function whatsappcreditSearch(Request $request)
    {
        $title = "Search WhatsApp Credit Log";
        $search = $request->search;
        $searchDate = $request->date;
        if ($search!="") {
            $whatsappCreditLogs = WhatsappCreditLog::where('trx_number', 'like', "%$search%");
        }
        if ($searchDate!="") {
            $searchDate_array = explode('-',$request->date);
            $firstDate = $searchDate_array[0];
            $lastDate = null;
            if (count($searchDate_array)>1) {
                $lastDate = $searchDate_array[1];
            }
            $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
            if ($firstDate && !preg_match($matchDate,$firstDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($lastDate && !preg_match($matchDate,$lastDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($firstDate) {
                $whatsappCreditLogs = WhatsappCreditLog::whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $whatsappCreditLogs = WhatsappCreditLog::whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }
        if ($search=="" && $searchDate=="") {
            $notify[] = ['error','Search data field empty'];
            return back()->withNotify($notify);
        }
        $whatsAppLogs = $whatsappCreditLogs->latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.whatsapp_log', compact('title', 'whatsAppLogs', 'search'));
    }

    public function emailCredit()
    {
        $title = "All email credit log";
        $emailCreditLogs = EmailCreditLog::latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.email_credit_log', compact('title', 'emailCreditLogs'));
    }

    public function emailCreditSearch(Request $request)
    {
        $title = "Search Email Credit Log";
        $search = $request->search;
        $searchDate = $request->date;

        if ($search!="") {
            $emailCreditLogs = EmailCreditLog::where('trx_number', 'like', "%$search%");
        }

        if ($searchDate!="") {
            $searchDate_array = explode('-',$request->date);
            $firstDate = $searchDate_array[0];
            $lastDate = null;
            if (count($searchDate_array)>1) {
                $lastDate = $searchDate_array[1];
            }
            $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
            if ($firstDate && !preg_match($matchDate,$firstDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($lastDate && !preg_match($matchDate,$lastDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($firstDate) {
                $emailCreditLogs = EmailCreditLog::whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $emailCreditLogs = EmailCreditLog::whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate=="") {
            $notify[] = ['error','Search data field empty'];
            return back()->withNotify($notify);
        }
        $emailCreditLogs = $emailCreditLogs->latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.email_credit_log', compact('title', 'emailCreditLogs', 'search'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $title = "Search by ".$searchData." transactions log";
        $transactions = Transaction::where('transaction_number', 'like', "%$search%")->latest()->with('user')->paginate(paginateNumber());
        return view('admin.report.index', compact('title', 'transactions', 'search'));
    }

    public function paymentLog()
    {
        $title = "Payment Logs";
        $paymentLogs = PaymentLog::where('status', '!=', 0)->with('user', 'paymentGateway')->latest()->paginate(paginateNumber());
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        return view('admin.report.payment_log', compact('title', 'paymentLogs', 'paymentMethods'));
    }

    public function paymentDetail($id)
    {
        $title = "Payment Details";
        $paymentLog = PaymentLog::where('status', '!=', 0)->where('id', $id)->firstOrFail();
        return view('admin.report.payment_detail', compact('title', 'paymentLog'));
    }


    public function approve(Request $request)
    {
        $request->validate([
            'id'       => 'required|integer',
            'feedback' => 'required'
        ]);
        $general     = GeneralSetting::first();
        $paymentData = PaymentLog::where('id',$request->id)->where('status',1)->first();
    
        $paymentData->feedback = $request->input('feedback');
        $paymentData->status = 2;
        $paymentData->save();
       
        $subscription = Subscription::where('id', $paymentData->subscriptions_id)->first();
        $last_expired_plan = Subscription::where("status", Subscription::EXPIRED)->latest()->first();
        $last_renewed_plan = Subscription::where("status", Subscription::RENEWED)->latest()->first();
        if($last_expired_plan?->plan_id == $subscription->plan_id) {
            Subscription::where(["plan_id" => $subscription->plan_id, "status" => Subscription::EXPIRED])->delete();
            $subscription->status = Subscription::RENEWED;
        } else {
            $subscription->status = Subscription::RUNNING;
            if($last_renewed_plan) {
                Subscription::where("status", Subscription::RENEWED)->update(["status" => Subscription::INACTIVE]);
            }
            if($last_expired_plan) {
                Subscription::where("status", Subscription::EXPIRED)->update(["status" => Subscription::INACTIVE]);
            }
        }
        Subscription::where('user_id', auth()->user()->id)->where('status', 1)->delete();
        AndroidApi::where(["user_id" => auth()->user()->id, "status" => 1])->update(["status" => 2]);
        WhatsappDevice::where(["user_id" => auth()->user()->id, "status" => "connected"])->update(["status" => "disconnected"]);
        $subscription->status        = Subscription::RUNNING;
        $subscription->plan_id       = $subscription->plan->id;
        $subscription->amount        = $subscription->plan->amount;
        $subscription->expired_date  = $subscription->expired_date->addDays($subscription->plan->duration);
        $subscription->save();
        $previousSubs = Subscription::where('user_id', auth()->user()->id)->where('status', 3)->pluck('id');
        
        if ($previousSubs->isNotEmpty()) {
            Subscription::destroy($previousSubs->toArray());
        } 
        PaymentLog::where('user_id', auth()->user()->id)->where('status', 1)->update(['status' => 3, 'feedback' => "Transaction Process Did Not Complete!"]);
        $user = User::find($paymentData->user_id);
        if($subscription->status == Subscription::RENEWED && $subscription->plan->carry_forward == 1) {
            $user->credit += $subscription->plan->sms->credits;
            $user->email_credit += $subscription->plan->email->credits;
            $user->whatsapp_credit += $subscription->plan->whatsapp->credits;
        } else {
            $user->credit = $subscription->plan->sms->credits;
            $user->email_credit = $subscription->plan->email->credits;
            $user->whatsapp_credit = $subscription->plan->whatsapp->credits;
        }
        Gateway::where('user_id', $user->id)->update(['status' => 0, 'is_default' => 0]);
       
        $user->save();
        
        $creditInfo = new CreditLog();
        $creditInfo->user_id = $user->id;
        $creditInfo->credit_type = "+";
        $creditInfo->credit = $subscription->plan->sms->credits;
        $creditInfo->trx_number = trxNumber();
        $creditInfo->post_credit =  $user->credit;
        $creditInfo->details = $subscription->plan->name. " Plan Purchased";
        $creditInfo->save();

        $emailCredit = new EmailCreditLog();
        $emailCredit->user_id = $user->id;
        $emailCredit->type = "+";
        $emailCredit->credit = $subscription->plan->email->credits;
        $emailCredit->trx_number = trxNumber();
        $emailCredit->post_credit =  $user->email_credit;
        $emailCredit->details = $subscription->plan->name. " Plan Purchased";
        $emailCredit->save();

        $whatsappCredit = new WhatsappCreditLog();
        $whatsappCredit->user_id = $user->id;
        $whatsappCredit->type = "+";
        $whatsappCredit->credit = $subscription->plan->whatsapp->credits;
        $whatsappCredit->trx_number = trxNumber();
        $whatsappCredit->post_credit =  $user->whatsapp_credit;
        $whatsappCredit->details = $subscription->plan->name. " Plan Purchased";
        $whatsappCredit->save();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'payment_method_id' => $paymentData->method_id,
            'amount' => $paymentData->amount,
            'transaction_type' => Transaction::PLUS,
            'transaction_number' => $paymentData->trx_number,
            'details' => 'Enrollment Confirmed:'.$subscription->plan->name.' Plan Subscribed!',
        ]);

        $mailCode = [
            'trx' => $paymentData->trx_number,
            'amount' => shortAmount($paymentData->final_amount),
            'charge' => shortAmount($paymentData->charge),
            'currency' => $general->currency_name,
            'rate' => shortAmount($paymentData->rate),
            'method_name' => $paymentData->paymentGateway->name,
            'method_currency' => $paymentData->paymentGateway->currency->name,
        ];
        
        $send = SendMail::MailNotification($user,'PAYMENT_CONFIRMED',$mailCode);
        
        $notify[] = ['success', "Payment has been approved. \t$send"];
        return back()->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $paymentLog = PaymentLog::where('id',$request->id)->where('status',1)->firstOrFail();
        $paymentLog->feedback = $request->input('feedback');
        $paymentLog->status = 3;
        $paymentLog->save();
        $paymentLog->plan->delete();
        $notify[] = ['success', 'Payment has been rejected.'];
        return back()->withNotify($notify);
    }



    public function paymentLogSearch(Request $request)
    {
        $title = "Payment Log Search";
        $search = $request->search;
        $paymentMethod = $request->paymentMethod;
        $searchDate = $request->date;
        if ($search!="") {
            $paymentLogs = PaymentLog::OrWhere('trx_number','like',"%$search%")
                ->OrWhereHas('user', function($q) use ($search){
                $q->where('email','like',"%$search%");
            });
        }
        if ($paymentMethod!="") {
            $paymentLogs = PaymentLog::where('method_id', '=', "$paymentMethod");
        }

        if ($searchDate!="") {
            $searchDate_array = explode('-',$request->date);
            $firstDate = $searchDate_array[0];
            $lastDate = null;
            if (count($searchDate_array)>1) {
                $lastDate = $searchDate_array[1];
            }
            $matchDate = "/\d{2}\/\d{2}\/\d{4}/";
            if ($firstDate && !preg_match($matchDate,$firstDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($lastDate && !preg_match($matchDate,$lastDate)) {
                $notify[] = ['error','Invalid order search date format'];
                return back()->withNotify($notify);
            }
            if ($firstDate) {
                $paymentLogs = PaymentLog::whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $paymentLogs = PaymentLog::whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate=="" && $paymentMethod=="") {
            $notify[] = ['error','Search data field empty'];
            return back()->withNotify($notify);
        }

        $paymentLogs = $paymentLogs->where('status', '!=', 0)->orderBy('id','desc')->with('user','paymentGateway')->paginate(paginateNumber());
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        return view('admin.report.payment_log', compact('title', 'paymentLogs', 'search', 'paymentMethods'));
    }

    public function subscription()
    {
        $title = "Subscription history";
        $pricingPlan = PricingPlan::where('status','=',1)->get();
        $subscriptions = Subscription::where('status', '!=', 0)->latest()->with('user', 'plan')->paginate(paginateNumber());
        return view('admin.report.subscription', compact('title', 'subscriptions', 'pricingPlan'));
    }

    public function subscriptionSearch(Request $request)
    {
        $title = "Subscription ";
        $search = $request->input('search');
        $planId = $request->input('subs_plan');
        $searchDate = $request->input('date');

        $subscriptions = Subscription::query()->where('status', '!=', 0);

        if (!blank($search)) {
            $subscriptions = $subscriptions->where('trx_number','like',"%$search%")
                ->OrWhereHas('user', function($q) use ($search){
                    $q->where('email','like',"%$search%");
                });

            $title = 'Subscription Search - ' . $search;
        }

        if (!blank($planId)) {
            $subscriptions = $subscriptions->where('plan_id', '=', "$planId");
        }

        if (!blank($searchDate)) {
           
            $dateRange = explode('-', $searchDate);
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay();
            $lastDate  = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;
            if ($firstDate) {
                $subscriptions->whereDate('created_at', '>=', $firstDate);
            }
            if ($lastDate) {
                $subscriptions->whereDate('created_at', '<=', $lastDate);
            }

            $title = 'Subscription Search - ' . $searchDate;
        }

        $pricingPlan = PricingPlan::where('status','=',1)->get();
        $subscriptions = $subscriptions->orderBy('id','desc')->with('user')->paginate(paginateNumber());
        return view('admin.report.subscription', compact('title', 'subscriptions', 'search', 'pricingPlan'));
    }

}
