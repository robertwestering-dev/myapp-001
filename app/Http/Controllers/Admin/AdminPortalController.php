<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Actions\Translations\ManageHermesTranslations;
use App\Http\Controllers\Controller;
use App\Models\AcademyCourse;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AdminPortalController extends Controller
{
    public function index(Request $request, ManageHermesTranslations $translations): View
    {
        /** @var User $actor */
        $actor = $request->user();

        return view('admin-portal', [
            'actor' => $actor,
            'canManageLibrary' => $actor->isAdmin(),
            'lead' => $actor->isAdmin()
                ? "U bent ingelogd als admin met het account {$actor->email}. Vanuit deze omgeving beheert u organisaties, questionnaires en rapportage over alle organisaties heen."
                : "U bent ingelogd als beheerder met het account {$actor->email}. Vanuit deze omgeving beheert u uw eigen organisatie, stelt u questionnaires beschikbaar en bekijkt u responses binnen uw scope.",
            'questionnaireCount' => Questionnaire::query()->count(),
            'academyCourseCount' => AcademyCourse::query()->count(),
            'translationCount' => $translations->all()->count(),
            'scopedAvailabilityCount' => OrganizationQuestionnaire::query()
                ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                    $query->where('org_id', $actor->org_id);
                })
                ->count(),
            'spotlightQuestionnaires' => $this->spotlightQuestionnaires($actor),
        ]);
    }

    /**
     * @return Collection<int, Questionnaire>
     */
    protected function spotlightQuestionnaires(User $actor): Collection
    {
        $spotlightTitles = [
            SyncAdaptabilityAceQuestionnaire::TITLE,
            SyncDigitalResilienceQuickScanQuestionnaire::TITLE,
        ];

        $questionnaires = Questionnaire::query()
            ->whereIn('title', $spotlightTitles)
            ->withCount([
                'categories',
                'questions',
                'organizationQuestionnaires as scoped_organization_questionnaires_count' => function (Builder $query) use ($actor): void {
                    if (! $actor->isAdmin()) {
                        $query->where('org_id', $actor->org_id);
                    }
                },
            ])
            ->orderBy('title')
            ->get();

        return collect($spotlightTitles)
            ->map(fn (string $title): ?Questionnaire => $questionnaires->firstWhere('title', $title))
            ->filter()
            ->values();
    }
}
