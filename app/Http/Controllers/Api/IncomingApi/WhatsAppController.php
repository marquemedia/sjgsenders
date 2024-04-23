<?php

namespace App\Http\Controllers\Api\IncomingApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\GetWhatsAppLogResource;
use App\Http\Resources\WhatsAppLogResource;
use App\Jobs\ProcessWhatsapp;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Rules\MessageFileValidationRule;
use App\Service\CustomerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Shuchkin\SimpleXLSX;

class WhatsAppController extends Controller
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
    public function getWhatsAppLog(string $uid): JsonResponse
    {
        $whatsLog = WhatsappLog::where('uid', $uid)->first();
        if(!$whatsLog){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Whatsapp Log uid'
            ],404);
        }

        return response()->json([
            'status' => 'success',
            'whats_log' => new GetWhatsAppLogResource($whatsLog),
        ],201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request,[
            'contact' => 'required|array|min:1',
            'contact.*.number' => 'required|max:255',
            'contact.*.message' => 'required'
        ]);

        $general         = GeneralSetting::first();
        $user            = User::where('api_key', $request->header('Api-key'))->first();
        $admin           = Admin::where('api_key', $request->header('Api-key'))->first();
        $whatsAppHistory = collect(); $whatsappGateway = null;
        $setTimeInDelay  = Carbon::now();

        if($request->input('schedule') == 2){
            $setTimeInDelay = $request->input('schedule_date');
        }
        if($user){
            
            $whatsappGateway = WhatsappDevice::where("type", WhatsappDevice::NODE)->where('user_id', $user->id)->where('status', 'connected')->first();
        }

        if($admin){
            $whatsappGateway = WhatsappDevice::where("type", WhatsappDevice::NODE)->where('admin_id', $admin->id)->where('status', 'connected')->first();
        }

        if(is_null($whatsappGateway)){
            return response()->json([
                'status' => 'error',
                'message' => 'Not available WhatsApp Gateway'
            ],403);
        }

        $setWhatsAppGateway =  $whatsappGateway->pluck('id')->toArray();
        $i = 1; $addSecond = 50;
        foreach($request->input('contact') as $value){
            if($user){
                $messages = str_split($request->input('message'),$general->whatsapp_word_count);
                $totalCredit = count($messages);

                if($totalCredit > $user->whatsapp_credit){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You do not have a sufficient credit for send message'
                    ],403);
                }

                $this->customerService->deductWhatsAppCredit($user, $totalCredit, 1);
            }

            $postData = [
                'type' => Arr::get($value,'media'),
                'url_file' => Arr::get($value,'url'),
                'name' => Arr::get($value,'url')
            ];

            foreach ($setWhatsAppGateway as $key => $appGateway){
              
              	

                $gateway = $whatsappGateway->where('id',$appGateway)->first();
                $rand = rand($gateway->min_delay ,$gateway->max_delay);
                $addSecond = $i * $rand;
                unset($setWhatsAppGateway[$key]);
                
                if(empty($setWhatsAppGateway)){
                    
                    $setWhatsAppGateway = $whatsappGateway->pluck('id')->toArray();
                  	$i++;
                }
                break;
            }
            $log = new WhatsappLog();
            $log->user_id = $user ? $user->id : null;
            $log->word_length = $general->whatsapp_word_count;
            $log->whatsapp_id = $whatsappGateway->id ;
            $log->to = Arr::get($value, 'number');
            $log->message = str_replace('{{name}}',Arr::get($value, 'number'),offensiveMsgBlock(Arr::get($value, 'message')));
            $log->status =  1;
            foreach($postData as $k => $v){
                if($v != null) {
                    $log->file_info = $postData;
                }
                else{
                    $log->file_info = null;
                }
            }
            $log->initiated_time = $setTimeInDelay;
            $log->save();
            $whatsAppHistory->push(new WhatsAppLogResource($log));
           
            if($log->status == WhatsappLog::PENDING){ 
                ProcessWhatsapp::dispatch($log)->delay(Carbon::parse($setTimeInDelay)->addSeconds($addSecond));
            }
        }

        return response()->json([
            'status' => 'success',
            'whatsapp_logs' => $whatsAppHistory->toArray(),
            'message' => 'New WhatsApp Message request sent, please see in the WhatsApp Log history for final status'
        ],201);
    }
}