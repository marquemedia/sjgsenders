<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AndroidApi;
use App\Models\GeneralSetting;
use App\Models\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Transaction;
use App\Models\SMSlog;
use App\Models\Group;
use App\Models\Contact;
use App\Models\Template;
use App\Models\CreditLog;
use App\Models\EmailLog;
use App\Models\EmailCreditLog;
use App\Models\PaymentMethod;
use App\Models\WhatsappLog;
use App\Models\WhatsappCreditLog;
use App\Models\PaymentLog;
use App\Models\Gateway;
use App\Models\PostWebhookLog;
use App\Models\PricingPlan;
use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsappTemplate;
use App\Service\WhatsAppService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function dashboard()
    {
        $title = "User dashboard";
        $user = Auth::user();
        $transactions = Transaction::where('user_id', $user->id)->orderBy('id', 'DESC')->take(10)->get();
        $credits = CreditLog::where('user_id', $user->id)->with('user')->orderBy('id', 'DESC')->take(10)->get();


        $logs = [
            "sms" => [
                'all' => SMSlog::where('user_id', $user->id)->count(),
                'success' => SMSlog::where('user_id', $user->id)->where('status',SMSlog::SUCCESS)->count(),
                'pending' => SMSlog::where('user_id', $user->id)->where('status',SMSlog::PENDING)->count(),
                'failed' => SMSlog::where('user_id', $user->id)->where('status',SMSlog::FAILED)->count(),
            ],
            "email" => [
                'all' => EmailLog::where('user_id', $user->id)->count(),
                'success' => EmailLog::where('user_id', $user->id)->where('status',EmailLog::SUCCESS)->count(),
                'pending' => EmailLog::where('user_id', $user->id)->where('status',EmailLog::PENDING)->count(),
                'failed' => EmailLog::where('user_id', $user->id)->where('status',EmailLog::FAILED)->count(),
            ],
            'whats_app' => [
                'all' => WhatsappLog::where('user_id', $user->id)->count(),
                'success' => WhatsappLog::where('user_id', $user->id)->where('status',WhatsappLog::SUCCESS)->count(),
                'pending' => WhatsappLog::where('user_id', $user->id)->where('status',WhatsappLog::PENDING)->count(),
                'failed' => WhatsappLog::where('user_id', $user->id)->where('status',EmailLog::FAILED)->count(),
            ],
        ];

        return view('user.dashboard', compact('title', 'user', 'transactions', 'credits', 'logs'));
    }

    public function profile()
    {
        $title = "User Profile";
        $user = auth()->user();
        return view('user.profile', compact('title', 'user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'nullable',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'nullable|max:250',
            'city' => 'nullable|max:250',
            'state' => 'nullable|max:250',
            'zip' => 'nullable|max:250',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip
        ];
        $user->address = $address;
        if($request->hasFile('image')) {
            try {
                $removefile = $user->image ?: null;
                $user->image = StoreImage($request->image, filePath()['profile']['user']['path'], filePath()['profile']['user']['size'], $removefile);
            }catch (\Exception $exp){
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('user.profile')->withNotify($notify);
    }

    public function password()
    {
        $title = "Password Update";
        return view('user.password', compact('title'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'nullable',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->user();

        if ($user->password && !Hash::check($request->input('current_password'), $user->password)) {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        $notify[] = ['success', 'Password has been updated'];
        return back()->withNotify($notify);
    }


    public function transaction()
    {
        $title = "Transaction Log";
        $user = Auth::user();
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        $transactions = Transaction::where('user_id', $user->id)->latest()->paginate(paginateNumber());
        return view('user.transaction', compact('title', 'transactions', 'paymentMethods'));
    }

    public function payment()
    {
        $title = "Payment History";
        $user = Auth::user();
        $paymentLogs = PaymentLog::where('user_id', $user->id)->latest()->paginate(paginateNumber());
        return view('user.payment_history', compact('title', 'paymentLogs'));
    }


    public function credit()
    {
        $title = "SMS Credit Log";
        $user = Auth::user();
        $credits = CreditLog::where('user_id', $user->id)->with('user')->latest()->paginate(paginateNumber());
        return view('user.credit', compact('title', 'credits'));
    }

    public function creditSearch(Request $request)
    {
        $title = "SMS Credit Search";
        $user = Auth::user();

        $search = $request->search;
        $searchDate = $request->date;

        if ($search!="") {
            $credits = CreditLog::where('user_id', $user->id)->where('trx_number', 'like', "%$search%");
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
                $credits = CreditLog::where('user_id', $user->id)->whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $credits = CreditLog::where('user_id', $user->id)->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate==""){
            $notify[] = ['error','Please give any search filter data'];
            return back()->withNotify($notify);
        }

        $credits = $credits->with('user')->paginate(paginateNumber());
        return view('user.credit', compact('title', 'credits', 'search', 'searchDate'));
    }

    public function whatsappCredit()
    {
        $title = "WhatsApp Credit Log";
        $user = Auth::user();
        $whatsappCredits = WhatsappCreditLog::where('user_id', $user->id)->with('user')->latest()->paginate(paginateNumber());
        return view('user.whatsapp_credit', compact('title', 'whatsappCredits'));
    }
    public function whatsappCreditSearch(Request $request)
    {
        $title = "WhatsApp Credit Search";
        $user = Auth::user();
        $search = $request->search;
        $searchDate = $request->date;
        if ($search!="") {
            $credits = WhatsappCreditLog::where('user_id', $user->id)->where('trx_number', 'like', "%$search%");
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
                $credits = WhatsappCreditLog::where('user_id', $user->id)->whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $credits = WhatsappCreditLog::where('user_id', $user->id)->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }
        if ($search=="" && $searchDate==""){
            $notify[] = ['error','Please give any search filter data'];
            return back()->withNotify($notify);
        }
        $whatsappCredits = $credits->with('user')->paginate(paginateNumber());
        return view('user.whatsapp_credit', compact('title', 'whatsappCredits', 'search', 'searchDate'));
    }


    public function emailCredit()
    {
        $title = "Email Credit Log";
        $user = Auth::user();
        $emailCredits = EmailCreditLog::where('user_id', $user->id)->latest()->paginate(paginateNumber());
        return view('user.email_credit', compact('title', 'emailCredits'));
    }

    public function emailCreditSearch(Request $request)
    {
        $title = "Email Credit Search";
        $search = $request->search;
        $searchDate = $request->date;
        $user = Auth::user();
        if ($search!="") {
            $emailCredits = EmailCreditLog::where('user_id', $user->id)->where('trx_number', 'like', "%$search%");
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
                $emailCredits = EmailCreditLog::where('user_id', $user->id)->whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $emailCredits = EmailCreditLog::where('user_id', $user->id)->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }

        if ($search=="" && $searchDate==""){
            $notify[] = ['error','Please give any search filter data'];
            return back()->withNotify($notify);
        }

        $emailCredits = $emailCredits->paginate(paginateNumber());
        return view('user.email_credit', compact('title', 'emailCredits', 'search'));
    }

    public function transactionSearch(Request $request)
    {
        $title = "Transaction Log Search";
        $search = $request->search;
        $paymentMethod = $request->paymentMethod;
        $searchDate = $request->date;
        $user = Auth::user();
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
                $transactions = Transaction::where('user_id', $user->id)->whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $transactions = Transaction::where('user_id', $user->id)->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }
        $user = Auth::user();
        $paymentMethods = PaymentMethod::where('status', 1)->get();
        if ($search!="") {
            $transactions = Transaction::where('user_id', $user->id)->where('transaction_number', 'like', "%$search%");
        }
        if ($paymentMethod!="") {
            $transactions = Transaction::where('user_id', $user->id)->where('payment_method_id', '=', "$paymentMethod");
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
                $transactions = Transaction::where('user_id', $user->id)->whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $transactions = Transaction::where('user_id', $user->id)->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }
        if ($searchDate=="" && $paymentMethod=="" &&  $search=="") {
            $notify[] = ['error','Please give any search filter data'];
                return back()->withNotify($notify);
        }
        $transactions = $transactions->paginate(paginateNumber());
        return view('user.transaction', compact('title', 'transactions', 'paymentMethods', 'search', 'searchDate', 'paymentMethod'));
    }

    public function paymentSearch(Request $request)
    {
        $title = "Payment History Search";
        $search = $request->search;
        $searchDate = $request->date;
        $user = Auth::user();
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
                $paymentLogs = PaymentLog::where('user_id', $user->id)->whereDate('created_at',Carbon::parse($firstDate));
            }
            if ($lastDate){
                $paymentLogs = PaymentLog::where('user_id', $user->id)->whereDate('created_at','>=',Carbon::parse($firstDate))->whereDate('created_at','<=',Carbon::parse($lastDate));
            }
        }
        $user = Auth::user();
       
        if ($search!="") {
            $paymentLogs = PaymentLog::where('user_id', $user->id)->where('trx_number', 'like', "%$search%");
        }
        if ($searchDate=="" &&  $search=="") {
            $notify[] = ['error','Please give any search filter data'];
            return back()->withNotify($notify);
        }
        $paymentLogs = $paymentLogs->paginate(paginateNumber());
        return view('user.payment_history', compact('title', 'paymentLogs', 'search', 'searchDate'));
    }

    public function generateApiKey()
    {
        $title = "Generate Api Key";
        $user = Auth::user();
        return view('user.generate_api_key', compact('title', 'user'));
    }

    public function saveGenerateApiKey(Request $request)
    {
        $user = Auth::user();
        $user->api_key  = $request->has('api_key') ? $request->input('api_key') : $user->api_key ;
        $user->save();

        return response()->json([
            'message' => 'New Api Key Has been Generate'
        ]);
    }


    public function defaultSmsMethod()
    {
        $title = "SMS Send Method";
        $user = Auth::user();
        $setting = GeneralSetting::first();
        $allowed_access = planAccess($user);
        if($allowed_access) {
            $allowed_access = (object)planAccess($user);
        } else {
            $notify[] = ['error','Please Purchase A Plan'];
            return redirect()->route('user.dashboard')->withNotify($notify);
        }
        
        
        if($allowed_access->type == PricingPlan::USER) {
            $smsGateways      = Gateway::where('user_id', $user->id)->whereNotNull('sms_gateways')->orderBy('is_default', 'DESC')->paginate(paginateNumber());
            $gatewaysForCount = Gateway::where('user_id', $user->id)->whereNotNull('sms_gateways')->where('status',1)->get();
            $androids         = AndroidApi::where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->paginate(paginateNumber());
        } else {
            $smsGateways      = Gateway::whereNull('user_id')->whereNotNull('sms_gateways')->orderBy('is_default', 'DESC')->paginate(paginateNumber());
            $gatewaysForCount = Gateway::whereNull('user_id')->whereNotNull('sms_gateways')->where('status',1)->get();
            $androids         = AndroidApi::whereNull('user_id')->orderBy('id', 'DESC')->paginate(paginateNumber());
        }
        
        $defaultGateway = Arr::get($user->gateways_credentials, 'sms.default_gateway_id',  $setting->sms_gateway_id);
      
        $credentials    = SmsGateway::orderBy('id','asc')->get();
        
        if(request()->routeIs('user.sms.gateway.sendmethod.gateway')){
            return view("user.gateway.settings.gateway", compact('title', 'smsGateways', 'defaultGateway', 'androids'));
        }
        elseif(request()->routeIs('user.sms.gateway.sendmethod.api')){
            $gatewayCount     = $gatewaysForCount->groupBy('type')->map->count(); 
            return view("user.gateway.index", compact('title', 'smsGateways', 'defaultGateway', 'androids', 'credentials', 'user', 'gatewayCount', 'allowed_access'));
        }
        elseif(request()->routeIs('user.gateway.sendmethod.android')){
            return view("user.android.gateways", compact('title', 'smsGateways', 'defaultGateway', 'androids', 'allowed_access'));
        }
    }


    public function defaultSmsGateway(Request $request)
    {
        $request->validate([
            'sms_gateway'=>"required"
        ]);

        $user = Auth::user();
        $user->sms_gateway = $request->input('sms_gateway');
        $user->save();

        $notify[] = ['success', 'Default Gateway Updated!!!'];
        return back()->withNotify($notify);
    }

    public function defaultCaptcha(int | string $randCode) :void{
        
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();
 
        if(Session::has('gcaptcha_code')) {
            Session::forget('gcaptcha_code');
        }
        Session::put('gcaptcha_code', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    public function postWebhook(Request $request, WhatsAppService $whatsAppService) {

        $general = GeneralSetting::first();
        
        try {
            if ($request->isMethod('get')) {

                $apiKey = $general->webhook["verify_token"];
                $query  = $request->query();
        
                $hubMode   = $query["hub_mode"] ?? null;
                $hubToken  = $query["hub_verify_token"] ?? null;
                $challenge = $query["hub_challenge"] ?? null;
                
                $usersCount = User::where("webhook_token", $hubToken)->count();
                
                if ($hubMode && $hubToken && $hubMode === 'subscribe' && ($hubToken === $apiKey || $usersCount > 0)) {

                    return response($challenge, 200)->header('Content-Type', 'text/plain');
                } else {

                    throw new Exception("Invalid Request");
                }
            } else {

                $request      = request()->all();
                $user         = User::where('uid',request()->input('uid'))->first();
                
                $webhookLog   = PostWebhookLog::create([
                    'user_id'           => $user ? $user->id : null,
                    'webhook_response'  => json_encode($request)
                ]);
                
                $response      = json_decode($webhookLog->webhook_response, true);
                $idFromRequest = $response["entry"][0]['changes'][0]['value']['statuses'][0]['id'] ?? null;
        
                if ($idFromRequest) {

                    $whatsappLog = WhatsappLog::whereJsonContains('message_response->messages', [['id' => $idFromRequest]])->first();
        
                    if ($whatsappLog) {
                        
                        $errors = $response['entry'][0]['changes'][0]['value']['statuses'][0]['errors'] ?? [];
                        if (!empty($errors)) {

                            $whatsappLog->status = WhatsappLog::FAILED;
                            $whatsAppService->addedCredit($whatsappLog, $errors[0]['message']);
                            $whatsappLog->save();
                        } else {
                            
                            $status = $response['entry'][0]['changes'][0]['value']['statuses'][0]['status'];

                            if ($status == 'failed') {

                                $whatsappLog->status = WhatsappLog::FAILED;
                                $whatsAppService->addedCredit($whatsappLog, "Cloud API couldnt send the message.");
                            } elseif ($status == 'sent') {

                                $whatsappLog->delivered_at = now();
                                $whatsappLog->status = WhatsappLog::SUCCESS;
                            }
                            $whatsappLog->save();
                        }
                        
                    } 
                    
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
        
    }

    public function selectGateway(Request $request, $type = null) {
        
        $user = Auth::user();
        $allowed_access = planAccess($user);
        $rows = [];

        if ($allowed_access) {
            $allowed_access = (object)planAccess($user);
        } else {
            $notify[] = ['error','Please Purchase A Plan'];
            return redirect()->route('user.dashboard')->withNotify($notify);
        }

        if ($type == "sms") {

            $rows = $allowed_access->type == PricingPlan::USER ? 
            Gateway::whereNotNull("sms_gateways")->where('status', 1)->where("user_id", $user->id)->where('type', $request->type)->latest()->get()
            : Gateway::whereNotNull("sms_gateways")->where('status', 1)->whereNull("user_id")->where('type', $request->type)->latest()->get();
        
        } elseif ($type == "android") {

             $rows = AndroidApiSimInfo::where('android_gateway_id', $request->type)->latest()->get();

        } elseif ($type == "email") {

            $rows = $allowed_access->type == PricingPlan::USER ? 
                Gateway::whereNotNull("mail_gateways")->where('status', 1)->where("user_id", $user->id)->where('type', $request->type)->latest()->get() 
                : Gateway::whereNotNull("mail_gateways")->where('status', 1)->wherenull("user_id")->where('type', $request->type)->latest()->get();
        }
       
        return response()->json($rows);
    }
    public function fetch(Request $request, $type = null) {
		
		$templates = WhatsappTemplate::find(auth()->user()->id)->where("cloud_id", $request->input("cloud_id"))
                              ->whereIn("status", ["APPROVED", "PENDING"])
                              ->get();
		return response()->json(['templates' => $templates]);
	
	}
}
