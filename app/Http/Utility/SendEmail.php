<?php
namespace App\Http\Utility;

use App\Models\CampaignContact;
use App\Models\EmailCreditLog;
use App\Service\EmailService;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;
use App\Models\User;
use SendGrid\Mail\TypeException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Arr;
use GuzzleHttp\Client;
use Aws\Ses\SesClient;
use Mailgun\Mailgun;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class SendEmail
{
    /**
     * @param $emailFrom
     * @param $sitename
     * @param $emailTo
     * @param $subject
     * @param $messages
     * @param $emailLog
     * @return void
     */
    public static function sendPHPMail($emailFrom, $sitename, $emailTo, $subject, $messages, $emailLog): void
    {
        $headers = "From: $sitename <$emailFrom> \r\n";
        $headers .= "Reply-To: $sitename <$emailFrom> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        try {
            if($emailLog->contact_id){
               $status = "Success";
            }

            @mail($emailTo, $subject, $messages, $headers);
            $emailLog->status =  EmailLog::SUCCESS;
            $emailLog->save();
        } catch (\Exception $e) {
            EmailService::emailSendFailed($emailLog, $e->getMessage());
           
            if($emailLog->contact_id){
                $status = "Fail";
            }
        }
        if($emailLog->contact_id){
            CampaignContact::where('id',$emailLog->contact_id)->update([
                "status" => $status
            ]);
        }
        


    }
   
    /**
     * @param $emailFrom
     * @param $fromName
     * @param $emailTo
     * @param $replyTo
     * @param $subject
     * @param $messages
     * @param $emailLog
     * @return void
     */
    public static function sendSMTPMail($emailTo, $replyTo, $subject, $messages, $emailLog, $emailMethod, $emailFromName): void
    {
        try{
            $transport = Transport::fromDsn(sprintf(
                'smtp://%s:%s@%s:%d?encryption=%s',
                $emailMethod->mail_gateways->username,
                $emailMethod->mail_gateways->password,
                $emailMethod->mail_gateways->host,
                $emailMethod->mail_gateways->port,
                $emailMethod->mail_gateways->encryption,
                
            ));
            $mailer = new Mailer($transport);
            $email = (new Email())
            ->from(new Address($emailMethod->address, $emailFromName))
            ->to($emailTo)
            ->replyTo($replyTo)
            ->subject($subject)
            ->html($messages);
            $mailer->send($email);

            if ($emailLog->contact_id) {
                $status = "Success";
            }
            $emailLog->status       = EmailLog::SUCCESS;
            $emailLog->delivered_at = now();
            $emailLog->save();
        }catch (\Exception $e){
            
            EmailService::emailSendFailed($emailLog, $e->getMessage());
            if ($emailLog->contact_id) {
                $status = "Fail";
            }
        }
       

        if ($emailLog->contact_id) {
            CampaignContact::where('id', $emailLog->contact_id)->update([
                "status" => $status
            ]);
        }
    }

    /**
     * @param $emailFrom
     * @param $fromName
     * @param $emailTo
     * @param $subject
     * @param $messages
     * @param $emailLog
     * @param $credentials
     * @return void
     * @throws TypeException
     */
    public static function sendGrid($emailFrom, $fromName, $emailTo, $subject, $messages, $emailLog, $credentials): void
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($emailFrom, $fromName);
        $email->addTo($emailTo);
        $email->setSubject($subject);
        $email->addContent("text/html", $messages);
        $sendgrid = new \SendGrid(@$credentials);

        try {
            $response = $sendgrid->send($email);
            if (!in_array($response->statusCode(), ['201','200','202'])) {
                $emailLog->status =  EmailLog::FAILED;
                $emailLog->response_gateway  = "Error";
                $emailLog->save();
                $user = User::find($emailLog->user_id);
                if ($user!='') {
                    $user->email_credit += 1;
                    $user->save();
                }
                if($emailLog->contact_id){
                    $status = "Fail";
                }
            }else{
                if($emailLog->contact_id){
                    $status = "Success";
                 }
                $emailLog->status =  EmailLog::SUCCESS;
                $emailLog->save();
            }
        }catch (\Exception $e) {
            EmailService::emailSendFailed($emailLog, $e->getMessage());
            if($emailLog->contact_id){
                $status = "Fail";
            }
        }
        if($emailLog->contact_id){
            CampaignContact::where('id',$emailLog->contact_id)->update([
                "status" => $status
            ]);
        }
    }
    /**
     * @param $emailFrom
     * @param $emailTo
     * @param $fromName
     * @param $subject
     * @param $messages
     * @return string|void
     */
    public static function sendMailJetMail($emailTo, $subject, $messages, $emailLog, $gateway) {

        $mailCredential = $gateway->mail_gateways;
        $result = null;
        $emailParts = explode('@', $emailTo);
        $receiver = $emailParts[0];
       
        try {
            $body = [
                'Messages' => [
                    [
                    'From' => [
                        'Email' => $gateway->address,
                        'Name'  => $gateway->name
                    ],
                    'To' => [
                        [
                            'Email' => $emailTo,
                            'Name'  => $receiver
                        ]
                    ],
                    'Subject'  => $subject,
                    "TextPart" => " ",
                    'HTMLPart' => $messages
                    ]
                ]
            ];
            $client = new Client([
                'base_uri' => 'https://api.mailjet.com/v3.1/',
            ]);
 
            $response = $client->request('POST', 'send', [
                'json' => $body,
                'auth' => [$mailCredential->api_key, $mailCredential->secret_key]
            ]);
            if($response->getStatusCode() == 200) {
                $body = $response->getBody();
                $response = json_decode($body);
                
                if ($response->Messages[0]->Status == 'success') {
                    if ($emailLog->contact_id) {
                        $status = "Success";
                    }
                    $emailLog->status = EmailLog::SUCCESS;
                    $emailLog->delivered_at = now();
                    $emailLog->save();
                    
                    Artisan::call('optimize:clear');
                    \Illuminate\Support\Facades\Artisan::call('queue:restart');
                }
                else{
                    EmailService::emailSendFailed($emailLog, $e->getMessage());
                    if ($emailLog->contact_id) {
                        $status = "Fail";
                    }
                }
            }

          
        } catch (\Exception $e) {
            EmailService::emailSendFailed($emailLog, $e->getMessage());
            if ($emailLog->contact_id) {
                $status = "Fail";
            }
        }
        return $status;
    }


     /**
     * send mail using ses
     *
     */
    public static function sendSesMail($recipient_emails, $subject, $messages, $generalSetting, $gateway) {

        $result = null;
        $mailCredential = $gateway->mail_gateways;
       
        try {
            $SesClient = new SesClient([
                'profile' => $mailCredential->profile,
                'version' => $mailCredential->version,
                'region'  => $mailCredential->region
            ]);
            $sender_email = $gateway->address;
            $configuration_set = 'ConfigSet';
            $html_body = $messages;
            $char_set = 'UTF-8';
            $result = $SesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                'ReplyToAddresses' => [$sender_email],
                'Source' => $sender_email,
                'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => $char_set,
                        'Data' => $html_body,
                    ],
                ],
                'Subject' => [
                    'Charset' => $char_set,
                    'Data' => $subject,
                ],
                ],
                'ConfigurationSetName' => $configuration_set,
            ]);
           
        } catch (\Exception $e) {

          $result = $result;
        }
        
        return $result;

    }

    /**
     * send mail using MailGun
     *
     * @param $details , $email
     */
    public static function sendMailGunMail($recipient_email, $subject, $messages, $generalSetting, $gateway){
        $result = null;
        $mailCredential = $gateway->mail_gateways;
        $mailGun = Mailgun::create($mailCredential->secret_key);
        $domain = $mailCredential->verified_domain;
        try {
            $mailGun->messages()->send( $domain, [
                'from'    => $gateway->address,
                'to'      => $recipient_email,
                'subject' => $subject,
                'html'    => $messages
            ]);
        } catch (\Exception $e) {

            $result = $result;
        }
        return $result;
    }
}
