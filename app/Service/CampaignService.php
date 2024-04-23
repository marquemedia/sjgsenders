<?php

namespace App\Service;

use App\Models\Campaign;
use App\Models\CampaignContact;
use App\Models\CampaignSchedule;
use App\Models\EmailGroup;
use App\Models\Group;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gateway;
use App\Models\GeneralSetting;

class CampaignService
{
    public function __construct(
        protected EmailService $emailService,
        protected SmsService $smsService,
        protected WhatsAppService $whatsAppService
    ){}


    public function getChannelFromRoute(): string
    {
        if (request()->routeIs('admin.campaign.sms') || request()->routeIs('user.campaign.sms')) {
            return Campaign::SMS;
        }

        if (request()->routeIs('admin.campaign.email') || request()->routeIs('user.campaign.email')) {
            return Campaign::EMAIL;
        }
        return Campaign::WHATSAPP;
    }

    public function generateTitle(string $channel): string
    {
        $channelTranslations = [
            Campaign::SMS      => __('SMS Campaign'),
            Campaign::EMAIL    => __('Email Campaign'),
            Campaign::WHATSAPP => __('WhatsApp Campaign'),
        ];

        return $channelTranslations[$channel] ?? '';
    }

    public function getGroupsForChannel(string $channel)
    {
        if (request()->routeIs('admin.campaign.create') || request()->routeIs('admin.campaign.edit')) {

            return Group::whereNull('user_id')->get();
        }
        elseif(request()->routeIs('user.campaign.create') || request()->routeIs('user.campaign.edit')) {

            return Group::where('user_id',auth()->user()->id)->get();
        }
        
    }

    public function getTemplatesForChannel(string $channel)
    {
        if (request()->routeIs('admin.campaign.create') || request()->routeIs('admin.campaign.edit')) {

            return ($channel !== Campaign::EMAIL) ? Template::whereNull('user_id')->get() : [];
        }
        elseif(request()->routeIs('user.campaign.create') || request()->routeIs('user.campaign.edit')) {

            return ($channel !== Campaign::EMAIL) ? Template::where('user_id',auth()->user()->id)->get() : [];
        }
    }

    public function save(Request $request, $method = null, mixed $templateData = null): Campaign
    {
        $general     = GeneralSetting::first();
        $emailMethod = $request->input('gateway_id') ? Gateway::whereNotNull('mail_gateways')->where('id',$request->gateway_id)->first() : Gateway::whereNotNull('mail_gateways')->where('is_default', 1)->first();
        $message     = textSpinner(offensiveMsgBlock($request->input('message')));
        if ($request->has('id')) {

            try {

                $current_campaign = Campaign::find($request->id);
                if ($request->input('remove_media') == true) {

                    $postData = $this->whatsAppService->findAndUploadFile($request);
                } else {

                    $postData = $current_campaign->post_data ? $current_campaign->post_data : $this->whatsAppService->findAndUploadFile($request);
                }
                
            } catch( \Exception $e) {

                $notify[] = ['error', translate("Sorry Something went wrong")];
                return back()->withNotify($notify);
            }
        } else {

            $postData = $this->whatsAppService->findAndUploadFile($request);
        }

        if ($general->sms_gateway == 1) {

            $smsMethod = $request->input('gateway_id') ? Gateway::whereNotNull('sms_gateways')->where('id',$request->gateway_id)->first() : Gateway::whereNotNull('sms_gateways')->where('is_default', 1)->first();
        } 
        
        if($request->input("channel") == "whatsapp") {
        
            if ($request->input("cloud_api") == "true") { 
              
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
            }
        }

        $campaignData = [
            'name'           => $request->input('name'),
            'channel'        => $request->input('channel'),
            'body'           => $request->input("channel") == "whatsapp" ? json_encode(array_values($message), JSON_UNESCAPED_SLASHES) : $message,
            'subject'        => $request->input('subject'),
            'sms_type'       => $request->input('smsType'),
            'from_name'      => $request->input('from_name'),
            'post_data'      => count($postData) > 0 ? $postData : null,
            'reply_to_email' => $request->input('reply_to_email'),
            'status'         => 'Active',
            'schedule_time'  => $request->input('schedule_date'),
        ];
       
        if (request()->routeIs('user.campaign.*')) {
            
            $campaignData['user_id'] = auth()->user()->id;
            
            if ($campaignData['channel'] == Campaign::EMAIL) {
                
                $campaignData['json_body'] = ["method" => "email"];
                $campaignData['sender_id'] = $method->id;

            } elseif ($campaignData['channel'] == Campaign::SMS) {

                if (auth()->user()) {

                    $campaignData['sender_id'] = null;

                    if (auth()->user()->sms_gateway == 1) {

                        $campaignData['json_body'] = ["method" => "api"];
                        $campaignData['sender_id'] = (int)$smsMethod->id;
                    } else {

                        $campaignData['json_body'] = ["method" => "android"];
                        $campaignData['sender_id'] = $request->has("sim_id") ? (int) $request->input("sim_id") : (int) $request->input("android_gateways_id");
                    }
                }
            } elseif ($campaignData['channel'] == Campaign::WHATSAPP) {

                if ($request->input("whatsapp_sending_mode") == "without_cloud_api") {

                    $campaignData['json_body'] = ["method" => "whatsapp"];
                    $campaignData['sender_id'] = $request->input("whatsapp_device_id");
                } else {

                    $campaignData['json_body'] = [
                        "method" => "whatsapp_cloud",
                        "template_id" => $request->input("whatsapp_template_id")
                    ];
                    $campaignData['sender_id'] = $request->input("whatsapp_device_id");
                }
            }
        }

        else {
            if ($campaignData['channel'] == Campaign::EMAIL) {
                
                $campaignData['json_body'] = ["method" => "email"];
                $campaignData['sender_id'] = $emailMethod->id;
            } elseif ($campaignData['channel'] == Campaign::SMS) {
    
                $campaignData['sender_id'] = null;

                if ($general->sms_gateway == 1) {

                    $campaignData['json_body'] = ["method" => "api"];
                    $campaignData['sender_id'] = (int)$smsMethod->id;
                } else {

                    $campaignData['json_body'] = ["method" => "android"];
                    $campaignData['sender_id'] = $request->has("sim_id") ? (int) $request->input("sim_id") : (int) $request->input("android_gateways_id");
                }
            } elseif ($campaignData['channel'] == Campaign::WHATSAPP) {

                if ($request->input("whatsapp_sending_mode") == "without_cloud_api") {

                    $campaignData['json_body'] = ["method" => "whatsapp"];
                    $campaignData['sender_id'] = $request->input("whatsapp_device_id");
                } else {
                    
                    $campaignData['json_body'] = [
                        
                        "method" => "whatsapp_cloud",
                        "template_id" => $request->input("whatsapp_template_id")
                    ];
                    $campaignData['sender_id'] = $request->input("whatsapp_device_id");
                }
            }

        }
        
        return Campaign::updateOrCreate(['id' => $request->input('id')], $campaignData);
    }


    public function saveSchedule(Request $request, int $campaignId): void
    {
        CampaignSchedule::create([
            'campaign_id'   => $campaignId,
            'repeat_number' => $request->input('repeat_number'),
            'repeat_format' => $request->input('repeat_format'),
        ]);
    }

    public function saveContacts(array $attachableData, Campaign $campaign): void
    {
        $contactNewArray = array_unique($attachableData['contacts']);
        $groupWithId     = $attachableData['contact_with_id'] ?? [];
        $data            = collect($contactNewArray)->map(function ($value) use ($campaign, $groupWithId) {
            $content     = $campaign->body;
            $replacement = $groupWithId[$value] ?? $value;
            $content     = str_replace('{{name}}', $replacement, $content);
            return [
                'campaign_id' => $campaign->id,
                'contact'     => $value,
                'message'     => $content,
            ];
        })->toArray();
        
        CampaignContact::insert($data);
    }

    public function processContacts(Request $request): array
    {

       
        $groupWithId = []; $contacts = [];
        
        if($request->input('channel') == Campaign::EMAIL){

            if($request->has('group_id')){

                $request->merge([
                    'email_group_id' => $request->input('group_id')
                ]);
            }
            if(request()->routeIs('user.campaign.store') || request()->routeIs('user.campaign.update') ) {
                $this->emailService->processEmail($request,$contacts, auth()->user()->id);
                $this->smsService->processGroupId($request, $contacts, $groupWithId, auth()->user()->id);
                $this->smsService->processFile($request, $contacts, $groupWithId, auth()->user()->id);
            }
            elseif(request()->routeIs('admin.campaign.store') || request()->routeIs('admin.campaign.update')) {

                $this->emailService->processEmail($request,$contacts);
                $this->smsService->processGroupId($request, $contacts, $groupWithId);
                $this->smsService->processFile($request, $contacts, $groupWithId);
            }
        }
        
        if(in_array($request->input('channel'), [Campaign::SMS, Campaign::WHATSAPP])) {

            if(request()->routeIs('user.campaign.store') || request()->routeIs('user.campaign.update') ) {

                $this->smsService->processNumber($request, $contacts, auth()->user()->id);
                $this->smsService->processGroupId($request, $contacts, $groupWithId, auth()->user()->id);
                $this->smsService->processFile($request, $contacts, $groupWithId, auth()->user()->id);
            }
            elseif(request()->routeIs('admin.campaign.store') || request()->routeIs('admin.campaign.update')) {

                $this->smsService->processNumber($request, $contacts);
                $this->smsService->processGroupId($request, $contacts, $groupWithId);
                $this->smsService->processFile($request, $contacts, $groupWithId);
            }
        }
        $contacts = $this->flattenAndUnique($contacts);
        
        return ([
            "contacts"         => $contacts,
            "contact_with_id"  => $groupWithId,
        ]);
    }

    public function flattenAndUnique(array $allEmail): array
    {
        
        $newArray = [];
        foreach ($allEmail as $childArray) {
            foreach ($childArray as $value) {
                $newArray[] = $value;
            }
        }
        return array_unique($newArray);
    }

}
