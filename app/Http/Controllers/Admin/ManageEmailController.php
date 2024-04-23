<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmailRequest;
use App\Service\CustomerService;
use App\Service\EmailService;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Models\EmailCreditLog;
use App\Models\User;
use App\Models\EmailGroup;
use App\Models\Gateway;
use App\Jobs\ProcessEmail;
use App\Models\Group;
use App\Service\SmsService;

class ManageEmailController extends Controller
{
    public EmailService $emailService;
    public SmsService $smsService;
    public CustomerService $customerService;

    public function __construct(EmailService $emailService, CustomerService $customerService, SmsService $smsService) {

        $this->emailService    = $emailService;
        $this->customerService = $customerService;
        $this->smsService      = $smsService;
    }

    public function index() {

        $title     = "All Email History";
        $emailLogs = EmailLog::orderBy('id', 'DESC')->with('sender','user')->paginate(paginateNumber());
        return view('admin.email.index', compact('title', 'emailLogs'));
    }

    public function search(Request $request) {

        $search     = $request->input('search');
        $searchDate = $request->input('date');
        $status     = $request->input('status');

        if (empty($search) && empty($searchDate) && empty($status)) {

            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }
        $emailLogs = $this->emailService->searchEmailLog($search, $searchDate);
        $emailLogs = $emailLogs->paginate(paginateNumber());
        $title     = 'Email History Search - ' . $search;
        return view('admin.email.index', compact('title', 'emailLogs', 'search', 'status', 'searchDate'));
    }

    public function emailStatusUpdate(Request $request) {
        
        $request->validate([
            'id'     => 'nullable|exists:email_logs,id',
            'status' => 'required|in:1,3,4',
        ]);

        $emailLogIds = !empty($emailLogIds) ? array_filter(explode(",",$request->input('email_log_id'))) : $request->input('id');
        $this->emailLogStatusUpdate((int) $request->status, (array) $emailLogIds);

        $notify[] = ['success', 'Email status has been updated'];
        return back()->withNotify($notify);
    }

    private function emailLogStatusUpdate(int $status, array $emailLogIds): void {

        foreach(array_reverse($emailLogIds) as $emailLogId) {
           
            $emailLog = EmailLog::find($emailLogId);
            if(!$emailLog) {
                continue;
            }
            if($status == 1) {

                if($emailLog->user_id) {

                    $user = User::find($emailLog->user_id);
                    if($user?->email_credit > 1) {

                        $this->customerService->deductEmailCredit($user, 1);
                        $emailLog->status = $status;
                        $emailLog->update();
                        ProcessEmail::dispatch($emailLog);
                    }
                }else {
                    $emailLog->status = $status;
                    $emailLog->update();
                    ProcessEmail::dispatch($emailLog);
                }
            }else{
                $emailLog->status = $status;
                $emailLog->update();
            }
        }
    }

    public function emailSend($id)
    {
        
        $emailLog = EmailLog::where('id', $id)->first();
        $emailLog->status = 1;
        $emailLog->save();
        ProcessEmail::dispatch($emailLog);
        if($emailLog->user_id){
            $user = User::find($emailLog->user_id);
            if($user?->email_credit > 1) {
                $this->customerService->deductEmailCredit($user, 1);
                $emailLog->status = EmailLog::PENDING;
                $emailLog->save();
            }
        } 

        $notify[] = ['success', 'Mail sent'];
        return back()->withNotify($notify);
    }

    public function create() {

        $title       = "Compose Email";
        $emailGroups = Group::whereNull('user_id')->get();
        $gateways    = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('status', 1)->get();
        $credentials = config('setting.gateway_credentials.email');
        $channel     = "email";
        return view('admin.email.create', compact('title', 'emailGroups', 'gateways', 'credentials', 'channel'));
    }

    public function store(StoreEmailRequest $request) {
        
        $defaultGateway = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('is_default', 1)->first();
        
        if($request->input('gateway_id')) {
            
            $emailMethod = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('id',$request->gateway_id)->firstOrFail();
        }

        else{
            if($defaultGateway) {
                $emailMethod = $defaultGateway;
            }
            else {
                $notify[] = ['error', 'No Available Default Mail Gateway'];
                return back()->withNotify($notify);
            }
        }

        if(!$emailMethod) {

            $notify[] = ['error', 'Invalid Email Gateway'];
            return back()->withNotify($notify);
        }
        if(!$request->input('email') && !$request->input('group_id') && !$request->has('file')) {

            $notify[] = ['error', 'Invalid email format'];
            return back()->withNotify($notify);
        }
        if($request->has('file')) {

            $extension    = strtolower($request->file('file')->getClientOriginalExtension());

            if (!in_array($extension, ['csv', 'xlsx'])) {
                
                $notify[] = ['error', 'Invalid file extension'];
                return back()->withNotify($notify);
            }
        }

        $numberGroupName  = []; 
        $allContactNumber = [];
        $this->emailService->processEmail($request,$allContactNumber);
        $this->smsService->processGroupId($request, $allContactNumber, $numberGroupName);
        $this->smsService->processFile($request, $allContactNumber, $numberGroupName);
       
        $emailAllNewArray = $this->emailService->flattenAndUnique($allContactNumber);
        $this->emailService->sendEmail($emailAllNewArray, $emailMethod, $request, $numberGroupName);

        $notify[]         = ['success', 'New Email request sent, please see in the Email history for final status'];
        return back()->withNotify($notify);
    }
    public function viewEmailBody($id) {

        $title     = "Details View";
        $emailLogs = EmailLog::where('id',$id)->orderBy('id', 'DESC')->limit(1)->first();
        return view('partials.email_view', compact('title', 'emailLogs'));
    }

    public function delete(Request $request) {

        $this->validate($request, [
            'id' => 'required'
        ]);

        try {
            $emailLog = EmailLog::findOrFail($request->id);
            $user     = User::find($emailLog->user_id);
            if ($emailLog->status==1 && $user) {

                $user->email_credit      += 1;
                $user->save();
                $emailCredit              = new EmailCreditLog();
                $emailCredit->user_id     = $user->id;
                $emailCredit->type        = "+";
                $emailCredit->credit      = 1;
                $emailCredit->trx_number  = trxNumber();
                $emailCredit->post_credit =  $user->email_credit;
                $emailCredit->details     = "Credit Added for failed " .$emailLog->to;
                $emailCredit->save();
            }

            $emailLog->delete();
            $notify[] = ['success', "Successfully email log deleted"];
        } catch (\Exception $e) {
            $notify[] = ['error', "Error occour in email delete time. Error is "+$e->getMessage()];
        }
        return back()->withNotify($notify);
    }
}
