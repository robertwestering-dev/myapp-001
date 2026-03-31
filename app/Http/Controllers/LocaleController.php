<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $supportedLocales = array_keys(config('locales.supported', []));
        $sessionKey = config('locales.session_key', 'locale');

        $attributes = $request->validate([
            'locale' => ['required', 'string', Rule::in($supportedLocales)],
        ]);

        $locale = $attributes['locale'];

        $request->session()->put($sessionKey, $locale);

        if ($request->user() !== null && $request->user()->locale !== $locale) {
            $request->user()->forceFill([
                'locale' => $locale,
            ])->save();
        }

        app()->setLocale($locale);

        return back();
    }
}
