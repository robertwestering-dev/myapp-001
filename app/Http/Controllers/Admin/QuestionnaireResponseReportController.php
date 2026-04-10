<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\ProvidesOrganizationOptions;
use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;
use App\Models\User;
use App\Services\SpotlightQuestionnaireService;
use App\Support\CsvExporter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionnaireResponseReportController extends Controller
{
    use ProvidesOrganizationOptions;

    public function __construct(private readonly SpotlightQuestionnaireService $spotlightService) {}

    public function index(Request $request): View
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaireId = $request->integer('questionnaire_id');
        $organizationId = $request->integer('org_id');
        $responseState = $this->normalizeResponseState($request->string('response_state')->value());
        $userId = $request->integer('user_id');

        $responses = $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId, $responseState)
            ->orderByDesc('submitted_at')
            ->orderByDesc('last_saved_at')
            ->orderByDesc('id')
            ->paginate(config('app.per_page'))
            ->withQueryString();

        return view('admin/questionnaire-responses/index', [
            'responses' => $responses,
            ...$this->reportFilterData($actor, $questionnaireId, $organizationId, $userId, $responseState),
        ]);
    }

    public function show(Request $request, QuestionnaireResponse $response): View
    {
        /** @var User $actor */
        $actor = $request->user();

        $response->loadMissing('organizationQuestionnaire:id,org_id');

        abort_unless($actor->canManageOrganization($response->organizationQuestionnaire->org_id), 403);

        $response->load([
            'user:id,name,email,org_id',
            'organizationQuestionnaire.organization:org_id,naam',
            'organizationQuestionnaire.questionnaire:id,title',
            'answers.question.category.questionnaire',
        ]);

        return view('admin/questionnaire-responses/show', [
            'response' => $response,
        ]);
    }

    public function stats(Request $request): View|RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaireId = $request->integer('questionnaire_id');
        $organizationId = $request->integer('org_id');
        $responseState = $this->normalizeResponseState($request->string('response_state')->value());
        $userId = $request->integer('user_id');

        $questionnaire = $this->requireQuestionnaireForExport($request, $questionnaireId);

        if ($questionnaire instanceof RedirectResponse) {
            return $questionnaire;
        }

        $questionnaire = $this->loadQuestionnaireStructure($questionnaire);

        $responseCount = $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId, $responseState, withDisplayRelations: false)->count();
        $statistics = $this->buildQuestionStatistics(
            $questionnaire,
            $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId, $responseState, withDisplayRelations: false),
        );

        return view('admin/questionnaire-responses/stats', [
            'questionnaire' => $questionnaire,
            'responseCount' => $responseCount,
            'statistics' => $statistics,
            ...$this->reportFilterData($actor, $questionnaireId, $organizationId, $userId, $responseState),
        ]);
    }

    public function export(Request $request): StreamedResponse|RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaireId = $request->integer('questionnaire_id');
        $organizationId = $request->integer('org_id');
        $userId = $request->integer('user_id');

        $questionnaire = $this->requireQuestionnaireForExport($request, $questionnaireId);

        if ($questionnaire instanceof RedirectResponse) {
            return $questionnaire;
        }

        $fileName = sprintf('questionnaire-responses-%d.csv', $questionnaireId);

        return response()->streamDownload(function () use ($actor, $organizationId, $questionnaireId, $userId): void {
            $csv = (new CsvExporter)->open();

            if (! $csv->isOpen()) {
                return;
            }

            $csv->writeRow([
                __('hermes.reports.csv.questionnaire'),
                __('hermes.reports.csv.organization'),
                __('hermes.reports.csv.user'),
                __('hermes.reports.csv.email'),
                __('hermes.reports.csv.submitted_at'),
                __('hermes.reports.csv.category'),
                __('hermes.reports.csv.question'),
                __('hermes.reports.csv.answer'),
            ]);

            $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId, 'completed')
                ->with([
                    'user:id,name,email,org_id',
                    'organizationQuestionnaire.organization:org_id,naam',
                    'organizationQuestionnaire.questionnaire:id,title',
                    'answers.question.category:id,questionnaire_id,title',
                ])
                ->orderByDesc('submitted_at')
                ->orderByDesc('last_saved_at')
                ->cursor()
                ->each(function (QuestionnaireResponse $response) use ($csv): void {
                    $answers = $response->answers;

                    if ($answers->isEmpty()) {
                        $csv->writeRow([
                            $response->organizationQuestionnaire->questionnaire->title,
                            $response->organizationQuestionnaire->organization->naam,
                            $response->user->name,
                            $response->user->email,
                            $response->submitted_at?->format('Y-m-d H:i:s') ?? '',
                            '',
                            '',
                            '',
                        ]);

                        return;
                    }

                    $answers->each(function ($answer) use ($csv, $response): void {
                        $csv->writeRow([
                            $response->organizationQuestionnaire->questionnaire->title,
                            $response->organizationQuestionnaire->organization->naam,
                            $response->user->name,
                            $response->user->email,
                            $response->submitted_at?->format('Y-m-d H:i:s') ?? '',
                            $answer->question->category->title,
                            $answer->question->prompt,
                            $answer->answer ?? implode(' | ', $answer->answer_list ?? []),
                        ]);
                    });
                });

            $csv->close();
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportSummary(Request $request): StreamedResponse|RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaireId = $request->integer('questionnaire_id');
        $organizationId = $request->integer('org_id');
        $userId = $request->integer('user_id');

        $questionnaire = $this->requireQuestionnaireForExport($request, $questionnaireId);

        if ($questionnaire instanceof RedirectResponse) {
            return $questionnaire;
        }

        $questionnaire = $this->loadQuestionnaireStructure($questionnaire);

        $questions = $questionnaire->categories
            ->flatMap(fn ($category) => $category->questions)
            ->values();

        $fileName = sprintf('questionnaire-responses-summary-%d.csv', $questionnaireId);

        return response()->streamDownload(function () use ($actor, $organizationId, $questionnaire, $questions, $questionnaireId, $userId): void {
            $csv = (new CsvExporter)->open();

            if (! $csv->isOpen()) {
                return;
            }

            $header = [
                __('hermes.reports.csv.questionnaire'),
                __('hermes.reports.csv.organization'),
                __('hermes.reports.csv.user'),
                __('hermes.reports.csv.email'),
                __('hermes.reports.csv.submitted_at'),
            ];

            foreach ($questions as $question) {
                $header[] = $question->prompt;
            }

            $csv->writeRow($header);

            $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId, 'completed')
                ->with([
                    'user:id,name,email,org_id',
                    'organizationQuestionnaire.organization:org_id,naam',
                    'organizationQuestionnaire.questionnaire:id,title',
                    'answers.question:id,prompt',
                ])
                ->orderByDesc('submitted_at')
                ->orderByDesc('last_saved_at')
                ->cursor()
                ->each(function (QuestionnaireResponse $response) use ($csv, $questionnaire, $questions): void {
                    $answersByQuestion = $response->answers
                        ->mapWithKeys(function ($answer): array {
                            return [
                                $answer->questionnaire_question_id => $answer->answer ?? implode(' | ', $answer->answer_list ?? []),
                            ];
                        });

                    $row = [
                        $questionnaire->title,
                        $response->organizationQuestionnaire->organization->naam,
                        $response->user->name,
                        $response->user->email,
                        $response->submitted_at?->format('Y-m-d H:i:s') ?? '',
                    ];

                    foreach ($questions as $question) {
                        $row[] = $answersByQuestion->get($question->id, '');
                    }

                    $csv->writeRow($row);
                });

            $csv->close();
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportStats(Request $request): StreamedResponse|RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaireId = $request->integer('questionnaire_id');
        $organizationId = $request->integer('org_id');
        $responseState = $this->normalizeResponseState($request->string('response_state')->value());
        $userId = $request->integer('user_id');

        $questionnaire = $this->requireQuestionnaireForExport($request, $questionnaireId);

        if ($questionnaire instanceof RedirectResponse) {
            return $questionnaire;
        }

        $questionnaire = $this->loadQuestionnaireStructure($questionnaire);
        $statistics = $this->buildQuestionStatistics(
            $questionnaire,
            $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId, $responseState, withDisplayRelations: false),
        );
        $fileName = sprintf('questionnaire-responses-stats-%d.csv', $questionnaireId);

        return response()->streamDownload(function () use ($questionnaire, $statistics): void {
            $csv = (new CsvExporter)->open();

            if (! $csv->isOpen()) {
                return;
            }

            $csv->writeRow([
                __('hermes.reports.csv.questionnaire'),
                __('hermes.reports.csv.category'),
                __('hermes.reports.csv.question'),
                __('hermes.reports.csv.question_type'),
                __('hermes.reports.csv.answered_total'),
                __('hermes.reports.csv.option'),
                __('hermes.reports.csv.count'),
            ]);

            $statistics->each(function (array $categoryStats) use ($csv, $questionnaire): void {
                foreach ($categoryStats['questions'] as $questionStats) {
                    if ($questionStats['options']->isNotEmpty()) {
                        $questionStats['options']->each(function (array $optionStats) use ($categoryStats, $csv, $questionStats, $questionnaire): void {
                            $csv->writeRow([
                                $questionnaire->title,
                                $categoryStats['category']->title,
                                $questionStats['question']->prompt,
                                $questionStats['type_label'],
                                $questionStats['answered_count'],
                                $optionStats['label'],
                                $optionStats['count'],
                            ]);
                        });

                        continue;
                    }

                    $csv->writeRow([
                        $questionnaire->title,
                        $categoryStats['category']->title,
                        $questionStats['question']->prompt,
                        $questionStats['type_label'],
                        $questionStats['answered_count'],
                        '',
                        '',
                    ]);
                }
            });

            $csv->close();
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Builds question statistics using chunked processing to avoid loading all
     * responses into memory at once.
     *
     * @return Collection<int, array{
     *     category: QuestionnaireCategory,
     *     questions: Collection<int, array{
     *         answered_count: int,
     *         answered_percentage: int,
     *         latest_answers: Collection<int, string>,
     *         options: Collection<int, array{count: int, label: string, percentage: int}>,
     *         question: QuestionnaireQuestion,
     *         type_label: string
     *     }>
     * }>
     */
    protected function buildQuestionStatistics(
        Questionnaire $questionnaire,
        Builder $responseQuery
    ): Collection {
        /** @var array<int, int> $answeredCounts */
        $answeredCounts = [];
        /** @var array<int, array<string, int>> $optionCounts */
        $optionCounts = [];
        /** @var array<int, array<int, string>> $latestAnswers */
        $latestAnswers = [];
        $responseCount = 0;

        $responseQuery
            ->with(['answers.question:id,type,options'])
            ->chunk(200, function (Collection $responses) use (
                &$answeredCounts,
                &$latestAnswers,
                &$optionCounts,
                &$responseCount
            ): void {
                $responseCount += $responses->count();

                foreach ($responses as $response) {
                    foreach ($response->answers as $answer) {
                        $qid = $answer->questionnaire_question_id;
                        $answeredCounts[$qid] = ($answeredCounts[$qid] ?? 0) + 1;

                        $formatted = $this->formatAnswerValue($answer);

                        if ($formatted !== '' && count($latestAnswers[$qid] ?? []) < 5) {
                            $latestAnswers[$qid] ??= [];
                            if (! in_array($formatted, $latestAnswers[$qid], true)) {
                                $latestAnswers[$qid][] = $formatted;
                            }
                        }

                        $question = $answer->question;

                        if ($question === null) {
                            continue;
                        }

                        if (in_array($question->type, [
                            QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
                            QuestionnaireQuestion::TYPE_LIKERT_SCALE,
                        ], true) && $answer->answer !== null) {
                            $optionCounts[$qid][$answer->answer] = ($optionCounts[$qid][$answer->answer] ?? 0) + 1;
                        } elseif ($question->type === QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE) {
                            foreach ($answer->answer_list ?? [] as $option) {
                                $optionCounts[$qid][$option] = ($optionCounts[$qid][$option] ?? 0) + 1;
                            }
                        }
                    }
                }
            });

        $denominator = max($responseCount, 1);

        return $questionnaire->categories
            ->map(function (QuestionnaireCategory $category) use ($answeredCounts, $denominator, $latestAnswers, $optionCounts): array {
                $questions = $category->questions->map(function (QuestionnaireQuestion $question) use ($answeredCounts, $denominator, $latestAnswers, $optionCounts): array {
                    $qid = $question->id;
                    $answeredCount = $answeredCounts[$qid] ?? 0;
                    $options = collect();

                    if (in_array($question->type, [
                        QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
                        QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE,
                        QuestionnaireQuestion::TYPE_LIKERT_SCALE,
                    ], true)) {
                        $options = collect($question->options ?? [])
                            ->map(function (string $option) use ($qid, $optionCounts, $denominator): array {
                                $count = $optionCounts[$qid][$option] ?? 0;

                                return [
                                    'count' => $count,
                                    'label' => $option,
                                    'percentage' => (int) round(($count / $denominator) * 100),
                                ];
                            });
                    }

                    return [
                        'answered_count' => $answeredCount,
                        'answered_percentage' => (int) round(($answeredCount / $denominator) * 100),
                        'latest_answers' => collect($latestAnswers[$qid] ?? [])->values(),
                        'options' => $options,
                        'question' => $question,
                        'type_label' => QuestionnaireQuestion::typeLabels()[$question->type] ?? $question->type,
                    ];
                });

                return [
                    'category' => $category,
                    'questions' => $questions,
                ];
            })
            ->values();
    }

    protected function formatAnswerValue(QuestionnaireResponseAnswer $answer): string
    {
        return $answer->answer ?? implode(' | ', $answer->answer_list ?? []);
    }

    /**
     * @return array{
     *     activeFilters: array{org_id?: int, questionnaire_id?: int, response_state?: string, user_id?: int},
     *     organizations: array<int, string>,
     *     orgId: int,
     *     questionnaireId: int,
     *     questionnaires: array<int, string>,
     *     responseState: string,
     *     selectedUserId: int,
     *     spotlightQuestionnaires: Collection<int, Questionnaire>,
     *     users: array<int, string>
     * }
     */
    protected function reportFilterData(
        User $actor,
        int $questionnaireId,
        int $organizationId,
        int $userId,
        string $responseState
    ): array {
        return [
            'activeFilters' => $this->activeFilters($questionnaireId, $organizationId, $userId, $responseState),
            'organizations' => $this->organizationOptions($actor),
            'orgId' => $organizationId,
            'questionnaireId' => $questionnaireId,
            'questionnaires' => $this->questionnaireOptions($actor),
            'responseState' => $responseState,
            'selectedUserId' => $userId,
            'spotlightQuestionnaires' => $this->spotlightService->getForFilters(),
            'users' => $this->userOptions($actor),
        ];
    }

    /**
     * @return array{org_id?: int, questionnaire_id?: int, response_state?: string, user_id?: int}
     */
    protected function activeFilters(
        int $questionnaireId,
        int $organizationId,
        int $userId,
        string $responseState
    ): array {
        return array_filter([
            'questionnaire_id' => $questionnaireId > 0 ? $questionnaireId : null,
            'org_id' => $organizationId > 0 ? $organizationId : null,
            'user_id' => $userId > 0 ? $userId : null,
            'response_state' => $responseState !== 'completed' ? $responseState : null,
        ], fn (mixed $value): bool => $value !== null);
    }

    protected function filteredResponsesQuery(
        User $actor,
        int $questionnaireId,
        int $organizationId,
        int $userId,
        string $responseState,
        bool $withDisplayRelations = true
    ): Builder {
        $query = QuestionnaireResponse::query()
            ->select(['id', 'organization_questionnaire_id', 'user_id', 'submitted_at', 'last_saved_at', 'created_at']);

        if ($withDisplayRelations) {
            $query->with([
                'user:id,name,email,org_id',
                'organizationQuestionnaire.organization:org_id,naam',
                'organizationQuestionnaire.questionnaire:id,title',
            ]);
        }

        return $query
            ->when($responseState === 'completed', function (Builder $query): void {
                $query->whereNotNull('submitted_at');
            })
            ->when($responseState === 'draft', function (Builder $query): void {
                $query->whereNull('submitted_at');
            })
            ->when(
                ! $actor->isAdmin() || $questionnaireId > 0 || $organizationId > 0,
                function (Builder $query) use ($actor, $questionnaireId, $organizationId): void {
                    $query->whereHas('organizationQuestionnaire', function (Builder $query) use ($actor, $questionnaireId, $organizationId): void {
                        $query
                            ->when(! $actor->isAdmin(), fn (Builder $q) => $q->where('org_id', $actor->org_id))
                            ->when($questionnaireId > 0, fn (Builder $q) => $q->where('questionnaire_id', $questionnaireId))
                            ->when($organizationId > 0, fn (Builder $q) => $q->where('org_id', $organizationId));
                    });
                }
            )
            ->when($userId > 0, function (Builder $query) use ($userId): void {
                $query->where('user_id', $userId);
            });
    }

    protected function requireQuestionnaireForExport(
        Request $request,
        int $questionnaireId
    ): Questionnaire|RedirectResponse {
        if ($questionnaireId <= 0) {
            return redirect()
                ->route('admin.questionnaire-responses.index', $request->query())
                ->withErrors(['questionnaire_id' => __('hermes.reports.choose_questionnaire_first')]);
        }

        return Questionnaire::query()->findOrFail($questionnaireId);
    }

    protected function loadQuestionnaireStructure(Questionnaire $questionnaire): Questionnaire
    {
        $questionnaire->load([
            'categories' => fn ($query) => $query->orderBy('sort_order'),
            'categories.questions' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return $questionnaire;
    }

    protected function normalizeResponseState(?string $responseState): string
    {
        return in_array($responseState, ['all', 'completed', 'draft'], true)
            ? $responseState
            : 'completed';
    }

    /**
     * @return array<int, string>
     */
    protected function questionnaireOptions(User $actor): array
    {
        return Questionnaire::query()
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->whereHas('organizationQuestionnaires', function (Builder $query) use ($actor): void {
                    $query->where('org_id', $actor->org_id);
                });
            })
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    /**
     * Returns at most 500 users to keep the filter dropdown manageable.
     *
     * @return array<int, string>
     */
    protected function userOptions(User $actor): array
    {
        return User::query()
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (User $user): array => [$user->id => "{$user->name} ({$user->email})"])
            ->all();
    }
}
