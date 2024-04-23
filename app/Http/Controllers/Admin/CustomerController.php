<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreditRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\AndroidApi;
use App\Models\CreditLog;
use App\Models\EmailCreditLog;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\WhatsappCreditLog;
use App\Service\CustomerService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WhatsappDevice;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $title = "All Users";
        $customers = $this->customerService->getPaginateUsers();
        return view('admin.customer.index', compact('title', 'customers'));
    }

    /**
     * @param $id
     * @return View
     */
    public function details($id): View
    {
        $title = "User Details";
        $user = $this->customerService->findById($id);
        $logs = $this->customerService->logs($user->id);
        $pricing_plans = PricingPlan::where("status", 1)->pluck("name", "id")->toArray();
        return view('admin.customer.details', compact('title', 'user', 'logs', "pricing_plans"));
    }

    public function store(UserStoreRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'status' => User::ACTIVE,
            'password' => Hash::make($request->input('password')),
            'gateways_credentials' => config('setting.gateway_credentials'),
            'email_verified_code' => null,
            'email_verified_at' => carbon(),
            'email_verified_status' => User::YES,
        ]);
        $this->customerService->applySignUpBonus($user);
        $notify[] = ['success', 'User has been created'];
        return back()->withNotify($notify);
    }

    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UserUpdateRequest $request, $id): mixed {
        
        $user = $this->customerService->findById($id);
        Subscription::where(["user_id" => $id, "status" => Subscription::RUNNING])->update(["status" => Subscription::INACTIVE]);
        $new_plan = PricingPlan::where("id", $request->input("pricing_plan"))->firstorFail();
        Subscription::create([
            "user_id"      =>  $id,
            "plan_id"      =>  $request->input("pricing_plan"),
            "amount"       =>  $new_plan->amount,
            "expired_date" => Carbon::now()->addDays($new_plan->duration),
            "trx_number"   => trxNumber(),
            "status"       => Subscription::RUNNING,
        ]);
        $user->credit = $new_plan->sms->credits;
        $user->email_credit = $new_plan->email->credits;
        $user->whatsapp_credit = $new_plan->whatsapp->credits;
        AndroidApi::where(["user_id" => $id, "status" => 1])->update(["status" => 2]);
        WhatsappDevice::where(["user_id" => $id, "status" => "connected"])->update(["status" => "disconnected"]);
        $user->fill($request->validated());
        $user->address = [
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip')
        ];
        $user->save();

        $notify[] = ['success', 'User has been updated'];
        return back()->withNotify($notify);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $searchDate = $request->input('date');
        $status = $request->input('status');

        if (empty($search) && empty($searchDate) && empty($status)) {
            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }

        $customers = $this->customerService->searchCustomers($search, $searchDate);
        $customers = $customers->paginate(paginateNumber());

        $title = 'User Search - ' . $search;
        return view('admin.customer.index', compact('title', 'search', 'customers', 'searchDate', 'status'));
    }


    /**
     * @param $id
     * @return View
     */
    public function contact($id): View
    {
        $user = $this->customerService->findById($id);
        $title = $user->name." Contact List";
        $users = $this->customerService->getCustomerForContacts();
        $contacts = $this->customerService->contactListForUser($id);
        return view('admin.phone_book.sms_contact', compact('title', 'contacts', 'users'));
    }


    /**
     * @param $id
     * @return View
     */
    public function sms($id): View
    {
        $user = $this->customerService->findById($id);
        $title = $user->name." sms list";
        $smslogs = $this->customerService->smsLogsForUser($id);
        return view('admin.sms.index', compact('title', 'smslogs'));
    }

    public function emailContact($id): View
    {
        $user = $this->customerService->findById($id);
        $title = $user->name." email contact list";
        $users = $this->customerService->getCustomerForContacts();
        $emailContacts = $this->customerService->emailContactsForUser($id);
        return view('admin.phone_book.email_contact', compact('title', 'emailContacts', 'users'));
    }


    /**
     * @param $id
     * @return View
     */
    public function emailLog($id): View
    {
        $user = $this->customerService->findById($id);
        $title = $user->name." email list";
        $emailLogs = $this->customerService->emailLogsForUser($id);
        return view('admin.email.index', compact('title', 'emailLogs'));
    }


    /**
     * @param UserCreditRequest $request
     * @return mixed
     */
    public function addReturnCredit(UserCreditRequest $request): mixed
    {
        $user = $this->customerService->findById($request->input('id'));
        $credits = $this->customerService->buildCreditArray($request);

        foreach ($credits as $type => $credit) {

            if($type == 'sms'){
                $column = 'credit';
            }else{
                $column = $type . '_credit';
            }

            if ($credit > 0) {
                if ($request->input('type') == 2 && $user->$column < $credit) {
                    $notify[] = ['error', 'Invalid ' . ucfirst($type) . ' Credit Number'];
                    return back()->withNotify($notify);
                }

                $creditLog = $this->{$type . 'Credit'}($request, $user);
                $credits[$type] = $creditLog->credit;
            }
        }

        $smsCredits = Arr::get($credits, 'sms');
        $emailCredits = Arr::get($credits, 'email');
        $whatsappCredits = Arr::get($credits, 'whatsapp');

        if ($request->input('type') == 1) {
            $user->credit += $smsCredits;
            $user->email_credit += $emailCredits;
            $user->whatsapp_credit += $whatsappCredits;

            $notify[] = ['success', 'Credit has been added'];

        } else {
            $user->credit -= $smsCredits;
            $user->email_credit -= $emailCredits;
            $user->whatsapp_credit -= $whatsappCredits;

            $notify[] = ['success', 'Credit has been returned'];
        }

        $user->save();

        return back()->withNotify($notify);
    }

    protected function createCreditLog(UserCreditRequest $request, User $user, $creditField, $creditLogClass)
    {
        $creditType = $request->input('type') == 1 ? "+" : "-";
        $details = $request->input('type') == 1 ? "Added by admin" : "Returned by admin";


        if($creditField == 'sms_credit'){
            $column = 'credit';
        }else{
            $column = $creditField;
        }

        return $creditLogClass::create([
            'user_id' => $user->id,
            'type' => $creditType,
            'credit' => $request->input($creditField),
            'trx_number' => trxNumber(),
            'post_credit' => $user->$column,
            'details' => $details,
        ]);
    }

    protected function smsCredit(UserCreditRequest $request, User $user)
    {
        return $this->createCreditLog($request, $user, 'sms_credit', CreditLog::class);
    }

    protected function emailCredit(UserCreditRequest $request, User $user): EmailCreditLog
    {
        return $this->createCreditLog($request, $user, 'email_credit', EmailCreditLog::class);
    }

    protected function whatsappCredit(UserCreditRequest $request, User $user): WhatsappCreditLog
    {
        return $this->createCreditLog($request, $user, 'whatsapp_credit', WhatsappCreditLog::class);
    }

      /**
     * user login
     * 
     */
    public function login(int $id) {

        $user = User::where('id',$id)->first();
        Auth::guard('web')->loginUsingId($user->id);
        $notify[] = ['success', "Logged in as $user->name"];
        return redirect()->route('user.dashboard')->withNotify($notify);
    }

}
