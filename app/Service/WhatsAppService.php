<?php

namespace App\Service;

use App\Models\User;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappLog;
use App\Rules\MessageFileValidationRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Jobs\ProcessWhatsapp;
use App\Models\WhatsappDevice;
use App\Models\WhatsappTemplate;
use App\Service\CustomerService;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{

    public SmsService $smsService;
    public CustomerService $customerService;

    public function __construct(SmsService $smsService, CustomerService $customerService)
    {
        $this->smsService = $smsService;
        $this->customerService = $customerService;
    }

    public function fileValidationRule(Request $request): void
    {
        $files = ['document', 'audio', 'image', 'video'];
        $message = 'message';
        $rules = 'required';

        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $message = $file;
                $rules = ['required', new MessageFileValidationRule($file)];
                break;
            }
        }

        $request->validate([
            $message => $rules,
        ]);
    }

    /**
     * @param $request
     * @return array|null
     */
    public function findAndUploadFile($request): ?array
    {
        
        $fileTypes = ['image', 'document', 'audio', 'video'];
       
        foreach ($fileTypes as $fileType) {

           
            if ($request->hasFile($fileType)) {

                $file = $request->file($fileType);
                $fileName = uniqid().time().'.'.$file->getClientOriginalExtension();
               
                $path = filePath()['whatsapp']['path_'.$fileType];
                
                if(!file_exists($path)){
                    mkdir($path, 0777, true);
                }
                try {
                    $file->move($path, $fileName);
                    
                    return [
                        'type' => $fileType,
                        'url_file' => $path . '/' . $fileName,
                        'name' => $fileName
                    ];
                } catch (\Exception $e) {

                    return [];
                }
            }
        }

        return [];
    }


    /**
     * @param Request $request
     * @param array $contactNewArray
     * @param int $wordLength
     * @param array $numberGroupName
     * @param array $whatsappGateway
     * @param int|null $userId
     * @return void
     */
    public function save(Request $request, array $contactNewArray, int $wordLength, array $numberGroupName, array $allAvailableWaGateway, ?int $userId = null, mixed $templateData = null, mixed $allowed_access = null): void {
        
        $postData       = $this->findAndUploadFile(request());
        $setTimeInDelay = Carbon::now();
        
        if ($request->input('schedule') == 2) {    

            $setTimeInDelay = $request->input('schedule_date');
        }

        if($request->input("cloud_api") == "true") {
            
            $template_message = $templateData["template_information"];
            $request_data     = $request->all();
            $matches = []; $i = 0; $message = []; $data = [];
            
            foreach ($request_data as $request_key => $request_value) {

                if (str_contains($request_key, "_placeholder_")) {

                    preg_match('/([a-z]+)_placeholder_(\d+)/', $request_key, $match);
                    $matches[] = $match;
                    $data[$request_key] = $request_value;
                }
                if (str_contains($request_key, "_header_media")) {

                    $fileType = explode('_', $request_key)[0];
                    $fileLink = "";

                    if ($fileType == "image") { $fileLink = storeCloudMediaAndGetLink('image_header_media', $request->file('image_header_media')); } 
                    elseif ($fileType == "video") { $fileLink = storeCloudMediaAndGetLink('image_header_media', $request->file('image_header_media')); } 
                    elseif ($fileType == "document") { $fileLink = storeCloudMediaAndGetLink('image_header_media', $request->file('image_header_media')); }

                    preg_match('/([a-z]+)_header_media/', $request_key, $match);
                    $match[] = "header_media"; 
                    $match[] = $fileLink; 
                    $matches[] = $match;
                    $data[$request_key] = $request_value;
                }
                if (str_contains($request_key, "_button_")) {
                    preg_match('/([a-z]+)_button_(\d+)/', $request_key, $match);
                
                    $match[] = $request_value; 
                    $matches[] = $match;
                    $data[] = $match; 
                }
            }
            array_column($matches, 1);
            $k = 0;
            
            foreach ($matches as $value) {
               
                $type                 = strtoupper($value[1]); 
                $number               = $value[2];
                $template_message_key = array_search($type, array_column($template_message, 'type'));
                
                if ($template_message_key !== false || preg_match('/button/', $value[0]) || preg_match('/_header_media/', $value[0])) {
                    
                    if ($value[1] == "header") {
                    
                        foreach($template_message[$template_message_key]['example']["$value[1]_text"] as $template_key => $template_value) {
                            
                            $message[$template_message_key]["type"]      = strtolower($template_message[$template_message_key]["type"]);
                            $message[$template_message_key]["parameters"][] = [
                                "type" => strtolower($template_message[$template_message_key]["format"]),
                                strtolower($template_message[$template_message_key]["format"]) => $request_data["$value[1]_placeholder_$template_key"]
                            ];
                        }
                    } elseif ($value[1] == "reply") {

                        $message[] = [
                            "type" => "button",
                            "sub_type" => "QUICK_REPLY",
                            "index" => $value[2],
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $value[3],
                                ]
                            ],
                        ];

                    } elseif ($value[1] == "code") {
                        
                        $message[3] = [
                            "type" => "button",
                            "sub_type" => "COPY_CODE",
                            "index" => $value[2],
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $value[3],
                                ]
                            ],
                        ];
                        
                    } elseif ($value[1] == "url") {
                        
                        $message[3] = [
                            "type" => "button",
                            "sub_type" => "URL",
                            "index" => $value[2],
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $value[3],
                                ]
                            ],
                        ];
                        
                    } elseif ($value[2] === 'header_media') {

                        $message[] = [
                            "type"      => "header",
                            "parameters" => [
                                [
                                    "type"  => strtolower($value[1]),
                                    strtolower($value[1]) => [
                                        "link" => $value[3],
                                    ],
                                ]
                            ],
                        ];
                    } else {
                        
                        foreach($template_message[$template_message_key]['example']["$value[1]_text"] as $template_key => $template_value) {
                            
                            $message[$template_message_key]["type"] = strtolower($template_message[$template_message_key]["type"]);
                            $message[$template_message_key]["parameters"][] = [
                                "type" => "text",
                                "text" => $data["body_placeholder_$k"]
                            ];
                            $k++;
                        }
                    } 
                }
            }
            

            foreach ($contactNewArray as $index_key => $number) {
                    
                $contact   = filterContactNumber($number);
                $value     = array_key_exists($contact, $numberGroupName) ? $numberGroupName[$contact] : $contact;
                $content   = $template_message;
            
                $log                  = new WhatsappLog();
                $log->user_id         = $userId;
                $log->whatsapp_id     = $allAvailableWaGateway[array_key_first($allAvailableWaGateway)];
                $log->template_id     = $templateData->id;
                $log->to              = $contact;
                $log->mode            = (boolean) WhatsappLog::CLOUD_API;
                $log->initiated_time  = $setTimeInDelay;
                $log->message         = json_encode(array_values($message), JSON_UNESCAPED_SLASHES);
                $log->word_length     = $wordLength;
                $log->status          = WhatsappLog::PENDING;
                $log->file_info       = count($postData) > 0 ? $postData : null;
                $log->schedule_status = $request->input('schedule');
                $log->save();
                
                if (count($contactNewArray) > 1 && $log->status == WhatsappLog::PENDING) { 

                    ProcessWhatsapp::dispatch($log);
                    
                } else {

                    $cloud_api = WhatsappDevice::find($log->whatsapp_id);
                    $template  = WhatsappTemplate::find($log->template_id);
                    $default_crendetials = (object) config("setting.whatsapp_business_credentials.default");
                    $gateway_credentials = (object) $cloud_api->credentials;
                   
                    $url = "https://graph.facebook.com/$default_crendetials->version/$gateway_credentials->phone_number_id/messages";
                    
                    $headers = [
                        'Content-Type'  => 'application/json',
                        'Authorization' => "Bearer $gateway_credentials->user_access_token",
                        'Cookie'        => 'ps_l=0; ps_n=0',
                    ];
                    if($log->message == []) {
                        $data = [
                        
                            'messaging_product' => 'whatsapp',
                            'to'                => $log->to,
                            'type'              => 'template',
                            "template" => [
                                "name" => $template->name,
                                "language" => [
                                    "code" => $template->language_code
                                ],
                                "components" => $log->message
                            ]
                        ];
                    } else {
                        $data = [
                            'messaging_product' => 'whatsapp',
                            'to'                => $log->to,
                            'type'              => 'template',
                            "template" => [
                                "name" => $template->name,
                                "language" => [
                                    "code" => $template->language_code
                                ],
                                "components" => $log->message
                            ]
                        ];
                    }
                    
                    $response = Http::withHeaders($headers)->post($url, $data);
                    $responseBody = $response->body();
                    $responseData = json_decode($responseBody, true);
                    
                    if ($response->successful()) {
                        $log->message_response = $responseBody;
                        $log->status = WhatsappLog::PROCESSING;
                        $log->update();
                      
                        if($userId) {
                          
                            $user        = User::find($userId);
                            $messages    = str_split($log->message,$wordLength);
                            $totalCredit = count($messages);
                            $this->deductWhatsAppCredit($user, $totalCredit, 1);
                        }
                    } else {
        
                        $log->message_response = $response->body();
                        if(isset($responseData['error']['message'])) {
                            $this->addedCredit($log, $responseData['error']['message']);
                        }
                        $log->status       = WhatsappLog::FAILED;
                        $log->update();
                    }
                }
                if($userId) {
                    $user        = User::find($userId);
                    $messages    = str_split($log->message,$wordLength);
                    $totalCredit = count($messages);
                    $this->deductWhatsAppCredit($user, $totalCredit, 1);
                }
               
            }
            
        } else {
    
            $setWhatsAppGateway = $allAvailableWaGateway;
            $i = 1; $addSecond = 50;

            if($request->input("whatsapp_device_id") == "-1") {
        
                foreach ($contactNewArray as $index_key => $number) {

                    $contact = filterContactNumber($number);
                    $value   = array_key_exists($contact, $numberGroupName) ? $numberGroupName[$contact] : $contact;
                    $content = $this->smsService->getFinalContent($value,$numberGroupName,$request->input('message'));
                    $log     = new WhatsappLog();

                    foreach ($setWhatsAppGateway as $id => $credentials) {
                        
                        $rand      = rand($credentials['min_delay'] ,$credentials['max_delay']);
                        $addSecond = $i * $rand;
                        unset($setWhatsAppGateway[$id]);
                        
                        if(empty($setWhatsAppGateway)){
                            
                            $setWhatsAppGateway = $allAvailableWaGateway;
                            $i++;
                        }
                        
                        break;
                    }
                    $log->whatsapp_id     = $id;
                    $log->user_id         = $userId;
                    $log->to              = $contact;
                    $log->initiated_time  = $setTimeInDelay;
                    $log->mode            = (boolean) $request->input("whatsapp_sending_mode") ? WhatsappLog::NODE : WhatsappLog::CLOUD_API;
                    $log->message         = $content;
                    $log->word_length     = $wordLength;
                    $log->status          = $request->input('schedule');
                    $log->file_info       = count($postData) > 0 ? $postData : null;
                    $log->schedule_status = $request->input('schedule');
                    $log->save();
                    
                    if($log->status == WhatsappLog::PENDING) { 
    
                        ProcessWhatsapp::dispatch($log)->delay(Carbon::parse($setTimeInDelay)->addSeconds($addSecond));
                    }
                }
            } else {
    
                foreach ($contactNewArray as $index_key => $number) {

                    
                    $contact = filterContactNumber($number);
                    $value   = array_key_exists($contact, $numberGroupName) ? $numberGroupName[$contact] : $contact;
                    $content = $this->smsService->getFinalContent($value,$numberGroupName,$request->input('message'));
                    $rand      = rand($allAvailableWaGateway[array_key_first($allAvailableWaGateway)]["min_delay"], $allAvailableWaGateway[array_key_first($allAvailableWaGateway)]["max_delay"]);
                    $addSecond = $i * $rand;
                    
                    $log                  = new WhatsappLog();
                    $log->user_id         = $userId;
                    $log->whatsapp_id     = array_key_first($allAvailableWaGateway);
                    $log->to              = $contact;
                    $log->mode            = (boolean) $request->input("whatsapp_sending_mode") ? WhatsappLog::NODE : WhatsappLog::CLOUD_API;
                    $log->initiated_time  = $setTimeInDelay;
                    $log->message         = $content;
                    $log->word_length     = $wordLength;
                    $log->status          = $request->input('schedule');
                    $log->file_info       = count($postData) > 0 ? $postData : null;
                    $log->schedule_status = $request->input('schedule');
                    $log->save();
                    
                    if($log->status == WhatsappLog::PENDING && $request->input("whatsapp_sending_mode") == "true") { 
                        
                        ProcessWhatsapp::dispatch($log)->delay(Carbon::parse($setTimeInDelay)->addSeconds($addSecond));
                        $i++;
                    } else {
                        
                        ProcessWhatsapp::dispatch($log);
                    }
                }
            }
        }
    }


    /**
     * @param $search
     * @param $searchDate
     * @return Builder
     */
    public function searchWhatsappLog($search, $searchDate): Builder
    {
        $smsLogs = WhatsappLog::query();
        if (!empty($search)) {
            $smsLogs->whereHas('user',function ($q) use ($search) {
                $q->where('to', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if (!empty(request()->input('status'))) {
            $status = match (request()->input('status')){
                'pending' => [1],
                'schedule' => [2],
                'fail' => [3],
                'delivered' => [4],
                'processing' => [5],
                default => [1,2,3,4,5],
            };
            $smsLogs->whereIn('status',$status);
        }


        if (!empty($searchDate)) {

            $dateRange = explode('-', $searchDate);
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay() ?? null;
            $lastDate = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;

            if ($firstDate) {
                $smsLogs->whereDate('created_at', '>=', $firstDate);
            }

            if ($lastDate) {
                $smsLogs->whereDate('created_at', '<=', $lastDate);
            }

        }

        return $smsLogs;
    }


    /**
     * @param WhatsappLog $whatsappLog
     * @param $gwException
     * @return void
     */
    public function addedCredit(WhatsappLog $whatsappLog ,$gwException): void
    {
        $whatsappLog->status = WhatsappLog::FAILED;
        $whatsappLog->response_gateway = $gwException;
        $whatsappLog->save();
        $user = User::find($whatsappLog->user_id);

        if($whatsappLog->contact_id){
            $status = "Fail";
        }

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

    /**
     * @param User $user
     * @param int $totalCredit
     * @param int $totalNumber
     * @return void
     */
    public function deductWhatsAppCredit(User $user, int $totalCredit, int $totalNumber): void
    {
        $user->whatsapp_credit -=  $totalCredit;
        $user->save();

        $creditInfo = new WhatsappCreditLog();
        $creditInfo->user_id = $user->id;
        $creditInfo->type = "-";
        $creditInfo->credit = $totalCredit;
        $creditInfo->trx_number = trxNumber();
        $creditInfo->post_credit =  $user->whatsapp_credit;
        $creditInfo->details = $totalCredit." credits were cut for " .$totalNumber . " number send message";
        $creditInfo->save();
    }
}
