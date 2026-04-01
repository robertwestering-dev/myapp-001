<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuestionnaireResponseRequest;
use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Support\Questionnaires\QuestionnaireConditionEvaluator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionnaireResponseController extends Controller
{
    public function __construct(
        protected QuestionnaireConditionEvaluator $conditionEvaluator,
    ) {}

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

        $questions = $organizationQuestionnaire->questionnaire
            ->categories
            ->flatMap->questions
            ->values();
        $existingAnswers = $response?->answers
            ->mapWithKeys(function ($answer): array {
                return [
                    $answer->questionnaire_question_id => $answer->answer_list ?? $answer->answer,
                ];
            })
            ->all() ?? [];
        $visibleQuestionIds = $this->conditionEvaluator->visibleQuestionIds($questions, $existingAnswers);

        return view('questionnaires.show', [
            'existingAnswers' => $existingAnswers,
            'organizationQuestionnaire' => $organizationQuestionnaire,
            'response' => $response,
            'resumeUrl' => $response?->resume_token
                ? route('questionnaire-responses.resume', $response->resume_token)
                : null,
            'visibleQuestionIds' => $visibleQuestionIds,
        ]);
    }

    public function resume(string $token): RedirectResponse
    {
        /** @var User $user */
        $user = request()->user();

        $response = QuestionnaireResponse::query()
            ->where('user_id', $user->id)
            ->where('resume_token', $token)
            ->firstOrFail();

        $this->ensureAccessible($response->organizationQuestionnaire()->with('questionnaire')->firstOrFail(), $user);

        return redirect()->route('questionnaire-responses.show', $response->organization_questionnaire_id);
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
        $validatedAnswers = $request->validated('answers', []);
        $isDraft = $request->saveAsDraft();

        DB::transaction(function () use ($isDraft, $organizationQuestionnaire, $questions, $user, $validatedAnswers): void {
            $response = QuestionnaireResponse::query()->firstOrNew([
                'organization_questionnaire_id' => $organizationQuestionnaire->id,
                'user_id' => $user->id,
            ]);

            $response->last_saved_at = now();
            $response->submitted_at = $isDraft ? null : now();
            $response->resume_token ??= Str::lower(Str::random(40));
            $response->save();

            $response->answers()->delete();

            foreach ($questions as $questionId => $question) {
                $value = data_get($validatedAnswers, (string) $questionId);

                if (! $this->conditionEvaluator->isVisible($question, $validatedAnswers)) {
                    continue;
                }

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
            ->with('status', __($isDraft ? 'hermes.questionnaire.draft_saved_status' : 'hermes.questionnaire.saved_status'));
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
