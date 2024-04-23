<?php

namespace App\Jobs;

use App\Models\CampaignContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ProcessWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected WhatsappLog $whatsappLog){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $whatsappLog = $this->whatsappLog;

        if($whatsappLog->mode == WhatsappLog::NODE && $whatsappLog->status != WhatsappLog::FAILED) {
            
            $body = [];
            
            if(!is_null($whatsappLog->file_info)){
                
                $url = Arr::get($whatsappLog->file_info, 'url_file', null);
                $type = Arr::get($whatsappLog->file_info, 'type', null);
                $name = Arr::get($whatsappLog->file_info, 'name', null);

                if(!filter_var($url, FILTER_VALIDATE_URL)){
                    $url = url($url);
                }

                if($type == "image" ) {
                    $body = [
                        'image'=>[
                            'url'=>$url
                        ],
                        'mimetype' => 'image/jpeg',
                        'caption'=> $whatsappLog->message,
                    ];
                }
                else if($type == "audio" ){
                    $body = [
                        'audio'=>[
                            'url'=>$url
                        ],
                        'caption'=> $whatsappLog->message,
                    ];
                }
                else if($type == "video" ){
                    $body = [
                        'video'=>[
                            'url'=>$url
                        ],
                        'caption'=> $whatsappLog->message,
                    ];
                }
                else if($type == "document" ){
                    $body = [
                        'document'=>[
                            'url'=>$url
                        ],
                        'mimetype' => 'application/pdf',
                        'fileName' => $name,
                        'caption'  => $whatsappLog->message,
                    ];
                }
            }
            
            else{

                $body['text'] = $whatsappLog->message;
            }
            //send api
            $response = null;
            try {

                $apiURL = env('WP_SERVER_URL').'/message/send?id='.$whatsappLog->whatsappGateway->name;
                $postInput = [
                    'receiver' => trim($whatsappLog->to),
                    'message' => $body
                ];
                $headers = [
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withoutVerifying()->withHeaders($headers)->post($apiURL, $postInput);
                
                if ($response) {
                    $res = json_decode($response->getBody(), true);

                    if($res['success']){
                        $whatsappLog->status = WhatsappLog::SUCCESS;
                        $whatsappLog->delivered_at = now();
                        $whatsappLog->save();
                        if($whatsappLog->contact_id){
                            $this->updateContact($whatsappLog->contact_id, "Success");
                        }
                    }else{
                        $this->addedCredit($whatsappLog,"Failed To Connect Gateway");
                        if($whatsappLog->contact_id){
                            $this->updateContact($whatsappLog->contact_id, "Fail");
                        }
                    }
                }else{
                    if($whatsappLog->contact_id){
                        $this->updateContact($whatsappLog->contact_id, "Fail");
                    }
                    $this->addedCredit($whatsappLog,"Failed To Connect Gateway");
                }
            } catch(\Exception $exception) {

                $this->addedCredit($whatsappLog, $exception->getMessage());
                if($whatsappLog->contact_id){
                
                    $this->updateContact($whatsappLog->contact_id, "Fail");
                }
            }
        } else {

            $cloud_api = WhatsappDevice::find($whatsappLog->whatsapp_id);
            $template  = WhatsappTemplate::find($whatsappLog->template_id);
            $default_crendetials = (object) config("setting.whatsapp_business_credentials.default");
            $gateway_credentials = (object) $cloud_api->credentials;
           
            $url = "https://graph.facebook.com/$default_crendetials->version/$gateway_credentials->phone_number_id/messages";
            
            $headers = [
                'Content-Type'  => 'application/json',
                'Authorization' => "Bearer $gateway_credentials->user_access_token",
                'Cookie'        => 'ps_l=0; ps_n=0',
            ];
            if($whatsappLog->message == []) {
                $data = [
                
                    'messaging_product' => 'whatsapp',
                    'to'                => $whatsappLog->to,
                    'type'              => 'template',
                    "template" => [
                        "name" => $template->name,
                        "language" => [
                            "code" => $template->language_code
                        ],
                        "components" => $whatsappLog->message
                    ]
                ];
            } else {
                $data = [
                    'messaging_product' => 'whatsapp',
                    'to'                => $whatsappLog->to,
                    'type'              => 'template',
                    "template" => [
                        "name" => $template->name,
                        "language" => [
                            "code" => $template->language_code
                        ],
                        "components" => $whatsappLog->message
                    ]
                ];
            }
            
            $response = Http::withHeaders($headers)->post($url, $data);
            $responseBody = $response->body();
            $responseData = json_decode($responseBody, true);
            
            if ($response->successful()) {
                $whatsappLog->message_response = $responseBody;
                $whatsappLog->status = WhatsappLog::PROCESSING;
                $whatsappLog->update();
            } else {

                $whatsappLog->message_response = $response->body();
                if(isset($responseData['error']['message'])) {
                    $this->addedCredit($whatsappLog, $responseData['error']['message']);
                }
                $whatsappLog->status       = WhatsappLog::FAILED;
                $whatsappLog->update();
            }
        }
    }

    public function updateContact($id, $status){
    
        $campaign_contact = CampaignContact::where('id',$id)->first();
        $campaign_contact->status = $status;
        $campaign_contact->save();
    }

    public function addedCredit(WhatsappLog $whatsappLog ,$gwException)
    {
        $whatsappLog->status           = WhatsappLog::FAILED;
        $whatsappLog->response_gateway = $gwException;
        $whatsappLog->save();
        $user = User::find($whatsappLog->user_id);

        if($user){
            $messages = str_split($whatsappLog->message,$whatsappLog->word_length);
            $totalcredit = count($messages);
            $user->whatsapp_credit += $totalcredit;
            $user->save();
            $creditInfo = new WhatsappCreditLog();
            $creditInfo->user_id = $whatsappLog->user_id;
            $creditInfo->type = "+";
            $creditInfo->credit = $totalcredit;
            $creditInfo->trx_number = trxNumber();
            $creditInfo->post_credit =  $user->whatsapp_credit;
            $creditInfo->details = $totalcredit." Credit Return ".$whatsappLog->to." is Falied";
            $creditInfo->save();
        }
    }
}
