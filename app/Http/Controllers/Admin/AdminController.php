<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Contact;
use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\PricingPlan;
use App\Models\SmsGateway;
use App\Models\Subscription;
use App\Models\SMSlog;
use App\Models\EmailLog;
use App\Models\AndroidApi;
use App\Models\AndroidApiSimInfo;
use App\Models\PaymentLog;
use App\Models\CreditLog;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\Gateway;
use App\Models\WhatsappDevice;

class AdminController extends Controller
{
    public function index()
    {
        $title = "Admin Dashboard";
        $customers = User::where('status','!=','3')->orderBy('id', 'DESC')->take(8)->get();
        $paymentLogs = PaymentLog::orderBy('id', 'DESC')->where('status', '!=', 0)->with('user', 'paymentGateway','paymentGateway.currency')->take(8)->get();
        $general = GeneralSetting::first();
        $logs = [
            "sms" => [
                'all' => SMSlog::count(),
                'success' => SMSlog::where('status',SMSlog::SUCCESS)->count(),
                'pending' => SMSlog::where('status',SMSlog::PENDING)->count(),
                'failed' => SMSlog::where('status',SMSlog::FAILED)->count(),
            ],
            "email" => [
                'all' => EmailLog::count(),
                'success' => EmailLog::where('status',EmailLog::SUCCESS)->count(),
                'pending' => EmailLog::where('status',EmailLog::PENDING)->count(),
                'failed' => EmailLog::where('status',EmailLog::FAILED)->count(),
            ],
            'whats_app' => [
                'all' => WhatsappLog::count(),
                'success' => WhatsappLog::where('status',WhatsappLog::SUCCESS)->count(),
                'pending' => WhatsappLog::where('status',WhatsappLog::PENDING)->count(),
                'failed' => WhatsappLog::where('status',EmailLog::FAILED)->count(),
            ],
        ];

        [$paymentReport, $paymentReportMonths] = $this->paymentReport();
        $smsWhatsAppReport = $this->smsWhatsAppReports();

        $totalUser = [
            'total_user' => User::count(),
            'subscriber' => Subscription::distinct('user_id')->count(),
        ];
        return view('admin.dashboard', compact(
            'title',
            'customers',
            'paymentLogs',
            'logs',
            'totalUser',
            'paymentReport',
            'paymentReportMonths',
            'smsWhatsAppReport',
        ));
    }



    public function smsWhatsAppReports(): array
    {
        $smsWhatsAppReports = [
            'sms' => [],
            'whatsapp' => [],
            'email' => [],
        ];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $month = $date->format('M');

            $smsCount = SMSlog::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $emailCount = EmailLog::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $whatsappCount = WhatsappLog::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            array_unshift($smsWhatsAppReports['sms'], $smsCount);
            array_unshift($smsWhatsAppReports['whatsapp'], $whatsappCount);
            array_unshift($smsWhatsAppReports['email'], $emailCount);
        }

        return $smsWhatsAppReports;
    }


    private function paymentReport(): array
    {
        $paymentReport = [
            'amount' => [],
            'charge' => [],
            'month' => [],
        ];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $month = $date->format('M');

            $paymentData = PaymentLog::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->get();

            $totalAmount = $paymentData->sum('amount');
            $totalCharge = $paymentData->sum('charge');

            array_unshift($paymentReport['amount'], $totalAmount);
            array_unshift($paymentReport['charge'], $totalCharge);
            array_unshift($paymentReport['month'], $month);
        }

        $quotedMonthsArray = array_map(function ($month) {
            return '"' . $month . '"';
        }, $paymentReport['month']);

        $paymentReportMonths = implode(',', $quotedMonthsArray);

        return [$paymentReport,$paymentReportMonths];
    }

    /**
     * @return View
     */
    public function profile(): View
    {
        $title = "Admin Profile";
        $admin = auth()->guard('admin')->user();

        return view('admin.profile', compact('title', 'admin'));
    }

    /**
     * @throws ValidationException
     */
    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
        ]);

        $admin = Auth::guard('admin')->user();
        $admin->name = $request->input('name');
        $admin->username = $request->input('username');
        $admin->email = $request->input('email');

        if($request->hasFile('image')){
            try{
                $removefile = $admin->image ?: null;
                $admin->image = StoreImage($request->file('image'), filePath()['profile']['admin']['path'], filePath()['profile']['admin']['size'], $removefile);
            }catch (\Exception){
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $admin->save();

        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }

    /**
     * @return View
     */
    public function password(): View
    {
        $title = "Password Update";
        return view('admin.password', compact('title'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);
        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            $notify[] = ['error', 'Password do not match !!'];
            return back()->withNotify($notify);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        $notify[] = ['success', 'Password changed successfully.'];
        return redirect()->route('admin.password')->withNotify($notify);
    }

    public function generateApiKey()
    {
        $title = "Generate Api Key";
        $admin = Auth::guard('admin')->user();
        return view('admin.generate_api_key', compact('title', 'admin'));
    }

    public function saveGenerateApiKey(Request $request): JsonResponse
    {
        $admin = Auth::guard('admin')->user();
        $admin->api_key  = $request->has('api_key') ? $request->input('api_key') : $admin->api_key ;
        $admin->save();

        return response()->json([
            'message' => 'New Api Key Has been Generate'
        ]);
    }


    public function selectSearch(Request $request){
        
        $searchData = trim($request->term);
        $contacts   = Contact::select('id','email_contact as text')->with('group')->where('email_contact','LIKE',  '%' . $searchData. '%')->latest()->simplePaginate(10);
        $pages      = true;

        if (empty($contacts->nextPageUrl())) {

            $pages = false;
        }
        $results = array(
          "results" => $contacts->items(),
          "pagination" => array(
            "more" => $pages
          )
        );

        return response()->json($results);
    }
    

    public function selectGateway(Request $request, $type = null) {

        $rows = [];

        if($type == "sms" || $type == "email") {
            
            $rows = Gateway::whereNull('user_id')->where('type', $request->type)->latest()->get();

        } elseif($type == "android") {

            $rows = AndroidApiSimInfo::where('android_gateway_id', $request->type)->latest()->get();
        } 
        return response()->json($rows);
    }
}
