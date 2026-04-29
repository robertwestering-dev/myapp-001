<?php

namespace App\Http\Controllers;

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Support\Questionnaires\Results\QuestionnaireResultsEngine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AcademyPermaWidgetController extends Controller
{
    public function __construct(
        private readonly QuestionnaireResultsEngine $resultsEngine,
    ) {}

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $latestResponse = QuestionnaireResponse::query()
            ->whereBelongsTo($user)
            ->whereNotNull('submitted_at')
            ->whereHas('organizationQuestionnaire.questionnaire', function ($query): void {
                $query->where('title', SyncPositiveFoundationQuestionnaire::TITLE);
            })
            ->with('organizationQuestionnaire.questionnaire')
            ->latest('submitted_at')
            ->latest('id')
            ->first();

        return view('academy.perma-widget', [
            'analysisResult' => $latestResponse ? $this->resultsEngine->forResponse($latestResponse) : null,
            'response' => $latestResponse,
        ]);
    }
}
