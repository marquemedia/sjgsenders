<?php
namespace App\Http\Utility;
use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Models\GeneralSetting;
use App\Models\EmailTemplates;
use GuzzleHttp\Client;
use App\Models\Gateway;
use Aws\Ses\SesClient;
use Mailgun\Mailgun;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class SendMail
{
    /**
     * @param $userInfo
     * @param $emailTemplate
     * @param array $code
     * 
     */
    public static function MailNotification($userInfo, $emailTemplate, array $code = [])
    {
       
        $general = GeneralSetting::first();
    	
        $emailTemplate = EmailTemplates::where('slug', $emailTemplate)->first();
        $messages = str_replace("{{username}}", @$userInfo->name, $general->email_template);
        $messages = str_replace("{{message}}", @$emailTemplate->body, $messages);
        $mailConfiguration = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('is_default', 1)->first();
    
        foreach ($code as $key => $value) {
           
            $messages = str_replace('{{' . $key . '}}', $value, $messages);
        }

        if(blank($mailConfiguration)) {
            return "No Default gateway Was Found. Could Not Notify Client!";

        } else {
            if($mailConfiguration->type == "smtp") { 
           
                self::sendSMTPMail($userInfo->email, $emailTemplate->subject, $messages, $mailConfiguration);
            }
            elseif($mailConfiguration->type == "mailjet") {
    
                self::sendSMTPMail($userInfo->email, $emailTemplate->subject, $messages, $general, $mailConfiguration); 
            } 
            elseif($mailConfiguration->type == "sendgrid") {
    
                self::sendGrid($userInfo->email, $general->site_name, $userInfo->email, @$emailTemplate->subject, $messages, @$mailConfiguration->mail_gateways->app_key);
            }
            elseif($mailConfiguration->type == "mailgun") {
                
                self::sendMailGunMail($userInfo->email, $emailTemplate->subject, $messages, $general, $mailConfiguration); 
            }
            elseif($mailConfiguration->type == "aws") {
    
                self::sendSesMail($userInfo->email, $emailTemplate->subject, $messages, $general, $mailConfiguration); 
            }
        }
        
  
        
    }

    /**
     * @param $emailFrom
     * @param $sitename
     * @param $emailTo
     * @param $subject
     * @param $messages
     * @return string|void
     */
    public static function sendPHPMail($emailFrom, $sitename, $emailTo, $subject, $messages)
    {
        $headers = "From: $sitename <$emailFrom> \r\n";
        $headers .= "Reply-To: $sitename <$emailFrom> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
        try {
            @mail($emailTo, $subject, $messages, $headers);
        } catch (\Exception $e) {
            return $e->getMessage();
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
    public static function sendSMTPMail($emailTo, $subject, $message, $mailConfiguration)
    {
        try{
            $transport = Transport::fromDsn(sprintf(
                'smtp://%s:%s@%s:%d?encryption=%s',
                $mailConfiguration->mail_gateways->username,
                $mailConfiguration->mail_gateways->password,
                $mailConfiguration->mail_gateways->host,
                $mailConfiguration->mail_gateways->port,
                $mailConfiguration->mail_gateways->encryption,
                
            ));
            $mailer = new Mailer($transport);
            $email = (new Email())
            ->from($mailConfiguration->address)
            ->to($emailTo)
            ->subject($subject)
            ->html($message);
     
            $mailer->send($email);
        }catch (\Exception $e){
            
            return $e->getMessage();
        }
    }

    /**
     * @param $emailFrom
     * @param $fromName
     * @param $emailTo
     * @param $subject
     * @param $messages
     * @param $credentials
     * @return string|void
     */
    public static function sendGrid($emailFrom, $fromName, $emailTo, $subject, $messages, $credentials)
    {
        try{
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($emailFrom, $fromName);
            $email->setSubject($subject);
            $email->addTo($emailTo);
            $email->addContent("text/html", $messages);
            $sendgrid = new \SendGrid($credentials);
            $sendgrid->send($email);
        }catch(\Exception $e){
            return $e->getMessage();
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
    public static function sendMailJetMail($emailTo, $subject, $messages, $generalSetting, $gateway) {

       
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
                
                if ($response->Messages[0]->Status != 'success') {
                   $result = $response;
                }
              
            }
        } catch (\Exception $e) {
            $result = $e;
           

        }
        
        return $result;
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
