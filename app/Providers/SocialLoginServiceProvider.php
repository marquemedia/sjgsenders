<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Config;
class SocialLoginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $general = GeneralSetting::first();
            if($general && $general->social_login){
                $google = [
                    'client_id' => Arr::get($general->social_login, 'g_client_id', ''),
                    'client_secret' => Arr::get($general->social_login, 'g_client_secret', ''),
                    'redirect' => url('auth/google/callback'),
                ];
                Config::set('services.google', $google);

            }
        }catch(\Exception $exception){

        }
    }
}
