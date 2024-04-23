<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampaignRequest;
use App\Models\AndroidApi;
use App\Models\Campaign;
use App\Models\CampaignContact;
use App\Models\CampaignSchedule;
use App\Service\CampaignService;
use Illuminate\Http\Request;
use App\Models\Gateway;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\WhatsappDevice;
use App\Models\WhatsappTemplate;

class CampaignController extends Controller
{
    public function __construct (
        protected CampaignService $campaignService,
    ){}

    public function index() {

        $channel   = $this->campaignService->getChannelFromRoute();
        
        $campaigns = Campaign::with('contacts')
            ->latest()
            ->whereNull('user_id')
            ->where('channel', $channel)
            ->paginate(paginateNumber());

        return view('admin.campaign.index', [
            
            
            'campaigns' => $campaigns,
            'title'     => $this->campaignService->generateTitle($channel),
            'channel'   => $channel,
        ]);
    }

    public function create(string $channel) {

        $credentials      = [];
        $title            = ucfirst($channel) . __(' Campaign Create');
        $groups           = $this->campaignService->getGroupsForChannel($channel);
        $templates        = $this->campaignService->getTemplatesForChannel($channel);
        $android_gateways = AndroidApi::whereNull("user_id")->where("status", AndroidApi::ACTIVE)->latest()->get();

        if ($channel == Campaign::EMAIL) {

            $credentials = config('setting.gateway_credentials.email');
            return view('admin.campaign.create', compact('title', 'channel', 'groups', 'templates', 'credentials'));
        }
        elseif ($channel == Campaign::SMS) {

            $credentials = SmsGateway::orderBy('id','asc')->get();
            return view('admin.campaign.create', compact('title', 'channel', 'groups', 'templates', 'credentials', 'android_gateways'));
        }
        elseif($channel == Campaign::WHATSAPP) {

            $whatsapp_node_devices  = WhatsappDevice::whereNull("user_id")->where('status', WhatsappDevice::CONNECTED)->where("type", WhatsappDevice::NODE)->latest()->get();
            $whatsapp_bussiness_api = WhatsappDevice::whereNull("user_id")->where("type", WhatsappDevice::BUSINESS)->latest()->get();
            return view('admin.campaign.create', compact('title', 'channel', 'groups', 'templates', 'credentials', 'whatsapp_bussiness_api', 'whatsapp_node_devices'));
        }
        
    }

    public function store(CampaignRequest $request) {
        
        $contactsData = $this->campaignService->processContacts($request);
        
        $general = GeneralSetting::first();
        
        if($request->input('channel') == Campaign::SMS) {
           
            $defaultGateway = $general->sms_gateway == 1 ? Gateway::whereNotNull('sms_gateways')->whereNull('user_id')->where('is_default', 1)->first() : null;
        }
        elseif($request->input('channel') == Campaign::EMAIL) {
            
            $defaultGateway = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('is_default', 1)->first();

        } elseif ($request->input("channel") == Campaign::WHATSAPP) {
            
            if($request->input("whatsapp_sending_mode") == "without_cloud_api") {

                $defaultGateway = $request->input("whatsapp_device_id") == "-1" ? WhatsappDevice::where("type", WhatsappDevice::NODE)->where('admin_id', auth()->guard('admin')->user()->id)->where('status', 'connected')->pluck("credentials", "id")->toArray()
                               : WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)->where("id", $request->input("whatsapp_device_id"))->where('status', 'connected')->pluck("credentials", "id")->toArray();
            } else {
                $defaultGateway = $request->input("whatsapp_device_id") == "-1" ? WhatsappDevice::where("type", WhatsappDevice::BUSINESS)->where('admin_id', auth()->guard('admin')->user()->id)->where('status', 'connected')->pluck("credentials", "id")->toArray()
                               : WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)->where("id", $request->input("whatsapp_device_id"))->where('status', 'connected')->pluck("credentials", "id")->toArray();
            }
            
        }
        if (empty($contactsData['contacts'])) {

            $notify[] = ['error', translate("A campaign cannot be created without contacts.")];
            return back()->withNotify($notify);
        }
        if($request->input('gateway_type')) {

            $gatewayMethod = Gateway::where('id', $request->input('gateway_id'))->firstOrFail();
        }
        else{
            
            if($general->sms_gateway == 2 || $request->input("channel") == Campaign::WHATSAPP) {

                $gatewayMethod = null;
            }
            else{

                if($defaultGateway) {
                
                    $gatewayMethod = $defaultGateway;
                }
                else {
                    $notify[] = ['error', 'You Do Not Have Any Default Gateway.'];
                    return back()->withNotify($notify);
                }
            }
            
        }
        $templateData = null;

        if($request->input("channel") == "whatsapp" && $request->input("cloud_api") == "true") {

            $templateData = WhatsappTemplate::find($request->input("whatsapp_template_id"));
        }
        $campaign = $this->campaignService->save($request, $gatewayMethod, $templateData);
        
        if ($request->input('repeat_number')) {

            $this->campaignService->saveSchedule($request, $campaign->id);
        }
        $this->campaignService->saveContacts($contactsData, $campaign);
        $notify[]     = ['success', translate('The campaign has been successfully created.')];
        return back()->withNotify($notify);
    }

    public function edit(string $channel, int $id) {

        $title       = ucfirst($channel) . __(' Campaign Update');
        $campaign    = Campaign::with("schedule")->with('contacts')->where('id', $id)->first();
        $groups      = $this->campaignService->getGroupsForChannel($campaign->channel);
        $android_gateways = AndroidApi::whereNull("user_id")->where("status", AndroidApi::ACTIVE)->latest()->get();
        $templates = $this->campaignService->getTemplatesForChannel($campaign->channel);
        $credentials = [];
       
        if($channel == Campaign::EMAIL) {

            $credentials = config('setting.gateway_credentials.email');
            return view('admin.campaign.edit', compact('campaign', 'groups', 'templates', 'title', 'channel', 'credentials'));
        }
        
        elseif($channel == Campaign::SMS) {

            $credentials = SmsGateway::orderBy('id','asc')->get();
            return view('admin.campaign.edit', compact('campaign', 'groups', 'templates', 'title', 'channel', 'credentials', 'android_gateways'));

        }elseif($channel == Campaign::WHATSAPP) {

            $whatsapp_node_devices  = WhatsappDevice::whereNull("user_id")->where('status', WhatsappDevice::CONNECTED)->where("type", WhatsappDevice::NODE)->latest()->get();
            $whatsapp_bussiness_api = WhatsappDevice::whereNull("user_id")->where("type", WhatsappDevice::BUSINESS)->latest()->get();
            return view('admin.campaign.edit', compact('campaign', 'title', 'channel', 'groups', 'templates', 'credentials', 'whatsapp_bussiness_api', 'whatsapp_node_devices'));
        }

        
        
    }

    public function search(Request $request) {

        $request->validate([
            'channel' => 'required',
        ]);

        $channel       = $request->get('channel');
        $search        = $request->get('search');
        $searchStatus  = $request->get('status');
        $campaignQuery = Campaign::where('channel', $channel)->whereNull('user_id');
        if ($search) {

            $campaignQuery->where('name', 'like', '%' . $search . '%');
        }

        if ($searchStatus) {

            $campaignQuery->where('status', $searchStatus);
        }
        $campaigns     = $campaignQuery->paginate(paginateNumber());
        return view('admin.campaign.index', compact('campaigns', 'channel', 'search', 'searchStatus'))->with('title', $channel . __(' Campaign Search'));
    }

    public function contacts(int $id) {

        $title    = 'Campaign Contact List';
        $campaign = Campaign::with('contacts')->where('id',$id)->first();
        $contacts = CampaignContact::where('campaign_id',$id)->paginate(paginateNumber());
        return view('admin.campaign.show', compact('title', 'campaign', 'contacts'));
    }

    public function update(CampaignRequest $request) {

        $general      = GeneralSetting::first();
        $contactsData = $this->campaignService->processContacts($request);

        if($request->input('channel') == Campaign::WHATSAPP) {

            if($request->input("whatsapp_sending_mode") == "without_cloud_api") {

                $defaultGateway = $request->input("whatsapp_device_id") == "-1" ? WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)->where('status', 'connected')->pluck("credentials", "id")->toArray()
                               : WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)->where("id", $request->input("whatsapp_device_id"))->where('status', 'connected')->pluck("credentials", "id")->toArray();
            } else {
                
            }
        }
        if($request->input('channel') == Campaign::SMS) {

            $defaultGateway = $general->sms_gateway == 1 ? Gateway::whereNotNull('sms_gateways')->whereNull('user_id')->where('is_default', 1)->first() : null;
        }
        elseif($request->input('channel') == Campaign::EMAIL) {

            $defaultGateway = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('is_default', 1)->first();
        }
        if (empty($contactsData['contacts'])) {

            $notify[] = ['error', translate("A campaign cannot be created without contacts.")];
            return back()->withNotify($notify);
        }

        if($request->input('gateway_type')) {

            $gatewayMethod = Gateway::where('id', $request->input('gateway_id'))->firstOrFail();
        }
        else{
            if($general->sms_gateway == 2) {

                $gatewayMethod = null;
            }
            else{
                if($defaultGateway) {
                
                    $gatewayMethod = $defaultGateway;
                }
                else {
                    $notify[] = ['error', 'You Do Not Have Any Default Gateway.'];
                    return back()->withNotify($notify);
                }
            }
        }
        $campaign     = $this->campaignService->save($request, $gatewayMethod);
        if($request->input('repeat_number')){
            CampaignSchedule::where('campaign_id',$campaign->id)->delete();
            $this->campaignService->saveSchedule($request, $campaign->id);
        }
        CampaignContact::where('campaign_id',$campaign->id)->delete();
        $this->campaignService->saveContacts($contactsData, $campaign);
        $notify[]     = ['success', translate('Campaign Updated Successfully')];
        return back()->withNotify($notify);
    }


    public function delete(Request $request) {

        $campaign = Campaign::with('contacts')->where('id',$request->input('id'))->first();
        if($campaign){
            CampaignContact::where('campaign_id',$campaign->id)->delete();
            CampaignSchedule::where('campaign_id',$campaign->id)->delete();
            $campaign->delete();
        }
        $notify[] = ['success', translate('Campaign Deleted')];
        return back()->withNotify($notify);
    }


    public function deleteContact(Request $request) {

        $campaignContact = CampaignContact::findOrFail($request->input('id'));
        $campaignContact->delete();
        $notify[]        = ['success', translate('Contact Deleted From Campaigns')];
        return back()->withNotify($notify);
    }
}