<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappDevice;
use App\Models\WhatsappTemplate;
use App\Rules\WhatsappDeviceRule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\View\View;
use App\Rules\DifferenceRule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

use Dotenv\Dotenv;
use Exception;

class WhatsappDeviceController extends Controller {

    public function whatsAppDevices() {
        
    	$title = "WhatsApp Setup";
        $credentials = config('setting.whatsapp_business_credentials');

        $whatsappBusinesses = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->where("type", WhatsappDevice::BUSINESS)
            ->orderBy('id', 'desc')
            ->paginate(paginateNumber());

        $whatsappNodes = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->where("type", WhatsappDevice::NODE)
            ->orderBy('id', 'desc')
            ->paginate(paginateNumber());
        
        $checkWhatsAppServer = $this->checkWhatsappServerStatus();
        
    	return view('admin.whatsapp.device', compact('title', 'whatsappBusinesses', 'whatsappNodes', 'checkWhatsAppServer', 'credentials'));
    }

    /**
     * whatsapp store method
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request) {
        
        $validate = [];
        $notify   = [];
        $credentials = [];
        $validate['name'] = 'required|unique:wa_device,name';

        if($request->has("whatsapp_business_api") && $request->input("whatsapp_business_api") == true) {

            $required_credentials = config("setting.whatsapp_business_credentials.required");

            foreach($required_credentials as $required_cred_key => $required_cred_value) {

                $validate['credentials'][$required_cred_key] = 'required';
            }
        }

        if($request->has("whatsapp_node_module") && $request->input("whatsapp_node_module") == true) { 
            
            $validate['min_delay'] = 'required|gte:10';
            $validate['max_delay'] = ['required', 'gt:min_delay', new DifferenceRule($request->min_delay)];
        }

        $request->validate($validate);
        
        if($request->input("whatsapp_business_api") == true) {

            $whatsapp              = new WhatsappDevice();
            $whatsapp->admin_id    = auth()->guard('admin')->user()->id;
            $whatsapp->name        = $request->input('name');
            $whatsapp->type        = WhatsappDevice::BUSINESS;
            $whatsapp->credentials = $request->credentials;
            $whatsapp->save();
            $notify[] = ['success', 'Whatsapp business API credentials added successfully'];
        } 
        
        if($request->input("whatsapp_node_module") == true) {

            $currentUrl = $request->root();
            $parsedUrl = parse_url($currentUrl);
            $domain = $parsedUrl['host'];

            $response = Http::post(env('WP_SERVER_URL').'/sessions/init', [ 
                'domain' => $domain
            ]);

            $responseBody = json_decode($response->body());
    
            if ($response->status() === 200 && $responseBody->success) {

                $credentials = [];
                $credentials['min_delay']   = $request->input('min_delay');
                $credentials['max_delay']   = $request->input('max_delay');
                $credentials['number']      = " ";
                $credentials['multidevice'] = "YES";
                $whatsapp = new WhatsappDevice();
                $whatsapp->admin_id = auth()->guard('admin')->user()->id;
                $whatsapp->name = randomNumber()."_".$request->input('name');
                $whatsapp->credentials =  $credentials;
                $whatsapp->type =  WhatsappDevice::NODE;
               
                $whatsapp->status = 'initiate';
                $whatsapp->save();
                $notify[] = ['success', 'Whatsapp Device successfully added'];

            }else {

                $notify[] = ['error', $responseBody->message];
            } 
        }
        
        return back()->withNotify($notify);
    }

    /**
     * whatsapp update method
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request) {

        $validate         = [];
        $notify           = [];
        if($request->has("whatsapp_business_api") && $request->input("whatsapp_business_api") == true) {

            $required_credentials = config("setting.whatsapp_business_credentials.required");

            foreach($required_credentials as $required_cred_key => $required_cred_value) {

                $validate['credentials'][$required_cred_key] = 'required';
            }
        }

        if($request->has("whatsapp_node_module") && $request->input("whatsapp_node_module") == true) { 
            
            $validate['min_delay'] = 'required|gte:10';
            $validate['max_delay'] = ['required', 'gt:min_delay', new DifferenceRule($request->min_delay)];
        }
        $request->validate($validate);

        $adminId     = auth()->guard('admin')->user()->id;
        $whatsapp    = WhatsappDevice::where('admin_id',$adminId)
            ->where('id', $request->input('id'))
            ->first();

        $credentials = $whatsapp->credentials;
       
        if($request->input("whatsapp_business_api") == true) {

            $credentials = $request->input("credentials");
            $whatsapp->admin_id    = $adminId;
            $whatsapp->name        = $request->input('name');
            $whatsapp->credentials = $credentials;
            $whatsapp->update();
            $notify[] = ['success', 'Whatsapp business API credentials updated successfully'];
        } 
        
        if($request->input("whatsapp_node_module") == true) {

            $whatsapp->admin_id  = $adminId;
            
            $credentials['min_delay']   = $request->input('min_delay');
            $credentials['max_delay']   = $request->input('max_delay');
            $whatsapp->credentials      = $credentials;
            $whatsapp->update();
            $notify[] = ['success', 'WhatsApp device updated successfully.'];
        }
        
        return back()->withNotify($notify);
    }

    /**
     * whatsapp delete method
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request): mixed {

        $adminId  = auth()->guard('admin')->user()->id;
        $whatsApp = WhatsappDevice::where('admin_id',$adminId)->where('id', $request->input('id'))->first();
        $notify   = [];
        try {
            if($whatsApp->type == WhatsappDevice::NODE) {

                $response = Http::delete(env('WP_SERVER_URL').'/sessions/delete/'.$whatsApp->name);
                $notify[] = ['success', 'WhatsApp device has been successfully deleted.'];

                if ($response->status() == 200) {
                    $notify[] = ['success', 'WhatsApp device has been successfully deleted, and the associated sessions have been cleared from the node.'];
                }
                $whatsApp->delete();
            } else {
                WhatsappTemplate::where('cloud_id', $request->input('id'))->delete();
                $whatsApp->delete();
                $notify[] = ['success', 'WhatsApp cloud API removed successfully.'];
            }
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'Oops! Something went wrong. The Node server is not running.'];
            return back()->withNotify($notify);
        }
    }

    /**
     * whatsapp device status update method
     * @param Request $request
     * @return false|string
     */
    public function statusUpdate(Request $request): bool|string {
        $adminId = auth()->guard('admin')->user()->id;
        $wpUrl = env('WP_SERVER_URL');

        $whatsApp = WhatsappDevice::where('admin_id',$adminId)
            ->where('id', $request->input('id'))
            ->first();

        try {
            if ($request->input('status') == 'connected') {
                $session = Http::get($wpUrl.'/sessions/status/'.$whatsApp->name);
                if ($session->status() == 200) {
                    $whatsApp->status = 'connected';
                    $message = "Successfully whatsapp sessions reconnect";
                }else{
                    Http::delete($wpUrl.'/sessions/delete/'.$whatsApp->name);
                    $whatsApp->status = 'disconnected';
                    $message = "Successfully whatsapp sessions disconnected";
                }

            }elseif ($request->input('status') == 'disconnected') {
                $response = Http::delete($wpUrl.'/sessions/delete/'.$whatsApp->name);
                if ($response->status() == 200) {
                    $whatsApp->status = 'disconnected';
                    $message = 'Whatsapp Device successfully Deleted';
                }else{
                    $message = 'Opps! Something went wrong, try again';
                }
            }else{
                $response = Http::delete($wpUrl.'/sessions/delete/'.$whatsApp->name);
                if ($response->status() == 200) {
                    $whatsApp->status = 'disconnected';
                    $message = 'Whatsapp Device successfully Deleted';
                }else{
                    $message ='Opps! Something went wrong, try again';
                }
                $whatsApp->status = $request->input('status');
            }
        }catch (\Exception $exception){
            $message = 'Opps! Something went wrong, try again';
        }

        $whatsApp->update();

        return json_encode([
            'success' => $message
        ]);
    }


    /**
     * whatsapp qr quote scan method
     * @param Request $request
     * @return false|string
     */
    public function whatsappQRGenerate(Request $request): bool|string {
        $whatsapp = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)
            ->where('id', $request->input('id'))
            ->first();
       
        $data = [];
        $currentUrl = $request->root();
        $parsedUrl = parse_url($currentUrl);
        $domain = $parsedUrl['host'];
        
        $response = Http::post(env('WP_SERVER_URL').'/sessions/create', [
            
            'id'       => $whatsapp->name,
            'isLegacy' => !(array_key_exists("multidevice", $whatsapp->credentials) && $whatsapp->credentials["multidevice"] ? $whatsapp->credentials["multidevice"] : 'NO' === "YES"),
            'domain'   => $domain
        ]);
       
        $responseBody = json_decode($response->body());
        
        if ($response->status() === 200) {
            $data['status']  = $response->status();
            $data['qr']      = $responseBody->data->qr;
            $data['message'] = $responseBody->message;
        } else {
            $msg = $response->status() === 500 ? "Invalid Software License" : $responseBody->message;
            $data['status'] = $response->status();
            $data['qr'] = '';
            $data['message'] = $msg;
        }

        $response = [
            'response' => $whatsapp,
            'data' => $data
        ];

        return json_encode($response);
    }

    /**
     * whatsapp wp device status method
     * @param Request $request
     * @return false|string
     */
    public function getDeviceStatus(Request $request): bool|string {

        $whatsapp = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)->where('id', $request->input('id'))->first();
        $credentials =  $whatsapp->credentials;
        $data = [];
        $wpUrl = env('WP_SERVER_URL');
        try {
            $checkConnection = Http::get($wpUrl.'/sessions/status/'.$whatsapp->name);
            if ($whatsapp->status == "connected" || $checkConnection->status() === 200) {
                $whatsapp->status = 'connected';
                $response = json_decode($checkConnection->body());
                if (isset($response->data->wpInfo)) {

                    $wpNumber = str_replace('@s.whatsapp.net', '', $response->data->wpInfo->id);
                    $wpNumber = explode(':', $wpNumber);
                    $wpNumber = $wpNumber[0] ?? $whatsapp->credentials["number"];
                    try {
                        $credentials["number"] = $wpNumber;
                        $whatsapp->credentials = $credentials;
                    } catch (Exception $e) {
                        dd($e);
                    }
                }
                $whatsapp->save();
                $data['status']  = 301;
                $data['qr']      = asset('assets/file/dashboard/image/done.gif');
                $data['message'] = 'Successfully connected WhatsApp device';
            }
        } catch (\Exception $e) {
            $data['status'] = $e->getCode();
            $data['qr'] = '';
            $data['message'] = $e->getMessage();
        }

        $response = [
            'response' => $whatsapp,
            'data' => $data
        ];
        return json_encode($response);
    }

    /**
     * Whatsapp Server Informations
     * Update Method
     * @param Request $request
     * 
     */ 

    public function updateServer(Request $request) {
        
        $request->validate([
           
            'server_host'        => 'required|ip',
            'server_port'        => 'required|numeric',
            'max_retries'        => 'required|numeric',
            'reconnect_interval' => 'required|numeric',
        ]);
        
        $updated_env = [
            
            'WP_SERVER_URL'      => "http://$request->server_host:$request->server_port",
            'NODE_SERVER_HOST'   => $request->server_host,
            'NODE_SERVER_PORT'   => $request->server_port,
            'MAX_RETRIES'        => $request->max_retries,
            'RECONNECT_INTERVAL' => $request->reconnect_interval,
        ];

        $path = app()->environmentFilePath();
        foreach ($updated_env as $key => $value) {
            $escaped = preg_quote('='.env($key), '/');
            
            file_put_contents($path, preg_replace(
                "/^{$key}{$escaped}/m",
                "{$key}={$value}",
                file_get_contents($path)
            ));

        }
        $notify[] = ['success', 'Whatsapp Server Settings Updated'];
        return back()->withNotify($notify);
    }

    protected function checkWhatsappServerStatus() {

        $checkWhatsappServer = true;
        try {

            $wpUrl = env('WP_SERVER_URL');
            Http::get($wpUrl);

        } catch (\Exception $e) {

            $checkWhatsappServer = false;
        }

        $devices = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($devices as $value) {
            try {
                $sessions               = Http::get($wpUrl . '/sessions/status/' . $value->name);
                $whatsAppDevice         = WhatsappDevice::where('id', $value->id)->first();
                $whatsAppDevice->status = 'disconnected';
                if ($sessions->status() == 200) {

                    $whatsAppDevice->status = 'connected';
                }
                $whatsAppDevice->save();
            } catch (\Exception $e) {

                $checkWhatsappServer = false;
            }
        }
        return $checkWhatsappServer;
    }
}
