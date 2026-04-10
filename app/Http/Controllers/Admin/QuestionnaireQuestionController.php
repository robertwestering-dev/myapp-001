<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionnaireQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionnaireQuestionRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionnaireQuestionController extends Controller
{
    public function create(Request $request, Questionnaire $questionnaire): View
    {
        $this->authorize('manage', Questionnaire::class);

        return view('admin.questionnaire-questions.form', [
            'questionnaire' => $questionnaire->load('categories'),
            'title' => __('hermes.admin.form_titles.new_questionnaire_question'),
            'intro' => 'Voeg een nieuwe vraag toe aan een categorie binnen deze questionnaire.',
            'submitLabel' => 'Vraag opslaan',
            'question' => new QuestionnaireQuestion([
                'questionnaire_category_id' => $request->integer('category'),
                'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
                'sort_order' => 0,
            ]),
            'conditionQuestionOptions' => $this->conditionQuestionOptions($questionnaire),
            'conditionOperators' => QuestionnaireQuestion::displayConditionOperatorLabels(),
            'questionTypes' => QuestionnaireQuestion::typeLabels(),
            'isEditing' => false,
        ]);
    }

    public function store(StoreQuestionnaireQuestionRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        $this->authorize('manage', Questionnaire::class);

        $attributes = $request->validated();
        $attributes['options'] = $this->normalizeOptions($attributes['type'], $attributes['options'] ?? null);
        $attributes['display_condition_answer'] = $this->normalizeConditionAnswer(
            $attributes['display_condition_operator'] ?? null,
            $attributes['display_condition_answer'] ?? null,
        );
        $attributes['is_required'] = $request->boolean('is_required');
        $attributes['locale'] = $questionnaire->locale;

        QuestionnaireQuestion::create($attributes);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaire_questions.created'));
    }

    public function edit(Request $request, Questionnaire $questionnaire, QuestionnaireQuestion $question): View
    {
        $this->authorize('manage', Questionnaire::class);
        abort_unless($question->category()->value('questionnaire_id') === $questionnaire->id, 404);

        return view('admin.questionnaire-questions.form', [
            'questionnaire' => $questionnaire->load('categories'),
            'title' => __('hermes.admin.form_titles.edit_questionnaire_question'),
            'intro' => 'Werk deze vraag en het antwoordtype bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'question' => $question,
            'conditionQuestionOptions' => $this->conditionQuestionOptions($questionnaire, $question),
            'conditionOperators' => QuestionnaireQuestion::displayConditionOperatorLabels(),
            'questionTypes' => QuestionnaireQuestion::typeLabels(),
            'isEditing' => true,
        ]);
    }

    public function update(
        UpdateQuestionnaireQuestionRequest $request,
        Questionnaire $questionnaire,
        QuestionnaireQuestion $question
    ): RedirectResponse {
        $this->authorize('manage', Questionnaire::class);
        abort_unless($question->category()->value('questionnaire_id') === $questionnaire->id, 404);

        $attributes = $request->validated();
        $attributes['options'] = $this->normalizeOptions($attributes['type'], $attributes['options'] ?? null);
        $attributes['display_condition_answer'] = $this->normalizeConditionAnswer(
            $attributes['display_condition_operator'] ?? null,
            $attributes['display_condition_answer'] ?? null,
        );
        $attributes['is_required'] = $request->boolean('is_required');
        $attributes['locale'] = $questionnaire->locale;

        $question->update($attributes);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaire_questions.updated'));
    }

    public function destroy(Request $request, Questionnaire $questionnaire, QuestionnaireQuestion $question): RedirectResponse
    {
        $this->authorize('manage', Questionnaire::class);
        abort_unless($question->category()->value('questionnaire_id') === $questionnaire->id, 404);

        $question->delete();

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaire_questions.deleted'));
    }

    /**
     * @return array<int, string>|null
     */
    protected function normalizeOptions(string $type, ?string $options): ?array
    {
        if (! in_array($type, [
            QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
            QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE,
            QuestionnaireQuestion::TYPE_LIKERT_SCALE,
        ], true)) {
            return null;
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $options))
            ->map(fn (?string $option): string => trim((string) $option))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function conditionQuestionOptions(
        Questionnaire $questionnaire,
        ?QuestionnaireQuestion $currentQuestion = null
    ): array {
        return $questionnaire->loadMissing('categories.questions')->categories
            ->flatMap(function ($category) use ($currentQuestion) {
                return $category->questions
                    ->reject(fn (QuestionnaireQuestion $question): bool => $question->is($currentQuestion))
                    ->sortBy('sort_order')
                    ->mapWithKeys(fn (QuestionnaireQuestion $question): array => [
                        $question->id => $category->title.' -> '.$question->prompt,
                    ]);
            })
            ->all();
    }

    /**
     * @return array<int, string>|null
     */
    protected function normalizeConditionAnswer(?string $operator, ?string $value): ?array
    {
        if ($operator === null || in_array($operator, [
            QuestionnaireQuestion::DISPLAY_CONDITION_ANSWERED,
            QuestionnaireQuestion::DISPLAY_CONDITION_NOT_ANSWERED,
        ], true)) {
            return null;
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn (?string $item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
