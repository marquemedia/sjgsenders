<?php

namespace App\Service;

use App\Http\Requests\StoreEmailRequest;
use App\Jobs\ProcessEmail;
use App\Models\CampaignContact;
use App\Models\Contact;
use App\Models\EmailContact;
use App\Models\EmailCreditLog;
use App\Models\EmailLog;
use App\Models\Gateway;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmailService
{
    /**
     * @param Request $request
     * @param array $allEmail
     * @param $userId
     * @return void
     */
    public function processEmail(Request $request, array &$allContactNumber, $userId = null): void {
      
        if($request->has('email')) {

            $email = Contact::query();
            $email->whereIn('id', $request->input('email'));

           
            if (!is_null($userId)) {

                $email->where('user_id', $userId);
            } else {

                $email->whereNull('user_id');
            }
            $emailArray = $email->pluck('email_contact','id')->toArray();
           
            $allContactNumber[] = array_values($emailArray) + array_diff($request->input('email') , $emailArray);
        }
    }

    public function searchEmailLog($search, $searchDate): \Illuminate\Database\Eloquent\Builder {

        $emailLogs = EmailLog::query();
        if (!empty($search)) {
            $emailLogs->whereHas('sender',function ($q) use ($search) {
                $q->where('subject', 'like', "%$search%")
                    ->orWhere('to', 'like', "%$search%");
            });
        }
        if (!empty(request()->input('status'))) {
            $status = match (request()->input('status')){
                'pending'   => [1],
                'schedule'  => [2],
                'fail'      => [3],
                'delivered' => [4],
                default     => [1,2,3,4],
            };
            $emailLogs->whereIn('status',$status);
        }
        if (!empty($searchDate)) {

            $dateRange = explode('-', $searchDate);
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay();
            $lastDate  = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;
            if ($firstDate) {
                $emailLogs->whereDate('created_at', '>=', $firstDate);
            }
            if ($lastDate) {
                $emailLogs->whereDate('created_at', '<=', $lastDate);
            }
        }

        return $emailLogs;
    }


    /**
     * @param Request $request
     * @param array $allEmail
     * @param array $emailGroupName
     * @param $userId
     * @return void
     */
    public function processEmailGroup(Request $request, array &$allEmail, array &$emailGroupName, $userId = null): void
    {
        
        if($request->has('email_group_id')){
            $emailContact = EmailContact::query();
            $emailContact->whereIn('email_group_id', $request->input('email_group_id'));

            if (!is_null($userId)) {
                $emailContact->where('user_id', $userId);
            } else {
                $emailContact->whereNull('user_id');
            }

            $allEmail[]     = $emailContact->pluck('email')->toArray();
            $emailGroupName = $emailContact->pluck('name', 'email')->toArray();
        }
    }


    /**
     * @param Request $request
     * @param array $allEmail
     * @param array $emailGroupName
     * @return void
     */
    public function processEmailFile(Request $request, array &$allEmail, array &$emailGroupName): void
    {
       
        if($request->has('file')) {

           
            $service   = new FileProcessService();
            $extension = strtolower($request->file('file')->getClientOriginalExtension());

            if($extension == "csv") {

                $response       =  $service->processCsv($request->file('file'));
                
                $allEmail[]     = array_keys($response);
                $emailGroupName = $emailGroupName + $response;
            };

            if($extension == "xlsx") {

                $response       = $service->processExel($request->file('file'));
                
                $allEmail[]     = array_keys($response);
                $emailGroupName = $emailGroupName + $response;
            }
        }
    }


    /**
     * @param array $allEmail
     * @return array
     */
    public function flattenAndUnique(array $allContactNumber): array
    {
        $newArray = [];
        foreach ($allContactNumber as $childArray) {
            foreach ($childArray as $value) {
                $newArray[] = $value;
            }
        }
        return array_unique($newArray);
    }

    /**
     * @param string $value
     * @param array $emailGroupName
     * @param string $content
     * @return string
     */
    public function getFinalContent(string $value, array $emailGroupName, string $content): string
    {
        $content      = buildDomDocument($content);
        $finalContent = str_replace('{{name}}',$value, $content);

        if(array_key_exists($value,$emailGroupName)){
            $finalContent = str_replace('{{name}}', $emailGroupName ? $emailGroupName[$value]:$value, $content);
        }

        return $finalContent;
    }


    /**
     * @param array $emailAllNewArray
     * @param Gateway $emailMethod
     * @param StoreEmailRequest $request
     * @param array $emailGroupName
     * @param null $userId
     * @return void
     */
    public function sendEmail(array $emailAllNewArray, Gateway $emailMethod, StoreEmailRequest $request, array $emailGroupName, $userId = null): void
    {
        foreach($emailAllNewArray as $value) {
            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                continue;
            }
            
            $prepare  = $this->prepParams($value, $request, (int)$emailMethod->id, $emailGroupName, $userId);
            $emailLog = $this->save($prepare);
            if($emailLog->status == 1){ 
                ProcessEmail::dispatch($emailLog);
            }
        }
    }

    /**
     * @param string $value
     * @param StoreEmailRequest $request
     * @param int $emailMethodId
     * @param array $emailGroupName
     * @param $userId
     * @return array
     */
    public function prepParams(string $value, StoreEmailRequest $request, int $emailMethodId, array $emailGroupName, $userId): array
    {
        $setTimeInDelay = Carbon::now();
        
       
        if($request->input('schedule') == 2){
            $setTimeInDelay = $request->input('schedule_date');
        }

        return  [
            'from_name'       => $request->input('from_name'),
            'reply_to_email'  => $request->input('reply_to_email'),
            'sender_id'       => $emailMethodId,
            'to'              => $value,
            'user_id'         => $userId,
            'initiated_time'  => $setTimeInDelay,
            'subject'         => $request->input('subject'),
            'message'         => $this->getFinalContent($value, $emailGroupName, $request->input('message')),
            'status'          => $request->input('schedule', EmailLog::PENDING),
            'schedule_status' => $request->input('schedule'),
        ];
    }

    /**
     * @param array $params
     * @return EmailLog
     */
    public function save(array $params): EmailLog
    {
        return EmailLog::create([
            'from_name'       => $params['from_name'],
            'user_id'         => $params['user_id'],
            'reply_to_email'  => $params['reply_to_email'],
            'sender_id'       => $params['sender_id'],
            'to'              => $params['to'],
            'initiated_time'  => $params['initiated_time'],
            'subject'         => $params['subject'],
            'message'         => $params['message'],
            'status'          => $params['status'],
            'schedule_status' => $params['schedule_status'],
        ]);
    }


    /**
     * @param EmailLog $emailLog
     * @param $errorMessage
     * @return void
     */
    public static function emailSendFailed(EmailLog $emailLog, $errorMessage): void {

        $emailLog->status           = EmailLog::FAILED;
        $emailLog->response_gateway = $errorMessage;
        $emailLog->save();
        $user = User::find($emailLog->user_id);
        if ($user) {

            $user->email_credit += 1;
            $user->save();
            $emailCredit              = new EmailCreditLog();
            $emailCredit->user_id     = $user->id;
            $emailCredit->type        = "+";
            $emailCredit->credit      = 1;
            $emailCredit->trx_number  = trxNumber();
            $emailCredit->post_credit =  $user->email_credit;
            $emailCredit->details     = "1 credit were added for send email failed";
            $emailCredit->save();
        }

        if($emailLog->contact_id){
            CampaignContact::where('id',$emailLog->contact_id)->update([
                "status" => 'Fail'
            ]);
        }
    }

    
}
