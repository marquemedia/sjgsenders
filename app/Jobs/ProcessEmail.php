<?php

namespace App\Jobs;

use Illuminate\Support\Arr;
use App\Models\EmailLog;
use App\Models\User;
use App\Models\Admin;
use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Utility\SendEmail;
use App\Models\Gateway;
use Exception;
use SendGrid\Mail\TypeException;


class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailLog;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailLog){

        $this->emailLog = $emailLog;
    }


    /**
     * @return void
     * @throws TypeException
     */
    public function handle(): void
    {
        if($this->emailLog->status != 3) {

            $general       = GeneralSetting::first();
            $emailTo       = $this->emailLog->to;
            $subject       = $this->emailLog->subject;
            $messages      = $this->emailLog->message;
            $emailFrom     = $general->mail_from;
            $emailFromName = $general->site_name;
            $emailReplyTo  = $general->mail_from;
            $emailMethod   = Gateway::whereNotNull('mail_gateways')->where('status',1)->where('id', $this->emailLog->sender_id)->first();
            $user          = User::where('id', $this->emailLog->user_id)->first();

            if(is_null($this->emailLog->user_id)) { 

                $admin          = Admin::first();
                $emailFrom      = $emailMethod->address;
                $emailFromName  = is_null($this->emailLog->from_name) ? $emailMethod->name : $this->emailLog->from_name;
                $emailReplyTo   = is_null($this->emailLog->reply_to_email) ? $admin->email : $this->emailLog->reply_to_email;
            }

            if($user) {

                $emailMethod    = Gateway::whereNotNull('mail_gateways')->where('status',1)->where('id', $this->emailLog->sender_id)->firstOrFail();
                $emailFrom      = $emailMethod->address;
                $emailFromName  = $this->emailLog->from_name ?? $emailMethod->name;
                $emailReplyTo   = $this->emailLog->reply_to_email ?? $user->email;
            }

            if($this->emailLog->sender->type == 'smtp') {
                SendEmail::sendSMTPMail($emailTo, $emailReplyTo, $subject, $messages, $this->emailLog,  $emailMethod, $emailFromName);
            }
            elseif($this->emailLog->sender->type == "mailjet"){
                SendEmail::sendMailJetMail($emailFrom, $subject, $messages, $this->emailLog, $emailMethod);
            }
            elseif($this->emailLog->sender->type == "aws") {
                SendEmail::sendSesMail($emailFrom, $subject, $messages, $general, $emailMethod); 
            }
            elseif($this->emailLog->sender->type  == "mailgun") {
                
                SendEmail::sendMailGunMail($emailFrom, $subject, $messages, $general, $emailMethod); 
            }
            elseif($this->emailLog->sender->typ == "sendgrid"){
                $apiKey = $this->getSendGridApiKey($this->emailLog, $emailMethod, $user);
                SendEmail::sendGrid($emailFrom, $emailFromName, $emailTo, $subject, $messages, $this->emailLog, $apiKey);
            }
        }
    }

    /**
     * @param EmailLog $this->emailLog
     * @param Gateway $emailMethod
     * @param User|null $user
     * @return string
     */
    private function getSendGridApiKey(EmailLog $emailLog, Gateway $emailMethod, ?User $user): string
    {
        $apiKey = '';

        if(is_null($emailLog->user_id)) {

            $apiKey = @$emailMethod->mail_gateways->app_key;
        }

        if ($user && !is_null($user->gateways_credentials)) {

            $apiKey = Arr::get($user->gateways_credentials, 'email.send_grid_api.app_key', '###');
        }
        return $apiKey;
    }
}
