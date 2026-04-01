<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionnaireResponseReportController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaireId = $request->integer('questionnaire_id');
        $organizationId = $request->integer('org_id');
        $userId = $request->integer('user_id');

        $responses = $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId)
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin/questionnaire-responses/index', [
            'responses' => $responses,
            ...$this->reportFilterData($actor, $questionnaireId, $organizationId, $userId),
        ]);
    }

    public function show(QuestionnaireResponse $response): View
    {
        /** @var User $actor */
        $actor = request()->user();

        $response->load([
            'user:id,name,email,org_id',
            'organizationQuestionnaire.organization:org_id,naam',
            'organizationQuestionnaire.questionnaire:id,title',
            'answers.question.category.questionnaire',
        ]);

        abort_unless($actor->canManageOrganization($response->organizationQuestionnaire->org_id), 403);

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
        $userId = $request->integer('user_id');

        $questionnaire = $this->requireQuestionnaireForExport($request, $questionnaireId);

        if ($questionnaire instanceof RedirectResponse) {
            return $questionnaire;
        }

        $questionnaire = $this->loadQuestionnaireStructure($questionnaire);
        $responses = $this->responsesForStatistics($actor, $questionnaireId, $organizationId, $userId);

        $statistics = $this->buildQuestionStatistics($questionnaire, $responses);

        return view('admin/questionnaire-responses/stats', [
            'questionnaire' => $questionnaire,
            'responseCount' => $responses->count(),
            'statistics' => $statistics,
            ...$this->reportFilterData($actor, $questionnaireId, $organizationId, $userId),
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
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                __('hermes.reports.csv.questionnaire'),
                __('hermes.reports.csv.organization'),
                __('hermes.reports.csv.user'),
                __('hermes.reports.csv.email'),
                __('hermes.reports.csv.submitted_at'),
                __('hermes.reports.csv.category'),
                __('hermes.reports.csv.question'),
                __('hermes.reports.csv.answer'),
            ]);

            $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId)
                ->with([
                    'user:id,name,email,org_id',
                    'organizationQuestionnaire.organization:org_id,naam',
                    'organizationQuestionnaire.questionnaire:id,title',
                    'answers.question.category:id,questionnaire_id,title',
                ])
                ->orderByDesc('submitted_at')
                ->cursor()
                ->each(function (QuestionnaireResponse $response) use ($handle): void {
                    $answers = $response->answers;

                    if ($answers->isEmpty()) {
                        fputcsv($handle, [
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

                    $answers->each(function ($answer) use ($handle, $response): void {
                        fputcsv($handle, [
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

            fclose($handle);
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
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
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

            fputcsv($handle, $header);

            $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId)
                ->with([
                    'user:id,name,email,org_id',
                    'organizationQuestionnaire.organization:org_id,naam',
                    'organizationQuestionnaire.questionnaire:id,title',
                    'answers.question:id,questionnaire_question_id,prompt',
                ])
                ->orderByDesc('submitted_at')
                ->cursor()
                ->each(function (QuestionnaireResponse $response) use ($handle, $questionnaire, $questions): void {
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

                    fputcsv($handle, $row);
                });

            fclose($handle);
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
        $userId = $request->integer('user_id');

        $questionnaire = $this->requireQuestionnaireForExport($request, $questionnaireId);

        if ($questionnaire instanceof RedirectResponse) {
            return $questionnaire;
        }

        $questionnaire = $this->loadQuestionnaireStructure($questionnaire);
        $responses = $this->responsesForStatistics($actor, $questionnaireId, $organizationId, $userId);

        $statistics = $this->buildQuestionStatistics($questionnaire, $responses);
        $fileName = sprintf('questionnaire-responses-stats-%d.csv', $questionnaireId);

        return response()->streamDownload(function () use ($questionnaire, $statistics): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, [
                __('hermes.reports.csv.questionnaire'),
                __('hermes.reports.csv.category'),
                __('hermes.reports.csv.question'),
                __('hermes.reports.csv.question_type'),
                __('hermes.reports.csv.answered_total'),
                __('hermes.reports.csv.option'),
                __('hermes.reports.csv.count'),
            ]);

            $statistics->each(function (array $categoryStats) use ($handle, $questionnaire): void {
                foreach ($categoryStats['questions'] as $questionStats) {
                    if ($questionStats['options']->isNotEmpty()) {
                        $questionStats['options']->each(function (array $optionStats) use ($categoryStats, $handle, $questionStats, $questionnaire): void {
                            fputcsv($handle, [
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

                    fputcsv($handle, [
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

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
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
        Collection $responses
    ): Collection {
        $answers = $responses->flatMap->answers;
        $responseCount = $responses->count();

        return $questionnaire->categories
            ->map(function ($category) use ($answers, $responseCount): array {
                $questions = $category->questions->map(function (QuestionnaireQuestion $question) use ($answers, $responseCount): array {
                    $questionAnswers = $answers
                        ->where('questionnaire_question_id', $question->id)
                        ->values();

                    $answeredCount = $questionAnswers->count();
                    $denominator = max($responseCount, 1);
                    $options = collect();

                    if (in_array($question->type, [
                        QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
                        QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE,
                    ], true)) {
                        $options = collect($question->options ?? [])
                            ->map(function (string $option) use ($question, $questionAnswers, $denominator): array {
                                $count = $questionAnswers->filter(function ($answer) use ($option, $question): bool {
                                    if ($question->type === QuestionnaireQuestion::TYPE_SINGLE_CHOICE) {
                                        return $answer->answer === $option;
                                    }

                                    return in_array($option, $answer->answer_list ?? [], true);
                                })->count();

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
                        'latest_answers' => $questionAnswers
                            ->map(fn ($answer): string => $this->formatAnswerValue($answer))
                            ->filter()
                            ->unique()
                            ->take(5)
                            ->values(),
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
     *     organizations: array<int, string>,
     *     orgId: int,
     *     questionnaireId: int,
     *     questionnaires: array<int, string>,
     *     selectedUserId: int,
     *     spotlightQuestionnaires: Collection<int, Questionnaire>,
     *     users: array<int, string>
     * }
     */
    protected function reportFilterData(
        User $actor,
        int $questionnaireId,
        int $organizationId,
        int $userId
    ): array {
        return [
            'organizations' => $this->organizationOptions($actor),
            'orgId' => $organizationId,
            'questionnaireId' => $questionnaireId,
            'questionnaires' => $this->questionnaireOptions(),
            'selectedUserId' => $userId,
            'spotlightQuestionnaires' => $this->spotlightQuestionnaires(),
            'users' => $this->userOptions($actor),
        ];
    }

    protected function filteredResponsesQuery(
        User $actor,
        int $questionnaireId,
        int $organizationId,
        int $userId
    ): Builder {
        return QuestionnaireResponse::query()
            ->select(['id', 'organization_questionnaire_id', 'user_id', 'submitted_at', 'created_at'])
            ->whereNotNull('submitted_at')
            ->with([
                'user:id,name,email,org_id',
                'organizationQuestionnaire.organization:org_id,naam',
                'organizationQuestionnaire.questionnaire:id,title',
            ])
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->whereHas('organizationQuestionnaire', function (Builder $query) use ($actor): void {
                    $query->where('org_id', $actor->org_id);
                });
            })
            ->when($questionnaireId > 0, function (Builder $query) use ($questionnaireId): void {
                $query->whereHas('organizationQuestionnaire', function (Builder $query) use ($questionnaireId): void {
                    $query->where('questionnaire_id', $questionnaireId);
                });
            })
            ->when($organizationId > 0, function (Builder $query) use ($organizationId): void {
                $query->whereHas('organizationQuestionnaire', function (Builder $query) use ($organizationId): void {
                    $query->where('org_id', $organizationId);
                });
            })
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
            'categories.questions' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return $questionnaire;
    }

    /**
     * @return Collection<int, QuestionnaireResponse>
     */
    protected function responsesForStatistics(
        User $actor,
        int $questionnaireId,
        int $organizationId,
        int $userId
    ): Collection {
        return $this->filteredResponsesQuery($actor, $questionnaireId, $organizationId, $userId)
            ->with([
                'answers.question:id,questionnaire_category_id,prompt,type,options',
                'answers.question.category:id,questionnaire_id,title',
            ])
            ->get();
    }

    /**
     * @return array<int, string>
     */
    protected function organizationOptions(User $actor): array
    {
        return Organization::query()
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('naam')
            ->pluck('naam', 'org_id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function questionnaireOptions(): array
    {
        return Questionnaire::query()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->all();
    }

    /**
     * @return Collection<int, Questionnaire>
     */
    protected function spotlightQuestionnaires(): Collection
    {
        $spotlightTitles = [
            SyncAdaptabilityAceQuestionnaire::TITLE,
            SyncDigitalResilienceQuickScanQuestionnaire::TITLE,
        ];

        $questionnaires = Questionnaire::query()
            ->whereIn('title', $spotlightTitles)
            ->get(['id', 'title', 'description']);

        return collect($spotlightTitles)
            ->map(fn (string $title): ?Questionnaire => $questionnaires->firstWhere('title', $title))
            ->filter()
            ->values();
    }

    /**
     * @return array<int, string>
     */
    protected function userOptions(User $actor): array
    {
        return User::query()
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (User $user): array => [$user->id => "{$user->name} ({$user->email})"])
            ->all();
    }
}
