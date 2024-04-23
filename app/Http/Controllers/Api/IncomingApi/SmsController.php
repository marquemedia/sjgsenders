<?php

namespace App\Http\Controllers\Api\IncomingApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\GetSmsLogResource;
use App\Http\Resources\SmsLogResource;
use App\Models\GeneralSetting;
use App\Models\SMSlog;
use App\Models\User;
use App\Service\CustomerService;
use App\Service\SmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class SmsController extends Controller
{

    public SmsService $smsService;
    public CustomerService $customerService;

    public function __construct(SmsService $smsService, CustomerService $customerService) {

        $this->smsService      = $smsService;
        $this->customerService = $customerService;
    }

    /**
     * @param string $uid
     * @return JsonResponse
     */
    public function getSmsLog(string $uid): JsonResponse {

        $smsLog = SMSlog::where('uid', $uid)->first();
        if (!$smsLog) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid SMS Log uid'
            ],404);
        }

        return response()->json([
            'status'   => 'success',
            'sms_logs' => new GetSmsLogResource($smsLog),
        ],201);
    }

    public function store(Request $request) {
        
        try {
            $this->validate($request,[
                'contact'            => 'required|array|min:1',
                'contact.*.number'   => 'required|max:255',
                'contact.*.body'     => 'required',
                'contact.*.sms_type' => 'required|in:plain,unicode',
            ]);

            $general        = GeneralSetting::first();
            $user           = User::where('api_key', $request->header('Api-key'))->first();
            $setTimeInDelay = $request->input('schedule') == 2 ? $request->input('schedule_date') : Carbon::now();

            if ($user) {
                
                $defaultGateway = Gateway::whereNotNull('sms_gateways')->where('user_id', $user->id)->where('is_default', 1)->first();
        
                if ($defaultGateway || $user->sms_gateway == 1) { 

                    $smsGateway = $defaultGateway; 
                } elseif ($user->sms_gateway == 2) {

                     $smsGateway = null; 
                } else {

                    return response()->json([
                        'status'  => 'error',
                        'message' => 'You do not have any sms default gateway'
                    ],404);
                }

                if (!$smsGateway && $user->sms_gateway == 1) {

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid Sms Gateway'
                    ],404);
                }
            } else {
                
                $defaultGateway = Gateway::whereNotNull('sms_gateways')->whereNull('user_id')->where('is_default', 1)->first();
               
                if ($defaultGateway || $general->sms_gateway == 1) {

                    $smsGateway = $defaultGateway;
                    
                } elseif($general->sms_gateway == 2) {

                  	$smsGateway = null;
                }
                else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You do not have any sms default gateway'
                    ],404);
                }

                if (!$smsGateway && $general->sms_gateway == 1) {

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid Sms Gateway'
                    ],404);
                }
            }

            

            $smsLogs = collect();

            foreach ($request->input('contact') as $value) {

                $wordLength = 0;
                if ($user) {

                    $wordLength  = Arr::get($value, 'sms_type') == "plain" ? $general->sms_word_text_count : $general->sms_word_unicode_count;
                    $messages    = str_split($request->input('message'),$wordLength);
                    $totalCredit = count($messages);

                    if ($totalCredit > $user->credit) {

                        return response()->json([
                            'status'  => 'error',
                            'message' => 'You do not have a sufficient credit for send message'
                        ],404);
                    }
                    $this->customerService->deductCreditAndLogTransaction($user, $totalCredit, 1);
                }

                $apiGatewayId = $general->sms_gateway == 1 ? $smsGateway->id : null;
                if ($user) {

                    $apiGatewayId = $user->sms_gateway == 1 ? $smsGateway->id : null;
                }
                
                $log                 = new SMSlog();
                $log->user_id        = $user ? $user->id : null;
                $log->word_length    = $wordLength;
                $log->api_gateway_id = $apiGatewayId;
                $log->initiated_time = $setTimeInDelay;
                $log->to             = Arr::get($value, 'number');
                $log->sms_type       = Arr::get($value, 'sms_type') == "plain" ? 1 : 2;
                $log->message        = str_replace('{{name}}',Arr::get($value, 'number'), offensiveMsgBlock(Arr::get($value, 'body')));
                $log->status         = 1;
                $log->save();

                if ($user && $user->sms_gateway == 1) {

                    $this->smsService->sendSmsByOwnGateway($log);
                }

                if ($general->sms_gateway == 1) {

                    $this->smsService->sendSmsByOwnGateway($log);
                }
                $smsLogs->push(new SmsLogResource($log));
            }

            return response()->json([
                'status'   => 'success',
                'sms_logs' => $smsLogs->toArray(),
                'message'  => 'New SMS request sent, please see in the SMS history for final status'
            ],201);

        } catch (Throwable $e) {
            echo $e;
        }
    }
}