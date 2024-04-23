<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class UpgradeVersionMigrateController extends Controller
{
    public function index() {
        
        $file_path = base_path('update_info.md');
        $file_contents = [];
        $markdownContent = File::get($file_path);
        $sections = explode('## ', $markdownContent);
        array_shift($sections);
        foreach ($sections as $section) {
            
            $section = trim($section);
            list($section_title, $section_content) = explode("\n", $section, 2);
            $file_contents[$section_title] = $section_content;
        }   

        $general = GeneralSetting::first();
        $current_version = $general->app_version; 
        $new_version     = config('requirements.core.appVersion'); 
        $title = "update $new_version";
        return view('update.index', compact(
            'general',
            'current_version',
            'new_version',
            'title',
            'file_contents'
        ));
    }

    public function update() {
        
        $general         = GeneralSetting::first();
        $whatsappDevices = WhatsappDevice::all();
        $whatsappLogs    = WhatsappLog::all();
        $users           = User::all();
        $current_version = $general->app_version; 
        $new_version     = config('requirements.core.appVersion'); 
        $file_path       = base_path('update_info.md');

        if(version_compare($new_version, $current_version, '>')) {
            
            try {
                session(["queue_restart" => true]);

                $migrationFiles = [
                    '/database/migrations/2024_03_10_055818_alter_table_wa_device.php',
                    '/database/migrations/2024_03_11_083134_alter_whatsapp_logs_table.php',
                    '/database/migrations/2024_03_12_080119_add_webhook_to_general_settings_table.php',
                    '/database/migrations/2024_03_13_043127_create_whatsapp_templates_table.php',
                    '/database/migrations/2024_03_18_085431_create_post_webhook_logs_table.php',
                    '/database/migrations/2024_03_23_031839_alter_users_table.php',
                    '/database/migrations/2024_03_24_063443_alter_table_email_logs.php',
                    '/database/migrations/2024_03_24_063427_alter_table_s_m_slogs.php',
                    '/database/migrations/2024_03_24_063414_alter_table_whatsapp_logs.php',
                ];
                $dropTableOrColumn = [
                    '/database/migrations/2024_03_10_093700_drop_whatsapp_device_columns.php',
                    '/database/migrations/2024_03_11_105148_drop_android_api_sim_info_columns.php',
                ];
                foreach($migrationFiles as $migrationFile) {
                    Artisan::call('migrate', ['--force' => true, '--path' => $migrationFile ]);
                }   

                $this->userUpdate($users);
                $this->whatsappDeviceUpdate($whatsappDevices);
                $this->whatsappLogsUpdate($whatsappLogs);
                $this->generalSetup($general);
               
                //Update general settings
              
                foreach($dropTableOrColumn as $drop) {
                    Artisan::call('migrate', ['--force' => true, '--path' => $drop ]);
                }
                
                if (File::exists($file_path)) {
                
                    File::delete($file_path);
                }

                Artisan::call('queue:restart');
                Artisan::call('optimize:clear');
                $this->versionUpdate($general, $new_version);
                $notify[] = ['success', 'Succesfully updated database.'];
                return redirect()->route('admin.dashboard')->withNotify($notify);
                
            }catch(\Exception $e) {
                
                $notify[] = ['error', "Internal Error"];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['error', "No update needed"];
        return back()->withNotify($notify);
    }

    private function userUpdate($users) {
        
       
        foreach($users->chunk(5) as $chunk) {
            foreach($chunk as $user) {

                $user->uid         = str_unique(); 
                $user->save();
            }
        }
    }
    private function whatsappDeviceUpdate($whatsappDevices) {

        $old_data = [];
        foreach($whatsappDevices->chunk(5) as $devices) {
            foreach($devices as $device) {

                $old_data['number']      = $device->number;
                $old_data['min_delay']   = $device->min_delay;
                $old_data['max_delay']   = $device->max_delay;
                $old_data['multidevice'] = $device->multidevice;
                
                $device->credentials = $old_data;
                $device->type        = WhatsappDevice::NODE;
                $device->uid         = str_unique(); 
                $device->save();
            }
        }
    }
    private function generalSetup($general) {
        $general->webhook = config("setting.webhook");
        $general->save();
    }
    private function whatsappLogsUpdate($whatsappLogs) {

        foreach($whatsappLogs->chunk(5) as $logs) {
            foreach($logs as $log) {

                $log->mode        = WhatsappLog::NODE;
                $log->save();
            }
        }
    }
    
    public function versionUpdate($general, $new_version) {
        $current_version      = $new_version;
        $general->app_version = $current_version;
        $general->save();
    }

    public function verify() {
        
        $general         = GeneralSetting::first();
        $current_version = $general->app_version;
        $new_version     = config('requirements.core.appVersion'); 
        $title           = "update $new_version";
        return view('update.verify', compact(
            'general',
            'current_version',
            'new_version',
            'title'
        ));

    }

    public function store(Request $request) {

        $admin_credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
            
        ]);
        $request->validate([
            'purchased_code' => ['required'],
        ]);
        
        try {
            
            if (Auth::guard('admin')->attempt($admin_credentials)) {

                $buyer_domain   = url()->current();
                $purchased_code = $request->purchased_code;
                $response = Http::withoutVerifying()->get('https://license.igensolutionsltd.com', [
                    'buyer_domain'   => $buyer_domain,
                    'purchased_code' => $purchased_code,
                ]);
               
                if($response->json()['status']) {
                    if(File::exists(base_path('update_info.md'))) {
                        Session::put('is_verified', true);
                        $notify[] = ['success', "Verification Successfull"];
                        return redirect()->route('admin.update.index')->withNotify($notify);
                    } 
                    $notify[] = ['error', "Files are not available"];
                    return back()->withNotify($notify); 
                    
                } else {
                    $notify[] = ['error', "Invalid licence key"];
                    return back()->withNotify($notify);
                }
            }
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        } catch(\Exception $e) {
           
            $notify[] = ['info', "Please Try Again"];
            return back()->withNotify($notify);
        }
    }
}
