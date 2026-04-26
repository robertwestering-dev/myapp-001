<?php

namespace App\Http\Controllers;

use App\Concerns\NormalizesAnswers;
use App\Http\Requests\SubmitQuestionnaireResponseRequest;
use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Support\Questionnaires\AvailableQuestionnaireCatalog;
use App\Support\Questionnaires\LocalizedQuestionnaireContent;
use App\Support\Questionnaires\QuestionnaireConditionEvaluator;
use App\Support\Questionnaires\Results\QuestionnaireResultsEngine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionnaireResponseController extends Controller
{
    use NormalizesAnswers;

    public function __construct(
        protected QuestionnaireConditionEvaluator $conditionEvaluator,
        protected AvailableQuestionnaireCatalog $catalog,
        protected LocalizedQuestionnaireContent $localizedContent,
        protected QuestionnaireResultsEngine $resultsEngine,
    ) {}

    public function show(Request $request, OrganizationQuestionnaire $organizationQuestionnaire): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $localeContext = $this->catalog->localeContext($request, $user);

        $this->ensureAccessible($request, $organizationQuestionnaire, $user);

        $organizationQuestionnaire->load('questionnaire');
        $this->localizedContent->apply($organizationQuestionnaire->questionnaire, $localeContext['locale']);

        $response = QuestionnaireResponse::query()
            ->with('answers')
            ->where('organization_questionnaire_id', $organizationQuestionnaire->id)
            ->where('user_id', $user->id)
            ->whereNull('submitted_at')
            ->latest('updated_at')
            ->first();

        if ($response === null && ! $user->isProUser() && $this->hasCompletedResponse($organizationQuestionnaire, $user)) {
            return redirect()
                ->route('questionnaires.index')
                ->with('pro_required_modal', true);
        }

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
        $initialCategoryId = $response?->current_questionnaire_category_id;

        return view('questionnaires.show', [
            'analysisResult' => null,
            'existingAnswers' => $existingAnswers,
            'initialCategoryId' => $initialCategoryId,
            'activeQuestionnaireLocale' => $localeContext['locale'],
            'activeQuestionnaireLocaleLabel' => $localeContext['label'],
            'activeQuestionnaireLocaleSource' => $localeContext['source'],
            'organizationQuestionnaire' => $organizationQuestionnaire,
            'response' => $response,
            'resumeUrl' => $response?->isDraft() && $response?->resume_token
                ? route('questionnaire-responses.resume', $response->resume_token)
                : null,
            'visibleQuestionIds' => $visibleQuestionIds,
        ]);
    }

    public function resume(Request $request, string $token): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $response = QuestionnaireResponse::query()
            ->where('user_id', $user->id)
            ->where('resume_token', $token)
            ->whereNull('submitted_at')
            ->firstOrFail();

        $this->ensureAccessible($request, $response->organizationQuestionnaire()->with('questionnaire')->firstOrFail(), $user);

        return redirect()->route('questionnaire-responses.show', $response->organization_questionnaire_id);
    }

    public function results(Request $request, QuestionnaireResponse $response): View
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($response->user_id === $user->id, 403);
        abort_unless($response->submitted_at !== null, 404);

        $response->loadMissing('organizationQuestionnaire.questionnaire');

        return view('questionnaires.results', [
            'analysisResult' => $this->resultsEngine->forResponse($response),
            'organizationQuestionnaire' => $response->organizationQuestionnaire,
            'response' => $response,
        ]);
    }

    public function store(
        SubmitQuestionnaireResponseRequest $request,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): RedirectResponse|JsonResponse {
        /** @var User $user */
        $user = $request->user();

        $this->ensureAccessible($request, $organizationQuestionnaire, $user);

        $localeContext = $this->catalog->localeContext($request, $user);
        $questionnaire = $this->localizedContent->apply(
            $organizationQuestionnaire->questionnaire,
            $localeContext['locale'],
        );
        $questions = $questionnaire->categories
            ->flatMap->questions
            ->keyBy('id');
        $categories = $questionnaire->categories->keyBy('id');
        $validatedAnswers = $request->validated('answers', []);
        $isDraft = $request->saveAsDraft();
        $currentCategory = $this->resolveCurrentCategory($request->integer('current_category_id'), $categories);
        $visibleQuestions = collect($this->conditionEvaluator->visibleQuestions($questions->values(), $validatedAnswers))
            ->keyBy('id');
        $existingResponse = QuestionnaireResponse::query()
            ->where('organization_questionnaire_id', $organizationQuestionnaire->id)
            ->where('user_id', $user->id)
            ->whereNull('submitted_at')
            ->latest('updated_at')
            ->first();

        if ($existingResponse === null && ! $user->isProUser() && $this->hasCompletedResponse($organizationQuestionnaire, $user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('hermes.questionnaires.pro_required_message'),
                ], 403);
            }

            return redirect()
                ->route('questionnaires.index')
                ->with('pro_required_modal', true);
        }

        $response = DB::transaction(function () use (
            $currentCategory,
            $existingResponse,
            $isDraft,
            $organizationQuestionnaire,
            $questions,
            $user,
            $validatedAnswers,
            $visibleQuestions,
        ): QuestionnaireResponse {
            $response = $existingResponse ?? (new QuestionnaireResponse)->forceFill([
                'organization_questionnaire_id' => $organizationQuestionnaire->id,
                'user_id' => $user->id,
            ]);

            $response->current_questionnaire_category_id = $currentCategory?->id;
            $response->last_saved_at = now();
            $response->submitted_at = $isDraft ? null : now();
            $response->resume_token ??= Str::lower(Str::random(40));
            $response->save();

            $response->answers()->delete();

            foreach ($questions as $questionId => $question) {
                $value = data_get($validatedAnswers, (string) $questionId);

                if (! $visibleQuestions->has($question->id)) {
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

            return $response->fresh(['currentQuestionnaireCategory']);
        });

        if ($request->isAutosave()) {
            return response()->json([
                'last_saved_at' => $response->last_saved_at?->format('d-m-Y H:i'),
                'message' => __('hermes.questionnaire.autosave_saved_status', [
                    'datetime' => $response->last_saved_at?->format('d-m-Y H:i'),
                ]),
                'step_label' => $response->currentQuestionnaireCategory?->title,
            ]);
        }

        if (! $isDraft) {
            $this->resultsEngine->analyzeAndStore($response);
        }

        return redirect()
            ->route($isDraft ? 'questionnaire-responses.show' : 'questionnaire-responses.results', $isDraft ? $organizationQuestionnaire : $response)
            ->with('status', __($isDraft ? 'hermes.questionnaire.draft_saved_status' : 'hermes.questionnaire.saved_status'));
    }

    protected function ensureAccessible(Request $request, OrganizationQuestionnaire $organizationQuestionnaire, User $user): void
    {
        $organizationQuestionnaire->loadMissing('questionnaire');

        abort_unless($organizationQuestionnaire->org_id === $user->org_id, 403);
        abort_unless($organizationQuestionnaire->isAvailable(), 403);
    }

    protected function hasCompletedResponse(OrganizationQuestionnaire $organizationQuestionnaire, User $user): bool
    {
        return QuestionnaireResponse::query()
            ->where('organization_questionnaire_id', $organizationQuestionnaire->id)
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    /**
     * @param  Collection<int, QuestionnaireCategory>  $categories
     */
    protected function resolveCurrentCategory(?int $currentCategoryId, Collection $categories): ?QuestionnaireCategory
    {
        if ($currentCategoryId !== null && $categories->has($currentCategoryId)) {
            return $categories->get($currentCategoryId);
        }

        return $categories->first();
    }
}
