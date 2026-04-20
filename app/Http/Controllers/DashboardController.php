<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use App\Models\OrganizationQuestionnaire;
use App\Services\SuccessfulLoginSummary;
use App\Support\Questionnaires\AvailableQuestionnaireCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, AvailableQuestionnaireCatalog $catalog): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->canAccessAdminPortal()) {
            return redirect()->route('admin.portal');
        }

        $localeContext = $catalog->localeContext($request, $user);
        $availableQuestionnaires = $catalog->forUser($user, $localeContext['locale']);

        $completedQuestionnaireCount = $availableQuestionnaires
            ->sum(fn (OrganizationQuestionnaire $organizationQuestionnaire): int => $organizationQuestionnaire->completedResponses->count());

        $draftQuestionnaireCount = $availableQuestionnaires
            ->filter(fn (OrganizationQuestionnaire $organizationQuestionnaire): bool => $organizationQuestionnaire->currentResponse?->isDraft() ?? false)
            ->count();

        return view('dashboard', [
            'activeQuestionnaireLocale' => $localeContext['locale'],
            'activeQuestionnaireLocaleLabel' => $localeContext['label'],
            'activeQuestionnaireLocaleSource' => $localeContext['source'],
            'academyCourseCount' => AcademyCourse::query()->active()->count(),
            // Academy progress tracking is not yet implemented, so "in progress" remains a placeholder count for now.
            'inProgressAcademyCourseCount' => 0,
            // TODO: Academy completion tracking is not yet implemented. Implement a completion model before showing real data here.
            'completedAcademyCourseCount' => 0,
            'availableQuestionnaireCount' => $availableQuestionnaires->count(),
            'completedQuestionnaireCount' => $completedQuestionnaireCount,
            'draftQuestionnaireCount' => $draftQuestionnaireCount,
            'loginSummary' => $request->session()->pull(SuccessfulLoginSummary::SESSION_KEY),
        ]);
    }
}
