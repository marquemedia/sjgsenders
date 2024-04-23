<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\Gateway;
use Illuminate\View\View;
use App\Models\EmailGroup;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Http\Requests\PricingPlanRequest;

class PricingPlanController extends Controller
{
    public function index()
    {
        
        $title = "Manage pricing plan";
        $gateways = array_keys(config('setting.gateway_credentials.email'));
        $plans = PricingPlan::orderBy('id', 'ASC')->paginate(paginateNumber());
        return view('admin.plan.index', compact('title', 'plans', 'gateways'));
    }

     /**
     * @return View
     */
    public function create(): View
    {
        $title = "Add A New Subscription Plan";
        $mail_credentials = array_keys(config('setting.gateway_credentials.email'));
        $sms_credentials = array_keys(config('setting.gateway_credentials.sms'));
        unset($sms_credentials[0]);
        $sms_credentials = array_values($sms_credentials);

        return view('admin.plan.create', compact('title', 'sms_credentials', 'mail_credentials'));
    }
    
    public function store(Request $request)
    { 
        
        $validations = [
            'name'                 => 'required|max:255',
            'description'          => 'nullable',
            'amount'               => 'required|numeric|min:0',
            'allow_carry_forward'  => 'nullable',
            'duration'             => 'required|integer',
            'status'               => 'required|in:1,2',
            'recommended_status'   => 'nullable|in:1,2'
        ];

        if($request->input('allow_admin_creds')) {
            $additionalValidations = [
                'whatsapp_device_limit'        => ['requiredIf:allow_whatsapp,true', 'gte:0'],
            ];
           
        } else {
            
            $additionalValidations = [
                'user_android_gateway_limit' => ['requiredIf:allow_user_android,true', 'gte:0'],
                'user_whatsapp_device_limit' => ['requiredIf:allow_user_whatsapp,true', 'gte:0'],
                'mail_gateways'              => ['requiredIf:mail_multi_gateway,true'],
                'total_mail_gateway'         => ['requiredIf:mail_multi_gateway,true|array'],
                'total_mail_gateway.*'       => ['numeric','gte:1'],
                'sms_gateways'               => ['requiredIf:sms_multi_gateway,true'],
                'total_sms_gateway'          => ['requiredIf:sms_multi_gateway,true|array'],
                'total_sms_gateway.*'        => ['gte:1','numeric']
            ];
            
        }
        $validations = array_merge($validations, $additionalValidations);
        $data = $this->validate($request, $validations);
        $planMapping = config("planaccess.pricing_plan");
        
        foreach( $planMapping as $plan_key => $plan_value) {
            if($request->input('allow_admin_creds')) {
                $data["type"] = PricingPlan::ADMIN;
                switch($plan_key) {
                    case ("sms") :
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_admin_".$plan_key) ?? false;
                        unset($plan_value["gateway_limit"]);
                        if(array_key_exists("android", $plan_value)) {
                            $plan_value["android"]["is_allowed"] =  (boolean)$request->input("allow_admin_android") ?? false;
                            unset($plan_value["android"]["gateway_limit"]);
                        }
                        break;
                    case ("email") :
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_admin_".$plan_key) ?? false;
                        unset($plan_value["gateway_limit"]);
                        break;
                    case ("whatsapp") :
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_admin_".$plan_key) ?? false;
                        
                        $plan_value["gateway_limit"] = (int)$request->input("whatsapp_device_limit");
                        break;
                }
                $plan_value["credits"] = (int)$request->input($plan_value["credits"]."_ga");
                unset($plan_value["allowed_gateways"]);
                $planMapping[$plan_key] = $plan_value;
               
            } else {
                $data["type"] = PricingPlan::USER;
                if($plan_key == "sms") {
                    if($request->input("sms_multi_gateway")) {
                        $plan_value["is_allowed"] = (boolean)$request->input("sms_multi_gateway");
                        for($i = 0; $i < count($data['sms_gateways']); $i++) {
              
                            $multi['sms'][$data['sms_gateways'][$i]] = (int)$data['total_sms_gateway'][$i];
                        }
                        unset($data['sms_gateways']);
                        unset($data['total_sms_gateway']);
                        $plan_value["gateway_limit"] = array_sum(array_values($multi["sms"]));
                        $plan_value["allowed_gateways"] = $multi["sms"];
                    } else {
                        $plan_value["is_allowed"] = false;
                        unset($plan_value["gateway_limit"]);
                        unset($plan_value["allowed_gateways"]);
                    }
                    if($request->input("allow_user_android")) {
                        $plan_value["android"]["is_allowed"] = (boolean)$request->input("allow_user_android");
                        $plan_value["android"]["gateway_limit"] = (int)$request->input("user_android_gateway_limit");
                        unset($data['user_android_gateway_limit']);
                        
                    } else {
                        $plan_value["android"]["is_allowed"] = false;
                        unset($plan_value["android"]["gateway_limit"]);
                    }
                }
                if($plan_key == "email") {
                    if($request->input("mail_multi_gateway")) {
                        $plan_value["is_allowed"] = (boolean)$request->input("mail_multi_gateway");
                        for($i = 0; $i < count($data['mail_gateways']); $i++) {
              
                            $multi['mail'][$data['mail_gateways'][$i]] = (int)$data['total_mail_gateway'][$i];
                        }
                        unset($data['mail_gateways']);
                        unset($data['total_mail_gateway']);
                        $plan_value["gateway_limit"] = array_sum(array_values($multi["mail"]));
                        $plan_value["allowed_gateways"] = $multi["mail"];
                    } else {
                        $plan_value["is_allowed"] = false;
                        unset($plan_value["gateway_limit"]);
                        unset($plan_value["allowed_gateways"]);
                    }
                    
                   
                }
                if($plan_key == "whatsapp") {
                    
                    if($request->input("allow_user_whatsapp")) {
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_user_whatsapp");
                        $plan_value["gateway_limit"] = (int)$request->input("user_whatsapp_device_limit");
                        unset($data['user_whatsapp_device_limit']);
                    } else {
                       
                        $plan_value["is_allowed"] = false;
                        unset($plan_value["gateway_limit"]);
                        
                    }
                }
                $plan_value["credits"] = (int)$request->input($plan_value["credits"]."_gu");
                $planMapping[$plan_key] = $plan_value;
            }
        }

        $data["carry_forward"] = $request->input("allow_carry_forward") ? PricingPlan::ENABLED : PricingPlan::DISABLED;
        $data = array_merge($data, $planMapping);
        PricingPlan::create($data);
        $notify[] = ['success', 'Pricing plan has been created'];
        return back()->withNotify($notify);
        
    }

    private function allowedGateway($request, $mapping) :array {
        $allowedGateway = [];
        foreach ($mapping as $inputKey => $outputKey) {
           
            $value = $request[$inputKey] ?? false;
            $this->setNestedArrayValue($allowedGateway, $outputKey, $value);
        }
        return $allowedGateway;
    }
    private function setNestedArrayValue(&$array, $key, $value)
    {
        $keys = explode('.', $key);

        foreach ($keys as $nestedKey) {
            if (!isset($array[$nestedKey])) {
                $array[$nestedKey] = [];
            }

            $array = &$array[$nestedKey];
        }

        if ($value === 'true') {
            $array = true;
        } elseif ($value === 'false') {
            $array = false;
        } else {
            $array = $value;
        }
    }
    public function edit($id){

        $mail_credentials = array_keys(config('setting.gateway_credentials.email'));
        $sms_credentials  = array_keys(config('setting.gateway_credentials.sms'));
        unset($sms_credentials[0]);
        $sms_credentials  = array_values($sms_credentials);
        $plan             = PricingPlan::findOrFail($id);
        $title            = "Update $plan->name Subscription Plan";
        $mail_gateways    = $plan->type == PricingPlan::USER && @$plan->email->allowed_gateways ? $plan->email->allowed_gateways : null;
        $sms_gateways     = $plan->type == PricingPlan::USER && @$plan->sms->allowed_gateways ? $plan->sms->allowed_gateways : null;
       
        return view('admin.plan.edit', compact('title', 'sms_credentials', 'mail_credentials', 'plan', 'sms_gateways', 'mail_gateways'));
    }

    public function update(Request $request)
    {
        $validations = [
            'name'                 => 'required|max:255',
            'description'          => 'nullable',
            'amount'               => 'required|numeric|min:0',
            'allow_carry_forward'  => 'nullable',
            'duration'             => 'required|integer',
            'status'               => 'required|in:1,2',
            'status'               => 'required|in:1,2',
            'recommended_status'   => 'nullable|in:1,2'
        ];

        if($request->input('allow_admin_creds')) {
            $additionalValidations = [
                'whatsapp_device_limit'        => ['requiredIf:allow_whatsapp,true', 'gte:0'],
            ];
           
        } else {
            
            $additionalValidations = [
                'user_android_gateway_limit' => ['requiredIf:allow_user_android,true', 'gte:0'],
                'user_whatsapp_device_limit' => ['requiredIf:allow_user_whatsapp,true', 'gte:0'],
                'mail_gateways'              => ['requiredIf:mail_multi_gateway,true'],
                'total_mail_gateway'         => ['requiredIf:mail_multi_gateway,true|array'],
                'total_mail_gateway.*'       => ['numeric','gte:1'],
                'sms_gateways'               => ['requiredIf:sms_multi_gateway,true'],
                'total_sms_gateway'          => ['requiredIf:sms_multi_gateway,true|array'],
                'total_sms_gateway.*'        => ['gte:1','numeric']
            ];
        }
        $validations = array_merge($validations, $additionalValidations);
        $data = $this->validate($request, $validations);
        $planMapping = config("planaccess.pricing_plan");
        
        foreach( $planMapping as $plan_key => $plan_value) {
            if($request->input('allow_admin_creds')) {
                $data["type"] = PricingPlan::ADMIN;
                switch($plan_key) {
                    case ("sms") :
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_admin_".$plan_key) ?? false;
                        unset($plan_value["gateway_limit"]);
                        if(array_key_exists("android", $plan_value)) {
                            $plan_value["android"]["is_allowed"] =  (boolean)$request->input("allow_admin_android") ?? false;
                            unset($plan_value["android"]["gateway_limit"]);
                        }
                        break;
                    case ("email") :
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_admin_".$plan_key) ?? false;
                        unset($plan_value["gateway_limit"]);
                        break;
                    case ("whatsapp") :
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_admin_".$plan_key) ?? false;
                        
                        $plan_value["gateway_limit"] = (int)$request->input("whatsapp_device_limit");
                        break;
                }
                $plan_value["credits"] = (int)$request->input($plan_value["credits"]."_ga");
                unset($plan_value["allowed_gateways"]);
                $planMapping[$plan_key] = $plan_value;
               
            } else {
                $data["type"] = PricingPlan::USER;
              
                if($plan_key == "sms") {
                    if($request->input("sms_multi_gateway")) {
                        $plan_value["is_allowed"] = (boolean)$request->input("sms_multi_gateway");
                        for($i = 0; $i < count($data['sms_gateways']); $i++) {
              
                            $multi['sms'][$data['sms_gateways'][$i]] = (int)$data['total_sms_gateway'][$i];
                        }
                        unset($data['sms_gateways']);
                        unset($data['total_sms_gateway']);
                        $plan_value["gateway_limit"] = array_sum(array_values($multi["sms"]));
                        $plan_value["allowed_gateways"] = $multi["sms"];
                    } else {
                        $plan_value["is_allowed"] = false;
                        unset($plan_value["gateway_limit"]);
                        unset($plan_value["allowed_gateways"]);
                    }
                    if($request->input("allow_user_android")) {
                        $plan_value["android"]["is_allowed"] = (boolean)$request->input("allow_user_android");
                        $plan_value["android"]["gateway_limit"] = (int)$request->input("user_android_gateway_limit");
                        unset($data['user_android_gateway_limit']);
                        
                    } else {
                        $plan_value["android"]["is_allowed"] = false;
                        unset($plan_value["android"]["gateway_limit"]);
                    }
                }
                if($plan_key == "email") {
                    if($request->input("mail_multi_gateway")) {
                        $plan_value["is_allowed"] = (boolean)$request->input("mail_multi_gateway");
                        for($i = 0; $i < count($data['mail_gateways']); $i++) {
              
                            $multi['mail'][$data['mail_gateways'][$i]] = (int)$data['total_mail_gateway'][$i];
                        }
                        unset($data['mail_gateways']);
                        unset($data['total_mail_gateway']);
                        $plan_value["gateway_limit"] = array_sum(array_values($multi["mail"]));
                        $plan_value["allowed_gateways"] = $multi["mail"];
                    } else {
                        $plan_value["is_allowed"] = false;
                        unset($plan_value["gateway_limit"]);
                        unset($plan_value["allowed_gateways"]);
                    }
                    
                   
                }
                if($plan_key == "whatsapp") {
                    
                    if($request->input("allow_user_whatsapp")) {
                        $plan_value["is_allowed"] = (boolean)$request->input("allow_user_whatsapp");
                        $plan_value["gateway_limit"] = (int)$request->input("user_whatsapp_device_limit");
                        unset($data['user_whatsapp_device_limit']);
                    } else {
                        
                        $plan_value["is_allowed"] = false;
                        unset($plan_value["gateway_limit"]);
                        
                    }
                  
                  
                }
                $plan_value["credits"] = (int)$request->input($plan_value["credits"]."_gu");
                $planMapping[$plan_key] = $plan_value;
            }
        }

        $data["carry_forward"] = $request->input("allow_carry_forward") ? PricingPlan::ENABLED : PricingPlan::DISABLED;
        $data = array_merge($data, $planMapping);
       
        $plan = PricingPlan::findOrFail($request->id);

        
        $plan->update($data);
        $notify[] = ['success', 'Pricing plan has been updated'];
        return back()->withNotify($notify);
    }
    public function status(Request $request)
    {

        PricingPlan::where('id', '!=',$request->id)->update([
            "recommended_status"=>2
        ]);
        $plan = PricingPlan::findOrFail($request->id);

        $plan->recommended_status =  $request->status;
        $plan->save();
       
        return json_encode([
            'reload' => true,
            'status' => true,
        ]);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        Subscription::where('plan_id',$request->id)->delete();
        PricingPlan::where('id',$request->id)->delete();
        $notify[] = ['success', 'Pricing plan has been deleted'];
        return back()->withNotify($notify);
    }
}
