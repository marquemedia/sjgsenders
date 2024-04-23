<?php

namespace App\Http\Controllers\Api\IncomingApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiStoreEmailRequest;
use App\Http\Resources\EmailLogResource;
use App\Http\Resources\GetEmailLogResource;
use App\Jobs\ProcessEmail;
use App\Models\EmailLog;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Service\CustomerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use App\Models\Gateway;
use Illuminate\Validation\ValidationException;

class EmailController extends Controller
{

    public CustomerService $customerService;
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }
    /**
     * @param string $uid
     * @return JsonResponse
     */
    public function getEmailLog(string $uid): JsonResponse
    {
        $emailLog = EmailLog::where('uid', $uid)->first();
        if(!$emailLog){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Email Log uid'
            ],404);
        }

        return response()->json([
            'status' => 'success',
            'email_logs' => new GetEmailLogResource($emailLog),
        ],201);
    }

    /**
     * @throws ValidationException
     */
    public function store(ApiStoreEmailRequest $request): JsonResponse
    {
        $general = GeneralSetting::first();
        

        $user = User::where('api_key', $request->header('Api-key'))->first();

        if($user){
            $defaultGateway = Gateway::whereNotNull('mail_gateways')->where('user_id', $user->id)->where('is_default', 1)->first();
        
            if($defaultGateway) {
                $emailMethod = $defaultGateway;
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have any sms default gateway'
                ],404);
            }
            $totalContact = count($request->input('contact'));
            if($totalContact > $user->email_credit){
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have a sufficient email credit for send mail'
                ],404);
            }

            $this->customerService->deductEmailCredit($user, $totalContact);
        }else{
            $defaultGateway = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('is_default', 1)->first();
            
            if($defaultGateway) {
                $emailMethod = $defaultGateway;
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have any sms default gateway'
                ],404);
            }
            if(!$emailMethod){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Email Gateway'
                ],404);
            }
        }

        $emailHistory = collect();
        foreach($request->input('contact') as $value){
           
            $emailLog = new EmailLog();
            $emailLog->user_id = $user ? $user->id : null;
            $emailLog->from_name = $emailMethod->name;
            $emailLog->reply_to_email = $emailMethod->address;
            $emailLog->sender_id = $emailMethod->id;
            $emailLog->to = Arr::get($value, 'email');
            $emailLog->initiated_time = Carbon::now();
            $emailLog->status = EmailLog::PENDING;
            $emailLog->subject = Arr::get($value, 'subject');
            $emailLog->message = Arr::get($value, 'message');
            $emailLog->save();
            $emailHistory->push(new EmailLogResource($emailLog));
            if($emailLog->status == EmailLog::PENDING) {
                ProcessEmail::dispatch($emailLog);
            }
            
        }

        return response()->json([
            'status' => 'success',
            'email_logs' => $emailHistory->toArray(),
            'message' => 'New Email request sent, please see in the Email history for final status'
        ],201);
    }

}