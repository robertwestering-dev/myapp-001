<?php

namespace App\Http\Controllers;

use App\Models\OrganizationQuestionnaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

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

        $redirectResponse = $this->redirectForQuestionnaireLocaleMismatch($request, $locale);

        if ($redirectResponse !== null) {
            return $redirectResponse;
        }

        return back();
    }

    protected function redirectForQuestionnaireLocaleMismatch(Request $request, string $locale): ?RedirectResponse
    {
        $referer = $request->headers->get('referer');

        if (! filled($referer)) {
            return null;
        }

        try {
            $previousRequest = Request::create($referer, 'GET');
            $previousRoute = Route::getRoutes()->match($previousRequest);
        } catch (Throwable $exception) {
            if ($exception instanceof HttpExceptionInterface) {
                return null;
            }

            return null;
        }

        if ($previousRoute->getName() !== 'questionnaire-responses.show') {
            return null;
        }

        $organizationQuestionnaire = $previousRoute->parameter('organizationQuestionnaire');

        if (! $organizationQuestionnaire instanceof OrganizationQuestionnaire) {
            $organizationQuestionnaire = OrganizationQuestionnaire::query()->find($organizationQuestionnaire);
        }

        if (! $organizationQuestionnaire instanceof OrganizationQuestionnaire) {
            return null;
        }

        $organizationQuestionnaire->loadMissing('questionnaire');

        if ($organizationQuestionnaire->questionnaire?->locale === $locale) {
            return null;
        }

        return redirect()
            ->route('questionnaires.index')
            ->with('status', __('hermes.questionnaire.locale_switch_unavailable'));
    }
}
