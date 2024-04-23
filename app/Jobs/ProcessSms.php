<?php

namespace App\Jobs;

use App\Models\SmsGateway;
use App\Service\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Utility\SendSMS;
use App\Models\SMSlog;
use App\Models\CreditLog;
use App\Models\Gateway;
use App\Models\User;
use App\Models\GeneralSetting;
use Exception;

class ProcessSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct(protected  SMSlog $SMSlog, protected  array $credential, protected Gateway $smsGateway){}
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if($this->SMSlog->status != 3) {
            try{
                $smsLog = $this->SMSlog;
                $smsGateway = $this->smsGateway;
                $smsLog->api_gateway_id = $smsGateway->id;
                $smsLog->android_gateway_sim_id = null;
                $smsType = $smsLog->sms_type == 1 ? 'plain' : 'unicode';
                $gateways = [
                    "101NEXMO" => 'nexmo',
                    "102TWILIO" => 'twilio',
                    "103MESSAGE_BIRD" => 'messageBird',
                    "104TEXT_MAGIC" => 'textMagic',
                    "105CLICKA_TELL" => 'clickaTell',
                    "106INFOBIP" => 'infoBip',
                    "107SMS_BROADCAST" => 'smsBroadcast',
                    "108MIM_SMS" => 'mimSMS',
                    "109AJURA_SMS" => 'ajuraSMS',
                    "110MSG91" => 'msg91'
                ];
                
                if (isset($gateways[$smsGateway->type])) {
                    $gateway = $gateways[$smsGateway->type];
                    SendSMS::$gateway($smsLog->to, $smsType, $smsLog->message, (object)$this->credential, $smsLog->id);
                }
              
            }catch (\Exception $exception){
    
                echo $exception->getMessage();
            }
        }
    }
}
