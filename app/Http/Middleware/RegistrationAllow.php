<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Http\Response;

class RegistrationAllow
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $general = GeneralSetting::first();
        if ($general->registration_status != 1) {
            $notify[] = ['error', translate('Registration is currently off.')];
            return back()->withNotify($notify);
        }

        return $next($request);

    }
}
