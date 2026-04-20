<?php

namespace App\Services;

use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SuccessfulLoginSummary
{
    public const SESSION_KEY = 'dashboard_login_summary';

    public function record(Request $request, User $user): void
    {
        $latestResponse = QuestionnaireResponse::query()
            ->with('organizationQuestionnaire.questionnaire')
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $request->session()->put(self::SESSION_KEY, [
            'name' => $user->first_name ?: $user->name,
            'previous_login_at' => $user->last_login_at?->toIso8601String(),
            'latest_questionnaire_title' => $latestResponse?->organizationQuestionnaire?->questionnaire?->title,
            'latest_questionnaire_submitted_at' => $latestResponse?->submitted_at?->toIso8601String(),
            'latest_questionnaire_is_stale' => $latestResponse?->submitted_at !== null
                && $latestResponse->submitted_at->toDateString() <= Carbon::today()->subMonthsNoOverflow(3)->toDateString(),
        ]);

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();
    }
}
