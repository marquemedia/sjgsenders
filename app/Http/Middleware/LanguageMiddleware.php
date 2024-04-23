<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LanguageMiddleware
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
        session()->put('lang', $this->getLanguageCode());
        app()->setLocale(session('lang',  $this->getLanguageCode()));
        return $next($request);
    }

    public function getLanguageCode()
    {
        try{
            if (session()->has('lang'))
            {
                return session('lang');
            }
            $languageData = Language::where('is_default', 1)->first();
            return $languageData ? $languageData->code : 'en';
        }catch (\Exception $e) {

        }
    }
}
