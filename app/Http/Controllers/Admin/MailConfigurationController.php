<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Utility\SendMail;
use Carbon\Carbon;
use App\Models\EmailTemplates;
use App\Models\Gateway;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;


class MailConfigurationController extends Controller
{
    /**
     * Mail Gateway List
     *
     * @param Request $request
     * @return View
     */

     public function index(Request $request) :View {
       
        $title       = "Connection List";
        $gateways    = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->orderBy('is_default', 'DESC')->paginate(paginateNumber());
        $gateway     = Gateway::whereNotNull('mail_gateways')->whereNull('user_id')->where('uid', $request->uid);
        $credentials = config('setting.gateway_credentials.email');

        return view('admin.mail.index', compact('title', 'gateways', 'credentials', 'gateway'));
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

            Gateway::whereNotNull('mail_gateways')->where('id', '!=',$request->id)->whereNull('user_id')->update([ "is_default" => 0 ]);
            $gateway->is_default = $request->default_value;
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
     * Updates the gateway  
     * @param Request $request
     * 
     */
    public function gatewayUpdate(Request $request) {

        $this->validate($request, [
            'type'               => "required",
            'driver_information' => "required",
            'name'               => ["required"],
            'address'            => "required",
            'status'             => "required|in:0,1",
        ]); 

        $mail                = Gateway::findOrFail($request->id);
        $mail->type          = $request->input('type');
        $mail->name          = $request->input('name');
        $mail->address       = $request->input('address');
        $mail->mail_gateways = $request->input('driver_information');
        $mail->status        = $request->input('status');
        $mail->save();
        $notify[] = ['success', ucfirst($mail->type). ' method under: '.ucfirst($mail->name). ' has been updated'];

        return back()->withNotify($notify);
    }

    /**
    * Create Gateway
    * @param Request $request
    */
    public function create(Request $request) {
    
        $this->validate($request, [
            'type'               => "required|in:smtp,sendgrid,aws,mailjet,mailgun",
            'driver_information' => ["required"],
            'address'            => ["required"],
            'name'               => ["required"],
            'status'             => "required|in:0,1",
        ]);

        $mail                = new Gateway();
        $mail->status        = $request->input('status');
        $mail->type          = $request->input('type');
        $mail->name          = $request->input('name');
        $mail->address       = $request->input('address');
        $mail->mail_gateways = $request->input('driver_information');
        $mail->sms_gateways  = null;
        $mail->save();

        $notify[] = ['success', 'A new '.ucfirst($mail->type). ' method has been created under: '.ucfirst($mail->name)];
        return back()->withNotify($notify);
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

    /**
     * Default Method Test 
     * @param Request $request
     * @return mixed
     */
    public function mailTester(Request $request) :mixed
    {
        
        $general     = GeneralSetting::first();
        $mailGateway = Gateway::whereNotNull('mail_gateways')->where('is_default', 1)->first();
        if($mailGateway == null) {
            return json_encode([
                'address' => 'Default Gateway and ', 
                'status'  => false,
            ]);
        }
        $response      = " ";
        $emailTemplate = EmailTemplates::where('slug', 'TEST_MAIL')->first();
        $messages      = str_replace("{{name}}", @$general->site_name, $emailTemplate->body);
        $messages      = str_replace("{{time}}", @Carbon::now(), $messages);
       

        if($mailGateway->type == "smtp") {
            
            $response = SendMail::sendSMTPMail($request->input('email'), $emailTemplate->subject, $messages, $mailGateway);
        }
        elseif($mailGateway->type == "mailjet") {

            $response = SendMail::sendMailJetMail($request->input('email'), $emailTemplate->subject, $messages, $general, $mailGateway); 
        }
        elseif($mailGateway->type == "aws") {
            
            $response = SendMail::sendMailJetMail($request->input('email'), $emailTemplate->subject, $messages, $general, $mailGateway); 
        }
        elseif($mailGateway->type == "mailgun") {
            
            $response = SendMail::sendMailGunMail($request->input('email'), $emailTemplate->subject, $messages, $general, $mailGateway); 
        }
        elseif($mailGateway->type === "sendgrid") {
           
            $response = SendMail::sendGrid($mailGateway->address, $general->site_name, $request->input('email'), $emailTemplate->subject, $messages, @$mailGateway->mail_gateways->app_key);
        }

        if ($response==null) {
            return json_encode([
                'address' => $request->email,
                'status'  => true,
            ]);
        }
        else{
            return json_encode([
                'address' => $mailGateway->name, 
                'status'  => false,
            ]);
        }
      
    }
    
     /**
     * Global Template Update
     *
     * @param Request $request
     * 
     */

     public function globalTemplateUpdate(Request $request)
     {
         $this->validate($request,[
             'mail_from' => 'required|email',
             'body'      => 'required',
         ]);
 
         $general                 = GeneralSetting::first();
         $general->mail_from      = $request->input('mail_from');
         $general->email_template = $request->input('body');
         $general->save();
 
         $notify[] = ['success', 'Global email template has been updated'];
         return back()->withNotify($notify);
 
     }
}
