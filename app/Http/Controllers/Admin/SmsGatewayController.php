<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AndroidApi;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use App\Models\SmsGateway;
use App\Models\GeneralSetting;
use App\Models\Gateway;
use Illuminate\Support\Facades\Http;

class SmsGatewayController extends Controller
{

   

    public function smsApi()
    {
    	$title = "SMS API Gateway list";
    	$smsGateways = Gateway::whereNull('user_id')->whereNotNull('sms_gateways')->orderBy('is_default', 'DESC')->paginate(paginateNumber());
    	$credentials = SmsGateway::orderBy('id','asc')->get();
    	return view('admin.sms_gateway.sms_api', compact('title', 'smsGateways', 'credentials'));
    }


    public function android()
    {
    	$title = "Android Gateway list";
        $androids = AndroidApi::where('admin_id', auth()->guard('admin')->user()->id)->orderBy('id', 'DESC')->paginate(paginateNumber());
    	return view('admin.android.gateways', compact('title', 'androids'));
    }
   

    /**
    * Create Gateway
    * @param Request $request
    */
    public function create(Request $request) {
    
        $this->validate($request, [
        'type'               => "required",
        'driver_information' => ["required"],
        'name'               => ["required", "unique:gateways,name"],
        'status'             => "required|in:0,1",
        ]);

        $sms                     = new Gateway();
        $sms->status             = $request->input('status');
        $sms->type               = $request->input('type');
        $sms->name               = $request->input('name');
        $sms->sms_gateways       = $request->input('driver_information');
        $sms->mail_gateways      = null;
        $sms->save();

        $notify[] = ['success', 'A new '.ucfirst($sms->type). ' method has been created under: '.ucfirst($sms->name)];
        return back()->withNotify($notify);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'type'               => "required",
            'driver_information' => "required",
            'name'               => ["required", "unique:gateways,name,".request()->id],
            'status'             => "required|in:0,1",
        ]); 

        $sms                     = Gateway::findOrFail($request->id);
        $sms->type               = $request->input('type');
        $sms->name               = $request->input('name');
        $sms->address            = $request->input('address');
        $sms->sms_gateways       = $request->input('driver_information');
        $sms->status             = $request->input('status');
        $sms->save();
        $notify[] = ['success', ucfirst($sms->type). ' method under: '.ucfirst($sms->name). ' has been updated'];

        return back()->withNotify($notify);
    }


    public function defaultGateway(Request $request)
    {
    	$smsGateway = SmsGateway::findOrFail($request->input('sms_gateway'));
    	$setting = GeneralSetting::first();
    	$setting->sms_gateway_id = $smsGateway->id;
    	$setting->save();

    	$notify[] = ['success', 'Default SMS Gateway has been updated'];
        return back()->withNotify($notify);
    }

    /**
     * Updates the default gateway status 
     * @param Request $request
     * @return mixed
     */
    public function defaultStatus(Request $request) :mixed {

        $gateway = Gateway::findOrFail($request->id);

        if($gateway->status == 1) {

            $general             = GeneralSetting::first();
            $general->mail_from  = $gateway->address;
            $general->save();

            Gateway::whereNotNull('sms_gateways')->where('id', '!=',$request->id)->whereNull('user_id')->update(["is_default" => 0 ]);
            $gateway->is_default =  $request->default_value;
            $gateway->update();
            return json_encode([
                'reload' => true,
                'status' => true,
            ]);
        }
        else {

            return json_encode([
                'reload' => true,
                'status' => false,
            ]);
        }
    }
/**
    * Delete Gateway
    * @param Request $request
    */
    public function delete(Request $request) {
       
        $gateway  = Gateway::find($request->id);
        $gateway->delete();
        $notify[] = ['success', 'Gateway has been successfully deleted'];
        return back()->withNotify($notify);
    }
}
