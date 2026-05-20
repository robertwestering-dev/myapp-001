<?php

namespace App\Http\Controllers;

use App\Models\AcademyCourse;
use App\Models\AcademyCourseProgress;
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

        $academyProgressQuery = $user->academyCourseProgress()
            ->whereHas('academyCourse', fn ($query) => $query->active());

        return view('dashboard', [
            'activeQuestionnaireLocale' => $localeContext['locale'],
            'activeQuestionnaireLocaleLabel' => $localeContext['label'],
            'activeQuestionnaireLocaleSource' => $localeContext['source'],
            'academyCourseCount' => AcademyCourse::query()->active()->count(),
            'inProgressAcademyCourseCount' => (clone $academyProgressQuery)
                ->where('status', AcademyCourseProgress::STATUS_IN_PROGRESS)
                ->count(),
            'completedAcademyCourseCount' => (clone $academyProgressQuery)
                ->where('status', AcademyCourseProgress::STATUS_COMPLETED)
                ->count(),
            'journalEntryCount' => $user->journalEntries()->count(),
            'latestJournalEntryDate' => $user->journalEntries()->max('entry_date'),
            'availableQuestionnaireCount' => $availableQuestionnaires->count(),
            'completedQuestionnaireCount' => $completedQuestionnaireCount,
            'draftQuestionnaireCount' => $draftQuestionnaireCount,
            'loginSummary' => $request->session()->pull(SuccessfulLoginSummary::SESSION_KEY),
        ]);
    }
}
