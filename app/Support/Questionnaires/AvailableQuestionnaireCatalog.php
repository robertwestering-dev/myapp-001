<?php

namespace App\Support\Questionnaires;

use App\Models\OrganizationQuestionnaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AvailableQuestionnaireCatalog
{
    /**
     * @return Collection<int, OrganizationQuestionnaire>
     */
    public function forUser(User $user, string $preferredLocale): Collection
    {
        return OrganizationQuestionnaire::query()
            ->with([
                'questionnaire',
                'responses' => fn ($query) => $query
                    ->select([
                        'id',
                        'organization_questionnaire_id',
                        'user_id',
                        'submitted_at',
                        'last_saved_at',
                        'resume_token',
                        'updated_at',
                    ])
                    ->where('user_id', $user->id)
                    ->latest('updated_at'),
            ])
            ->where('org_id', $user->org_id)
            ->where('is_active', true)
            ->get()
            ->filter(fn (OrganizationQuestionnaire $organizationQuestionnaire): bool => $organizationQuestionnaire->isAvailable())
            ->filter(fn (OrganizationQuestionnaire $organizationQuestionnaire): bool => $organizationQuestionnaire->questionnaire?->locale === $preferredLocale)
            ->map(function (OrganizationQuestionnaire $organizationQuestionnaire): OrganizationQuestionnaire {
                $organizationQuestionnaire->setRelation(
                    'currentResponse',
                    $organizationQuestionnaire->responses->sortByDesc('updated_at')->first(),
                );
                $organizationQuestionnaire->unsetRelation('responses');

                return $organizationQuestionnaire;
            })
            ->values();
    }

    /**
     * @return array{label: string, locale: string, source: string}
     */
    public function localeContext(Request $request, User $user): array
    {
        $sessionLocale = (string) $request->session()->get(config('locales.session_key', 'locale'), config('app.locale'));
        $preferredLocale = $user->locale ?: $sessionLocale;

        return [
            'label' => (string) config("locales.supported.{$preferredLocale}", strtoupper($preferredLocale)),
            'locale' => $preferredLocale,
            'source' => $user->locale ? 'profile' : 'session',
        ];
    }
}
