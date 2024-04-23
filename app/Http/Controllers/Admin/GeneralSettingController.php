<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Image;
use App\Models\PricingPlan;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $title = "General Setting";
        $general = GeneralSetting::first();
        $timeLocations = timezone_identifiers_list();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country_file.json')));
        $plans = PricingPlan::select('id', 'name')->latest()->get();

        return view('admin.setting.index', compact('title', 'general', 'timeLocations','countries', 'plans'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'site_name' => 'required|max:255',
            'primary_color' => 'required',
            'secondary_color' => 'required',
            'copyright' => 'required|max:20', 
            'country_code' => 'required|max:30',
            'currency_name' => 'required|max:10',
            'currency_symbol' => 'required|max:10',
            'email_verification_status' => 'required|in:1,2',
            'site_logo' => 'nullable|image|mimes:jpg,png,jpeg',
            'site_favicon' => 'nullable|image|mimes:jpg,png,jpeg',
            'site_icon' => 'nullable|image|mimes:jpg,png,jpeg',
            'whatsapp_word_count' => 'required|integer|gt:0',
            'sms_word_text_count' => 'required|integer|gt:0',
            'sms_word_unicode_count' => 'required|integer|gt:0',
        ]);

        $timeLocationFile = config_path('timesetup.php');
        $time = '<?php $timelog = '.$request->input('timelocation').' ?>';
        file_put_contents($timeLocationFile, $time);
        
        $general = GeneralSetting::first();
        $general->plan_id = $request->input('plan_id');
        $general->sign_up_bonus = $request->input('sign_up_bonus');
        $general->site_name = $request->input('site_name');
        $general->copyright = $request->input('copyright');
        $general->country_code = $request->input('country_code');
        $general->sms_gateway = $request->input('sms_gateway');
        $general->registration_status  = $request->input('registration_status');
        $general->email_verification_status  = $request->input('email_verification_status');
        $general->currency_name = $request->input('currency_name');
        $general->primary_color = $request->input('primary_color');
        $general->secondary_color = $request->input('secondary_color');
        $general->currency_symbol = $request->input('currency_symbol');
        $general->whatsapp_word_count  = $request->input('whatsapp_word_count');
        $general->sms_word_text_count = $request->input('sms_word_text_count');
        $general->sms_word_unicode_count = $request->input('sms_word_unicode_count');
        $general->cron_pop_up = $request->input('cron_pop_up')=="" ? "false" : $request->input('cron_pop_up');
        $general->landing_page = $request->input('landing_page')=="" ? "false" : $request->input('landing_page');
        $general->debug_mode = $request->input('debug_mode')=="" ? "false" : $request->input('debug_mode');
        $general->maintenance_mode = $request->input('maintenance_mode')=="" ? "false" : $request->input('maintenance_mode');
        $general->maintenance_mode_message = $request->input('maintenance_mode_message'); 


        $path = base_path('.env');
        $env_content = file_get_contents($path);


        if ($request->has('debug_mode')) {
            if (file_exists($path)) {
               file_put_contents($path, str_replace('APP_ENV=production', 'APP_ENV=local', $env_content));
               $env_content = file_get_contents($path);
               file_put_contents($path, str_replace('APP_DEBUG=false', 'APP_DEBUG=true', $env_content));
            }

        }else{
            if (file_exists($path)) {
               file_put_contents($path, str_replace('APP_ENV=local', 'APP_ENV=production', $env_content));
               $env_content = file_get_contents($path);
               file_put_contents($path, str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $env_content));
            }
        }
        $panel_logo = $general->panel_logo;
        $site_logo = $general->site_logo;
        $favicon = $general->favicon;
        $site_icon = $general->site_icon;

        if($request->hasFile('panel_logo')) {
            try{
                $general->panel_logo = StoreImage($request->panel_logo, filePath()['panel_logo']['path'],null, $panel_logo ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Panel Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        if($request->hasFile('site_logo')) {
            try{
                $general->site_logo = StoreImage($request->site_logo, filePath()['site_logo']['path'],null, $site_logo ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Site Logo could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        if($request->hasFile('site_favicon')) {
            try{
                $general->favicon = StoreImage($request->site_favicon, filePath()['site_logo']['path'],filePath()['favicon']['size'], $favicon ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Favicon could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        if($request->hasFile('site_icon')) {
            try{
                $general->site_icon = StoreImage($request->site_icon, filePath()['site_logo']['path'],filePath()['site_icon']['size'], $site_icon ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Site Icon could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $general->save();
        $notify[] = ['success', 'General Setting has been updated'];
        return back()->withNotify($notify);
    }

    public function cacheClear()
    {
        Artisan::call('optimize:clear');
        $notify[] = ['success','Cache cleared successfully'];
        return back()->withNotify($notify);
    }

    public function installPassportKey()
    {
        shell_exec('php ../artisan passport:install');
        shell_exec('php ../artisan passport:keys');

        $notify[] = ['success','Passport api key generated successfully'];
        return back()->withNotify($notify);
    }

    public function systemInfo()
    {
        $title = "System Information";

        $systemInfo = [
            'laravelversion' => app()->version(),
            'serverdetail' => $_SERVER,
            'phpversion' => phpversion(),
        ];
        return view('admin.system_info',compact('title','systemInfo'));
    }

    public function socialLogin() {

        $title = "Social Login Credentials";
        $general = GeneralSetting::first();
        $credentials = array_replace_recursive(config('setting.google'), (array)$general->social_login);
        return view('admin.setting.socal_login', compact('title', 'credentials'));
    }

    

    /**
     * @throws ValidationException
     */
    public function socialLoginUpdate(Request $request) {

        $this->validate($request, [
            'g_client_id' => 'required',
            'g_client_secret' => 'required',
            'g_client_status' => 'required|in:1,2',
        ]);

        $general = GeneralSetting::first();
        $general->social_login = array_replace_recursive(config('setting.google'), $request->only(['g_client_id','g_client_secret', 'g_client_status']));
        $general->save();

        $notify[] = ['success', 'Social login setting has been updated'];
        return back()->withNotify($notify);
    }
    
    /**
     * reCaptcha Functionality
     */
    public function recaptcha() {
        $title = translate("reCaptcha Settings");
        $general = GeneralSetting::first();
        $credentials = array_replace_recursive(config('setting.recaptcha'), (array)$general->recaptcha);
        return view('admin.setting.recaptcha', compact('title', 'credentials'));
    }

     /**
     * @throws ValidationException
     */
    public function recaptchaUpdate(Request $request) {
        
        $this->validate($request, [
            'recaptcha_key'    => 'required',
            'recaptcha_secret' => 'required',
            'recaptcha_status' => 'required|in:1,2',
        ]);
        $general = GeneralSetting::first();
        $general->default_recaptcha         = $request->input("default_recaptcha") ?? false;
        $general->captcha_with_registration = $request->input("captcha_with_registration") ?? false;
        $general->captcha_with_login        = $request->input("captcha_with_login") ?? false;
        
        $general->recaptcha                 = array_replace_recursive(config('setting.recaptcha'), $request->only(['recaptcha_key','recaptcha_secret', 'recaptcha_status']));
        
        $general->save();

        $notify[] = ['success', 'reCAPTCHA setting has been updated'];
        return back()->withNotify($notify);
    }

    public function webhookConfig() {

        $title       = "Webhook Configuration";
        $general     = GeneralSetting::first();
        $credentials = array_replace_recursive(config('setting.webhook'), (array)$general->webhook);
        return view('admin.setting.webhook_configuration', compact('title', 'credentials'));
    }

      /**
     * @throws ValidationException
     */
    public function webhookUpdate(Request $request) {
        
        $this->validate($request, [
            'callback_url' => 'required',
            'verify_token' => 'required',
        ]);

        $general = GeneralSetting::first();
        $general->webhook                 = array_replace_recursive(config('setting.webhook'), $request->only(['callback_url','verify_token']));
        
        $general->save();

        $notify[] = ['success', 'Webhook settings has been updated'];
        return back()->withNotify($notify);
    }

    /**
     * @return View
     */
    public function frontendSection(): View
    {
        $title = "Manage Frontend Section";
        return view('admin.setting.frontend_section', compact('title'));
    }


    /**
     * @throws ValidationException
     */
    public function frontendSectionStore(Request $request)
    {
        
         $this->validate($request, [
            'heading' => 'required',
            'sub_heading' => 'required',
            'admin_bg' => 'nullable|image|mimes:jpg,png,jpeg',
            'admin_card' => 'nullable|image|mimes:jpg,png,jpeg',
        ]);

        $general = GeneralSetting::first();
        $frontend = [
            'heading' => $request->input('heading'),
            'sub_heading' => $request->input('sub_heading'),
        ];
        $admin_bg = $general->admin_bg;
        $admin_card = $general->admin_card;
        if($request->hasFile('admin_bg')) {
            try{
                $general->admin_bg = StoreImage($request->admin_bg, filePath()['admin_bg']['path'],null, $admin_bg ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Admin Background could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        if($request->hasFile('admin_card')) {
            try{
                $general->admin_card = StoreImage($request->admin_card, filePath()['admin_card']['path'],null, $admin_card ?: null);
            }catch (\Exception) {
                $notify[] = ['error', 'Admin Login Card Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $general->frontend_section = $frontend;
        $general->save();

        $notify[] = ['success', 'Frontend section has been updated'];
        return back()->withNotify($notify);
    }


    /**
     * social media login configaration
     */
    public function beefree()
    {

        $title = "Bee Free Plugin";
        return view('admin.setting.bee_plugin', compact('title'));
    }

    /**
     * social media login configuration update
     * @param Request $request
     * @return mixed
     */
    public function beefreeUpdate(Request $request)
    {
        $this->credentialUpdate($request,'bee_plugin');
        $notify[] = ['success', translate('Beefree Credential Updated Successfully')];
        return back()->withNotify($notify);
    }


    /**
     * update json credential
     * @param $request
     * @param $column
     * @param bool $key
     */
    public function credentialUpdate($request, $column, bool $key = false): void
    {
        $generalSettings = GeneralSetting::first();
        $generalSettings->$column = json_encode($request->$column) ;
        $generalSettings->save();
    }
}
