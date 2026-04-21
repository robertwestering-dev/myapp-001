<?php

namespace App\Support\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;

class QuestionnaireLibraryExporter
{
    /**
     * @param  list<int>|null  $questionnaireIds
     * @return array{
     *     version: int,
     *     exported_at: string,
     *     questionnaires: array<int, array<string, mixed>>
     * }
     */
    public function export(?array $questionnaireIds = null): array
    {
        $query = Questionnaire::query()
            ->with([
                'categories.questions.displayConditionQuestion.category',
            ])
            ->orderBy('locale')
            ->orderBy('title');

        if ($questionnaireIds !== null && $questionnaireIds !== []) {
            $query->whereIn('id', $questionnaireIds);
        }

        return [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'questionnaires' => $query
                ->get()
                ->map(fn (Questionnaire $questionnaire): array => $this->mapQuestionnaire($questionnaire))
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapQuestionnaire(Questionnaire $questionnaire): array
    {
        return [
            'title' => $questionnaire->title,
            'description' => $questionnaire->description,
            'locale' => $questionnaire->locale,
            'is_active' => $questionnaire->is_active,
            'categories' => $questionnaire->categories
                ->map(fn (QuestionnaireCategory $category): array => $this->mapCategory($category))
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapCategory(QuestionnaireCategory $category): array
    {
        return [
            'title' => $category->title,
            'description' => $category->description,
            'sort_order' => $category->sort_order,
            'questions' => $category->questions
                ->map(fn (QuestionnaireQuestion $question): array => $this->mapQuestion($question))
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapQuestion(QuestionnaireQuestion $question): array
    {
        return [
            'locale' => $question->locale,
            'prompt' => $question->prompt,
            'help_text' => $question->help_text,
            'type' => $question->type,
            'options' => $question->options ?? [],
            'is_required' => $question->is_required,
            'sort_order' => $question->sort_order,
            'display_condition' => $this->mapDisplayCondition($question),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function mapDisplayCondition(QuestionnaireQuestion $question): ?array
    {
        if ($question->displayConditionQuestion === null) {
            return null;
        }

        return [
            'category_sort_order' => $question->displayConditionQuestion->category?->sort_order,
            'question_sort_order' => $question->displayConditionQuestion->sort_order,
            'locale' => $question->displayConditionQuestion->locale,
            'operator' => $question->display_condition_operator,
            'answer' => $question->display_condition_answer,
        ];
    }
}
