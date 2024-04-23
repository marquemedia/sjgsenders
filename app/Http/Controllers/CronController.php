<?php

namespace App\Http\Controllers;

use App\Http\Utility\SendMail;
use App\Service\SmsService;
use App\Models\AndroidApi;
use App\Models\AndroidApiSimInfo;
use App\Models\SMSlog;
use App\Models\Subscription;
use App\Models\GeneralSetting;
use App\Models\EmailLog;
use Carbon\Carbon;
use App\Jobs\ProcessEmail;
use App\Jobs\ProcessWhatsapp;
use App\Models\Campaign;
use App\Models\CampaignContact;
use App\Models\CreditLog;
use App\Models\EmailCreditLog;
use App\Models\Import;
use App\Models\Gateway;
use App\Models\PricingPlan;
use App\Models\User;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Log;
use App\Service\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class CronController extends Controller
{

    public SmsService $smsService;
    public WhatsAppService $whatsAppService;
    public function __construct(SmsService $smsService, WhatsAppService $whatsAppService)
    {
        $this->smsService = $smsService;
        $this->whatsAppService = $whatsAppService;
    }

    public function run(): void
    {
       
        $setting = GeneralSetting::first();
        $this->smsSchedule();
        $this->whatsAppSchedule();
        $this->emailSchedule();
        $this->unlinkImportFile();
        $this->gatewayCheck();
        $this->androidApiSim();
        $this->subscription();
        if(Carbon::parse($setting->schedule_at)->addMinute(30) < Carbon::now()){
            $setting->schedule_at = Carbon::now();
        }
        $setting->cron_job_run = Carbon::now();
        
        $setting->save();
        $this->campaignSchedule();
    }


    public function campaignSchedule(): void
    {
       
        $campaigns = Campaign::with(['contacts','schedule'])
            ->where('status','Active')
            ->orWhere('status','Completed')
            ->get();
        
        $onCampaigns = Campaign::where('status','Ongoing')->get();
       
        foreach($onCampaigns as $campaign){
            
            $contacts = CampaignContact::where('campaign_id',$campaign->id)->where('status','Processing')->count();
 
            if( $contacts == 0){
                $campaign->status = 'Completed';
              
                $campaign->save();
            }
        }
        
        $this->processCampaign($campaigns);
    }

    public function processCampaign($campaigns): void
    {
        
        foreach($campaigns as $campaign){
            
            $expiredTime = $campaign->schedule_time;
           
            $now = Carbon::now()->toDateTimeString();
            
           
            if ($now >= $expiredTime &&  $campaign->status != 'Ongoing') {
                
                if($campaign->status =='Completed' && !$campaign->schedule){
                    continue;
                }
                if($campaign->channel == Campaign::EMAIL){
                    $this->processEmailCampaign($campaign);
                }
                elseif($campaign->channel == Campaign::SMS){
                    
                    $this->processSmsCampaign($campaign);
                }
                else{
                    $this->processWhatsappCampaign($campaign);
                }
                $campaign->last_corn_run = Carbon::now();
                $campaign->status = 'Ongoing';
                $campaign->save();
                if($campaign->schedule){

                    $days = self::getRepeatDay($campaign->schedule);
                    $rescheduleDate = Carbon::parse($campaign->last_corn_run)->addDays($days);
                    
                    $campaign->schedule_status = 'Later';
                    $campaign->schedule_time =  $rescheduleDate ;
                    $campaign->save();
                }
            }
        }
    }


    public function processWhatsappCampaign($campaign): void {

        $contacts = CampaignContact::where('campaign_id',$campaign->id)->get();
        $general = GeneralSetting::first();
        $wordLenght = $general->whatsapp_word_count;
        $flag = 1;
        $whatsappGateway = WhatsappDevice::whereNull('user_id')->where('status', 'connected')->get();

        if($campaign->user_id) {

            $user = User::where('id',$campaign->user_id)->first();
            $allowed_access = (object) planAccess($user);
            if($user) {

                if($campaign->json_body->method == "whatsapp") {

                    $whatsappGateway = $allowed_access->type == PricingPlan::USER ? WhatsappDevice::where('user_id', $user->id)->where("type", WhatsappDevice::NODE)->where('status', 'connected')->get() 
                                   : WhatsappDevice::whereNull('user_id')->where("type", WhatsappDevice::NODE)->where('status', 'connected')->get();
                }
                if($campaign->json_body->method == "whatsapp_cloud") {

                    $whatsappGateway = $allowed_access->type == PricingPlan::USER ? WhatsappDevice::where('user_id', $user->id)->where("type", WhatsappDevice::BUSINESS)->where('status', 'connected')->get() 
                                   : WhatsappDevice::whereNull('user_id')->where("type", WhatsappDevice::BUSINESS)->where('status', 'connected')->get();
                }

                $messages = str_split($campaign->body,$wordLenght);
                $totalMessage = count($messages);
                $totalNumber = count($contacts);
                $totalCredit = $totalNumber * $totalMessage;
                
                if($totalCredit > $user->whatsapp_credit){
                    $flag = 0;
                    $campaign->status = 'Active';
                    $campaign->save();
                    $mailCode = [
                        'type' => $campaign->channel,
                        'name' => $campaign->name,
                        'credit_balance' => $user->whatsapp_credit,
                    ];
                    SendMail::MailNotification($user,'INSUFFICIENT_CREDIT',$mailCode);
                }
                else{
                    $user->whatsapp_credit -=  $totalCredit;
                    $user->save();
                    $creditInfo = new  WhatsappCreditLog();
                    $creditInfo->user_id = $user->id;
                    $creditInfo->type = "-";
                    $creditInfo->credit = $totalCredit;
                    $creditInfo->trx_number = trxNumber();
                    $creditInfo->post_credit =  $user->whatsapp_credit;
                    $creditInfo->details = $totalCredit." credits were cut for " .$totalNumber . " number send message";
                    $creditInfo->save();
                }
            }
        }

        if(count($whatsappGateway) == 0){
            $flag = 0;
        }
        
        if($flag == 1) {
            $setWhatsAppGateway =  $whatsappGateway->pluck('id')->toArray();
            $postData = [];
            
            if($campaign->post_data){

                $postData = $campaign->post_data;
            }
        
            $i = 1; $addSecond = 50;
            foreach($contacts as $index=>$contact){

                if(filter_var($contact->contact, FILTER_SANITIZE_NUMBER_INT)){

                    if($campaign->json_body->method == "whatsapp") {
                        
                        foreach ($setWhatsAppGateway as $key => $appGateway){

                            $gateway = $whatsappGateway->where('id',$appGateway)->first();
                            $rand = rand($gateway->credentials["min_delay"] ,$gateway->credentials["max_delay"]);
                            $addSecond = $i * $rand;
                            unset($setWhatsAppGateway[$key]);
                            if(empty($setWhatsAppGateway)){
                                $setWhatsAppGateway = $whatsappGateway->pluck('id')->toArray();
                                $i++;
                            }
                            break;
                        }
                    }
                    
                    
                    $log = new WhatsappLog();
                    $log->user_id = $campaign->user_id;

                    if ($campaign->json_body->method == "whatsapp" || $campaign->json_body->method == "whatsapp_cloud" && $campaign->sender_id == "-1") {
                        
                        $log->whatsapp_id = $setWhatsAppGateway[array_rand($setWhatsAppGateway)];
        
                    } else {
                        $log->whatsapp_id = $campaign->sender_id ?? null;
                    }
                    
                    $log->to = preg_replace('/[^0-9]/', '', trim(str_replace('+', '', $contact->contact)));
                    $log->contact_id = $contact->id;
                    $log->mode = $campaign->json_body->method == "whatsapp_cloud" ? WhatsappLog::CLOUD_API : WhatsappLog::NODE;
                    $log->campaign_id = $campaign->id;
                    $log->template_id = $campaign->json_body->template_id;
                    $log->initiated_time = Carbon::now();
                    $log->file_info = $postData == null ? null : $postData;
                    $log->message = $contact->message;
                    $log->word_length  = $wordLenght;
                    $log->status = 1;
                    $log->schedule_status = 1;
                    $log->save();
                    $contact->status = "Processing";
                    $contact->save();
                    ProcessWhatsapp::dispatch($log)->delay(Carbon::now()->addSeconds($addSecond));
                }
            }
        }
    }


    public function processEmailCampaign($campaign): void
    {
        
        $contacts = CampaignContact::where('campaign_id',$campaign->id)->get();

        $flag = 1;
        if ($campaign->user_id) {

            $user = User::where('id',$campaign->user_id)->first();
            if($user){
                if(count($contacts) > $user->email_credit ){
                    $campaign->status = 'Active';
                    $campaign->save();
                    $flag = 0;
                    $mailCode = [
                        'type' => $campaign->channel,
                        'name' => $campaign->name,
                        'credit_balance' => $user->email_credit,
                    ];
                    SendMail::MailNotification($user,'INSUFFICIENT_CREDIT',$mailCode);
                }
                else{
                    $user->email_credit -= count($contacts);
                    $user->save();

                    $emailCredit = new EmailCreditLog();
                    $emailCredit->user_id = $user->id;
                    $emailCredit->type = "-";
                    $emailCredit->credit = count($contacts);
                    $emailCredit->trx_number = trxNumber();
                    $emailCredit->post_credit =  $user->email_credit;
                    $emailCredit->details = count($contacts)." credits were cut for send email";
                    $emailCredit->save();
                }
            }
        }

        if($flag == 1){
            foreach($contacts  as $contact){
                $emailLog                 = new EmailLog();
                $emailLog->user_id        = $campaign->user_id;
                $emailLog->campaign_id    = $campaign->id;
                $emailLog->contact_id     = $contact->id;
                $emailLog->sender_id      = $campaign->sender_id;
                $emailLog->from_name      = $campaign->from_name;
                $emailLog->reply_to_email = $campaign->reply_to_email;
                $emailLog->to             = $contact->contact;
                $emailLog->message        = $contact->message;
                $emailLog->subject        = $campaign->subject;
                $emailLog->status         = 1;
                $emailLog->initiated_time = Carbon::now();
                $emailLog->save();
                $contact->status          = "Processing";
                $contact->save();

                ProcessEmail::dispatch($emailLog);
            }
        }

    }


    public function processSmsCampaign($campaign): void {

        $flag       = 1;
        $general    = GeneralSetting::first();
        $user       = User::where('id',$campaign->user_id)->first();
        $smsGateway = Gateway::where('id', $campaign->sender_id)->first();
        
        $contacts   = CampaignContact::where('campaign_id',$campaign->id)->get();
        $wordLenght = $campaign->sms_type == "plain" ? $general->sms_word_text_count : $general->sms_word_unicode_count;
        
        if ($campaign->user_id) {
            
            $messages     = str_split($campaign->body,$wordLenght);
            $totalMessage = count($messages);
            $totalNumber  = count($contacts);
            $totalCredit  = $totalNumber * $totalMessage;
            
            if ($totalCredit > $user->credit) {

                $flag             = 0;
                $campaign->status = 'Active';
                $campaign->save();
                
                $mailCode = [
                    'type'           => $campaign->channel,
                    'name'           => $campaign->name,
                    'credit_balance' => $user->credit,
                ];

                SendMail::MailNotification($user,'INSUFFICIENT_CREDIT',$mailCode);
            }
            else {

                $user->credit -= $totalCredit;
                $user->save();
                $creditInfo              = new CreditLog();
                $creditInfo->user_id     = $user->id;
                $creditInfo->credit_type = "-";
                $creditInfo->credit      = $totalCredit;
                $creditInfo->trx_number  = trxNumber();
                $creditInfo->post_credit = $user->credit;
                $creditInfo->details     = $totalCredit." credits were cut for " .$totalNumber . " number send message";
                $creditInfo->save();
            }
            
            if(!$smsGateway && $user->sms_gateway == 1){
            
                $flag = 0;
            }
        } else {
            
            if(!$smsGateway && $general->sms_gateway == 1){
            
                $flag = 0;
            }
        }
        if ($flag == 1) {
                
            foreach($contacts  as $contact) {
                
                if(filter_var($contact->contact, FILTER_SANITIZE_NUMBER_INT)) {
                    
                    $log                 = new SMSlog();
                    $log->campaign_id    = $campaign->id;
                    $log->contact_id     = $contact->id;
                    $log->api_gateway_id = $campaign->json_body->method == "api" ? $campaign->sender_id : null;
                    $log->sms_type       = $campaign->sms_type == "plain" ? 1 : 2;
                    $log->user_id        = $user ? $user->id : null;
                    $log->word_length    = $wordLenght;
                    $log->to             = preg_replace('/[^0-9]/', '', trim(str_replace('+', '', $contact->contact)));
                    $log->initiated_time = Carbon::now() ;
                    $log->message        = $contact->message;
                    $log->status         = 1;
                    
                    $contact->status = "Processing";

                    if ($campaign->json_body->method == "android" && $campaign->sender_id == "-1") {
                       
                        $allAvailableSims = AndroidApiSimInfo::where("status", AndroidApiSimInfo::ACTIVE)->pluck("id")->toArray();
                        $log->android_gateway_sim_id = $allAvailableSims[array_rand($allAvailableSims)];

                    } else {
                        $log->android_gateway_sim_id = $campaign->sender_id ?? null;
                    }
                    $log->save();
                    $contact->save();
                    $this->smsService->sendSmsByOwnGateway($log);
                }
            }
        }
    }

    public static function getRepeatDay($schedule) {

        if ($schedule->repeat_format == 'day') {

            return $schedule->repeat_number;
        }
        if ($schedule->repeat_format == 'month') {

            return days_in_month(date('m'),date('Y')) * $schedule->repeat_number;
        }
        if ($schedule->repeat_format == 'year') {

            return days_in_year() * $schedule->repeat_number;
        }
    }

    protected function androidApiSim(): void {
      	
        $smslogs = SMSlog::whereNull('api_gateway_id')->whereNull('android_gateway_sim_id')->where('status', 1)->get();
	
        foreach ($smslogs as $smslog) {

            $androidSimInfos = [];
            if ($smslog->user_id) {
                
                $user           = User::find($smslog->user_id);
                $allowed_access = (object)planAccess($user);
              
              	if($allowed_access->android["is_allowed"]) {
                
                	if($allowed_access->type == PricingPlan::USER ) {
                      
                        $androidApis     = AndroidApi::where('status', 1)->where('user_id', $smslog->user_id)->pluck('id')->toArray();
                        $androidSimInfos = AndroidApiSimInfo::whereIn('android_gateway_id', $androidApis)->where('status', 1)->pluck('id')->toArray();
                    } else {
                      
                        $androidApis     = AndroidApi::where('status', 1)->whereNotNull('admin_id')->pluck('id')->toArray();
                        $androidSimInfos = AndroidApiSimInfo::whereIn('android_gateway_id', $androidApis)->where('status', 1)->pluck('id')->toArray();
                    }
                }
            }
			
            if (is_null($smslog->user_id)) {
				
                $androidApis     = AndroidApi::where('status', 1)->whereNotNull('admin_id')->pluck('id')->toArray();
                $androidSimInfos = AndroidApiSimInfo::whereIn('android_gateway_id', $androidApis)->where('status', 1)->pluck('id')->toArray();
            	
            }
            if (!empty($androidSimInfos)) {
                $smslog->android_gateway_sim_id = $androidSimInfos[array_rand($androidSimInfos,1)];
                $smslog->save();
            }
        }

    }

    public function unlinkImportFile(): void
    {
        $imports = Import::where('status',1)->get();
        foreach($imports  as $import){
            if(@unlink(('assets/file/import/'.$import->path))){
                $import->delete();
            }
        }
    }

    protected function gatewayCheck(): void
    {
        $smslogs = SMSlog::whereNotNull('android_gateway_sim_id')->where('status', 1)->get();
        foreach ($smslogs as $key => $smslog) {
            if (isset($smslog->androidGateway)) {
                if($smslog->androidGateway->status == 2){
                    $smslog->android_gateway_sim_id = null;
                    $smslog->save();
                }
            }
        }
    }


    protected function subscription(): void
    {
        $subscriptions = Subscription::where('status',Subscription::RUNNING)->orWhere('status',Subscription::RENEWED)->get();
        foreach($subscriptions as $subscription){
            $expiredTime = $subscription->expired_date;
            $now = Carbon::now()->toDateTimeString();
            if($now > $expiredTime){
                $subscription->status = 2;
                $subscription->save();
            }
        }
    }

    protected function smsSchedule(): void
    {
        $smslogs = SMSlog::where('status', 2)->where('schedule_status', 2)->get();
        $general = GeneralSetting::first();

        foreach($smslogs as $smslog) {

            $expiredTime = Carbon::parse($smslog->initiated_time);
            $now = Carbon::parse(Carbon::now()->toDateTimeString());

            $diffInSeconds = $expiredTime->diffInSeconds($now);
            
            if($smslog->user_id){
                $smsGateway = $smslog->user->sms_gateway;
            }else{
                $smsGateway = $general->sms_gateway;
            }

            if($smsGateway == 1){
                $smslog->status = 1;
                $this->smsService->sendSmsByOwnGateway($smslog, $diffInSeconds);
            }else{
                $smslog->status = 1;
                $smslog->api_gateway_id = null;
                $smslog->android_gateway_sim_id = null;
            }
            
            $smslog->save();
            
        }
    }

    protected function emailSchedule(): void
    {
        $emailLogs = EmailLog::where('status', 2)->where('schedule_status', 2)->get();
        foreach($emailLogs as $emailLog) {
           
            $expiredTime = Carbon::parse($emailLog->initiated_time);
            $now = Carbon::parse(Carbon::now()->toDateTimeString());
            $diffInSeconds = $expiredTime->diffInSeconds($now);
            $emailLog->status = 1;
            $emailLog->save();

            ProcessEmail::dispatch($emailLog)->delay(now()->addSeconds($diffInSeconds));
        }
    }

    protected function whatsAppSchedule(): void {
        
        $whatsAppLogs = WhatsappLog::where('status', 2)->where('schedule_status', 2)->get();
        $i = 1; $addSecond = 50;
        foreach($whatsAppLogs as $key => $whatsAppLog){
           
            try {
                $expiredTime = Carbon::parse($whatsAppLog->initiated_time);
                $now = Carbon::parse(Carbon::now()->toDateTimeString());
                $diffInSeconds = $expiredTime->diffInSeconds($now);
                $whatsAppLog->status = WhatsappLog::PENDING;
                $whatsAppLog->save();
                $rand = rand($whatsAppLog->whatsappGateway?->min_delay, $whatsAppLog->whatsappGateway?->max_delay);
                $addSecond = $i * $rand;
                ProcessWhatsapp::dispatch($whatsAppLog)->delay(Carbon::now()->addSeconds($addSecond + $diffInSeconds));
                
            }catch (\Exception $exception){
                Log::debug('Whatsapp Gateway');
                $this->whatsAppService->addedCredit($whatsAppLog,$exception->getMessage());
            }
        }
    }
}
