<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuestionnaireResponseRequest;
use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class QuestionnaireResponseController extends Controller
{
    public function show(OrganizationQuestionnaire $organizationQuestionnaire): View
    {
        /** @var User $user */
        $user = request()->user();

        $this->ensureAccessible($organizationQuestionnaire, $user);

        $organizationQuestionnaire->load([
            'questionnaire.categories' => fn ($query) => $query->orderBy('sort_order'),
            'questionnaire.categories.questions' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $response = QuestionnaireResponse::query()
            ->with('answers')
            ->where('organization_questionnaire_id', $organizationQuestionnaire->id)
            ->where('user_id', $user->id)
            ->first();

        return view('questionnaires.show', [
            'existingAnswers' => $response?->answers
                ->mapWithKeys(function ($answer): array {
                    return [
                        $answer->questionnaire_question_id => $answer->answer_list ?? $answer->answer,
                    ];
                })
                ->all() ?? [],
            'organizationQuestionnaire' => $organizationQuestionnaire,
            'response' => $response,
        ]);
    }

    public function store(
        SubmitQuestionnaireResponseRequest $request,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $this->ensureAccessible($organizationQuestionnaire, $user);

        $questions = $organizationQuestionnaire->questionnaire
            ->loadMissing('categories.questions')
            ->questions
            ->keyBy('id');

        DB::transaction(function () use ($organizationQuestionnaire, $questions, $request, $user): void {
            $response = QuestionnaireResponse::query()->updateOrCreate(
                [
                    'organization_questionnaire_id' => $organizationQuestionnaire->id,
                    'user_id' => $user->id,
                ],
                [
                    'submitted_at' => now(),
                ],
            );

            $response->answers()->delete();

            foreach ($questions as $questionId => $question) {
                $value = data_get($request->validated('answers'), (string) $questionId);

                if ($this->isEmptyAnswer($value)) {
                    continue;
                }

                $response->answers()->create([
                    'questionnaire_question_id' => $questionId,
                    'answer' => $this->normalizeScalarAnswer($question, $value),
                    'answer_list' => $this->normalizeListAnswer($question, $value),
                ]);
            }
        });

        return redirect()
            ->route('questionnaire-responses.show', $organizationQuestionnaire)
            ->with('status', 'Uw antwoorden zijn opgeslagen.');
    }

    protected function ensureAccessible(OrganizationQuestionnaire $organizationQuestionnaire, User $user): void
    {
        $organizationQuestionnaire->loadMissing('questionnaire');

        abort_unless($organizationQuestionnaire->org_id === $user->org_id, 403);
        abort_unless($organizationQuestionnaire->isAvailable(), 403);
    }

    protected function isEmptyAnswer(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->filter(fn ($item): bool => trim((string) $item) !== '')->isEmpty();
        }

        return trim((string) $value) === '';
    }

    protected function normalizeScalarAnswer(QuestionnaireQuestion $question, mixed $value): ?string
    {
        if ($question->type === QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE) {
            return null;
        }

        return is_array($value) ? null : trim((string) $value);
    }

    /**
     * @return array<int, string>|null
     */
    protected function normalizeListAnswer(QuestionnaireQuestion $question, mixed $value): ?array
    {
        if ($question->type !== QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE || ! is_array($value)) {
            return null;
        }

        return collect($value)
            ->map(fn ($item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
