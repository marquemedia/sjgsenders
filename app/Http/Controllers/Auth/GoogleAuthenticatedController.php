<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Service\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Models\GeneralSetting;
class GoogleAuthenticatedController extends Controller
{
    public CustomerService $customerService;
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }
    public function redirectToGoogle()
    {
        $general = GeneralSetting::first();
        if(Arr::get($general->social_login, 'g_client_status', 1) != 1){
            $notify[] = ['error', 'Currently, social login is not enabled'];
            return back()->withNotify($notify);
        }
        return Socialite::driver('google')->redirect();
    }
    /**
     * @return RedirectResponse
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/');
        }
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            Auth::login($existingUser);
        } else {
            $newUser  = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->google_id = $user->id;
            $newUser->gateways_credentials = config('setting.gateway_credentials');
            $newUser->email_verified_status = User::YES;
            $newUser->email_verified_code = null;
            $newUser->email_verified_at = carbon();
            $newUser->save();
            $this->customerService->applySignUpBonus($newUser);
            Auth::login($newUser);
        }
        return redirect(RouteServiceProvider::HOME);
    }
}