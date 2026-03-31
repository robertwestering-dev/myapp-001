<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetApplicationLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = array_keys(config('locales.supported', []));
        $defaultLocale = config('app.locale');
        $sessionKey = config('locales.session_key', 'locale');
        $hostDefaults = config('locales.host_defaults', []);
        $sessionLocale = $request->session()->get($sessionKey);
        $userLocale = $request->user()?->locale;
        $hostLocale = $hostDefaults[$request->getHost()] ?? null;

        $locale = $defaultLocale;

        if (in_array($hostLocale, $supportedLocales, true)) {
            $locale = $hostLocale;
        }

        if (in_array($sessionLocale, $supportedLocales, true)) {
            $locale = $sessionLocale;
        }

        if (in_array($userLocale, $supportedLocales, true)) {
            $locale = $userLocale;
        }

        App::setLocale($locale);
        $request->session()->put($sessionKey, $locale);

        return $next($request);
    }
}
