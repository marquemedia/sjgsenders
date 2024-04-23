<?php

namespace App\Service;

use App\Http\Requests\UserCreditRequest;
use App\Jobs\RegisterMailJob;
use App\Models\Contact;
use App\Models\CreditLog;
use App\Models\EmailContact;
use App\Models\EmailCreditLog;
use App\Models\EmailLog;
use App\Models\GeneralSetting;
use App\Models\PricingPlan;
use App\Models\SMSlog;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Pagination\AbstractPaginator;

class CustomerService
{
    /**
     * @param $userId
     * @return mixed
     */
    public function findById($userId):mixed
    {
        return User::findOrFail($userId);
    }

    /**
     * @return AbstractPaginator
     */
    public function getPaginateUsers(): AbstractPaginator
    {
        return User::latest()->paginate(paginateNumber());
    }


    /**
     * @return AbstractPaginator
     */
    public function getPaginateActiveUsers(): AbstractPaginator
    {
        return User::active()->paginate(paginateNumber());
    }


    /**
     * @return AbstractPaginator
     */
    public function getPaginateBannedUsers(): AbstractPaginator
    {
        return User::banned()->paginate(paginateNumber());
    }


    /**
     * @param User $user
     * @return void
     */
    public function applySignUpBonus(User $user): void
    {
        $general = GeneralSetting::first();
        $plan = PricingPlan::find($general->plan_id);

        if($general->sign_up_bonus != 1 || !$plan){
            return;
        }

        $user->credit = $plan->sms->credits;
        $user->email_credit = $plan->email->credits;
        $user->whatsapp_credit = $plan->whatsapp->credits;
        $user->save();

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'expired_date' => Carbon::now()->addDays($plan->duration),
            'amount' =>$plan->amount,
            'trx_number' =>trxNumber(),
            'status' => Subscription::RUNNING,
        ]);
    }


    /**
     * @param User $user
     * @return void
     */
    public function handleEmailVerification(User $user): void
    {
        $general = GeneralSetting::first();

        if($general->email_verification_status == 2){
            $user->email_verified_status = User::YES;
            $user->email_verified_code = null;
            $user->email_verified_at = carbon();
        }else{

            $mailCode = [
                'name' => $user->name,
                'code' => $user->email_verified_code,
                'time' => carbon(),
            ];

            RegisterMailJob::dispatch($user, 'REGISTRATION_VERIFY', $mailCode);
        }

        $user->save();
    }


    public function searchCustomers($search, $searchDate): \Illuminate\Database\Eloquent\Builder
    {
        $customers = User::query();
        if (!empty($search)) {
            $customers->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if (!empty(request()->input('status'))) {
            $status = match (request()->input('status')){
                'active' => [1],
                'banned' => [0],
                default => [1,2],
            };
            $customers->whereIn('status',$status);
        }

        if (!empty($searchDate)) {
          
            $dateRange = explode('-', $searchDate);
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay();
            $lastDate  = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;
            if ($firstDate) {
                $customers->whereDate('created_at', '>=', $firstDate);
            }
            if ($lastDate) {
                $customers->whereDate('created_at', '<=', $lastDate);
            }
        }
        
        return $customers;
    }



    /**
     * @param $userId
     * @return array
     */
    public function logs($userId): array
    {
         return[
            "sms" => [
                'all' => SMSlog::where('user_id', $userId)->where('user_id', $userId)->count(),
                'success' => SMSlog::where('user_id', $userId)->where('status',SMSlog::SUCCESS)->count(),
                'pending' => SMSlog::where('user_id', $userId)->where('status',SMSlog::PENDING)->count(),
                'failed' => SMSlog::where('user_id', $userId)->where('status',SMSlog::FAILED)->count(),
            ],
            "email" => [
                'all' => EmailLog::where('user_id', $userId)->count(),
                'success' => EmailLog::where('user_id', $userId)->where('status',EmailLog::SUCCESS)->count(),
                'pending' => EmailLog::where('user_id', $userId)->where('status',EmailLog::PENDING)->count(),
                'failed' => EmailLog::where('user_id', $userId)->where('status',EmailLog::FAILED)->count(),
            ],
            'whats_app' => [
                'all' => WhatsappLog::where('user_id', $userId)->count(),
                'success' => WhatsappLog::where('user_id', $userId)->where('status',WhatsappLog::SUCCESS)->count(),
                'pending' => WhatsappLog::where('user_id', $userId)->where('status',WhatsappLog::PENDING)->count(),
                'failed' => WhatsappLog::where('user_id', $userId)->where('status',EmailLog::FAILED)->count(),
            ],
        ];

    }



    public function getCustomerForContacts()
    {
        return User::select('id', 'name')->get();
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function contactListForUser($userId): mixed
    {
        return Contact::where('user_id', $userId)->latest()->with('user', 'group')->paginate(paginateNumber());
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function smsLogsForUser($userId): mixed
    {
        return SMSlog::where('user_id', $userId)->latest()->with('user', 'androidGateway', 'smsGateway')->paginate(paginateNumber());
    }


    /**
     * @param $userId
     * @return mixed
     */
    public function emailContactsForUser($userId): mixed
    {
        return EmailContact::where('user_id', $userId)->latest()->with('user', 'emailGroup')->paginate(paginateNumber());
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function emailLogsForUser($userId): mixed
    {
        return EmailLog::where('user_id', $userId)->latest()->with('user','sender')->paginate(paginateNumber());
    }


    /**
     * @param UserCreditRequest $request
     * @return array
     */
    public function buildCreditArray(UserCreditRequest $request): array
    {
        return [
            'sms' => $request->input('sms_credit', 0),
            'email' => $request->input('email_credit', 0),
            'whatsapp' => $request->input('whatsapp_credit', 0),
        ];
    }


    /**
     * @param User $user
     * @param int $totalCredit
     * @param int $totalNumber
     * @return void
     */
    public function deductCreditAndLogTransaction(User $user, int $totalCredit, int $totalNumber): void
    {
        $user->credit -= $totalCredit;
        $user->save();

        $creditInfo = new CreditLog();
        $creditInfo->user_id = $user->id;
        $creditInfo->credit_type = "-";
        $creditInfo->credit = $totalCredit;
        $creditInfo->trx_number = trxNumber();
        $creditInfo->post_credit =  $user->credit;
        $creditInfo->details = $totalCredit." credits were cut for " .$totalNumber . " number send message";
        $creditInfo->save();
    }


    /**
     * @param User $user
     * @param int $totalCredit
     * @param string $to
     * @return void
     */
    public static function addedCreditLog(User $user, int $totalCredit, string $to): void
    {
        $user->credit += $totalCredit;
        $user->save();

        $creditInfo = new CreditLog();
        $creditInfo->user_id = $user->id;
        $creditInfo->credit_type = "+";
        $creditInfo->credit = $totalCredit;
        $creditInfo->trx_number = trxNumber();
        $creditInfo->post_credit =  $user->credit;
        $creditInfo->details = $totalCredit." Credit Return ".$to." is failed";
        $creditInfo->save();
    }


    /**
     * @param User $user
     * @param int $totalEmail
     * @return void
     */
    public function deductEmailCredit(User $user, int $totalEmail): void
    {
        $user->email_credit -=  $totalEmail;
        $user->save();

        $emailCredit = new EmailCreditLog();
        $emailCredit->user_id = $user->id;
        $emailCredit->type = "-";
        $emailCredit->credit = $totalEmail;
        $emailCredit->trx_number = trxNumber();
        $emailCredit->post_credit =  $user->email_credit;
        $emailCredit->details = $totalEmail." credits were cut for send email";
        $emailCredit->save();
    }

    /**
     * @param User $user
     * @param int $totalCredit
     * @param int $totalNumber
     * @return void
     */
    public function deductWhatsAppCredit(User $user, int $totalCredit, int $totalNumber): void
    {
        $user->whatsapp_credit -=  $totalCredit;
        $user->save();

        $creditInfo = new WhatsappCreditLog();
        $creditInfo->user_id = $user->id;
        $creditInfo->type = "-";
        $creditInfo->credit = $totalCredit;
        $creditInfo->trx_number = trxNumber();
        $creditInfo->post_credit =  $user->whatsapp_credit;
        $creditInfo->details = $totalCredit." credits were cut for " .$totalNumber . " number send message";
        $creditInfo->save();
    }

}
