<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionnaireQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionnaireQuestionRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class QuestionnaireQuestionController extends Controller
{
    public function create(Questionnaire $questionnaire): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.questionnaire-questions.form', [
            'questionnaire' => $questionnaire->load('categories'),
            'title' => 'Nieuwe vraag',
            'intro' => 'Voeg een nieuwe vraag toe aan een categorie binnen deze questionnaire.',
            'submitLabel' => 'Vraag opslaan',
            'question' => new QuestionnaireQuestion([
                'questionnaire_category_id' => request()->integer('category'),
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
        abort_unless($request->user()?->isAdmin(), 403);

        $attributes = $request->validated();
        $attributes['options'] = $this->normalizeOptions($attributes['type'], $attributes['options'] ?? null);
        $attributes['display_condition_answer'] = $this->normalizeConditionAnswer(
            $attributes['display_condition_operator'] ?? null,
            $attributes['display_condition_answer'] ?? null,
        );
        $attributes['is_required'] = $request->boolean('is_required');

        QuestionnaireQuestion::create($attributes);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Vraag succesvol toegevoegd.');
    }

    public function edit(Questionnaire $questionnaire, QuestionnaireQuestion $question): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);
        abort_unless($question->category->questionnaire_id === $questionnaire->id, 404);

        return view('admin.questionnaire-questions.form', [
            'questionnaire' => $questionnaire->load('categories'),
            'title' => 'Vraag wijzigen',
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
        abort_unless($request->user()?->isAdmin(), 403);
        abort_unless($question->category->questionnaire_id === $questionnaire->id, 404);

        $attributes = $request->validated();
        $attributes['options'] = $this->normalizeOptions($attributes['type'], $attributes['options'] ?? null);
        $attributes['display_condition_answer'] = $this->normalizeConditionAnswer(
            $attributes['display_condition_operator'] ?? null,
            $attributes['display_condition_answer'] ?? null,
        );
        $attributes['is_required'] = $request->boolean('is_required');

        $question->update($attributes);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Vraag succesvol bijgewerkt.');
    }

    public function destroy(Questionnaire $questionnaire, QuestionnaireQuestion $question): RedirectResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);
        abort_unless($question->category->questionnaire_id === $questionnaire->id, 404);

        $question->delete();

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Vraag succesvol verwijderd.');
    }

    /**
     * @return array<int, string>|null
     */
    protected function normalizeOptions(string $type, ?string $options): ?array
    {
        if (! in_array($type, [
            QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
            QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE,
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
