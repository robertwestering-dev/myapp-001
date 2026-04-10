<?php

namespace App\Support\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QuestionnaireLibraryImporter
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{questionnaires: int}
     */
    public function import(array $payload): array
    {
        $this->guardPayload($payload);

        $importedCount = 0;

        DB::transaction(function () use ($payload, &$importedCount): void {
            foreach ($payload['questionnaires'] as $questionnaireDefinition) {
                $this->importQuestionnaire($questionnaireDefinition);
                $importedCount++;
            }
        });

        return [
            'questionnaires' => $importedCount,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function guardPayload(array $payload): void
    {
        if (! isset($payload['questionnaires']) || ! is_array($payload['questionnaires'])) {
            throw new InvalidArgumentException('Het importbestand bevat geen geldige questionnaires-array.');
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    protected function importQuestionnaire(array $definition): void
    {
        $questionnaire = Questionnaire::query()->updateOrCreate(
            [
                'title' => (string) ($definition['title'] ?? ''),
                'locale' => (string) ($definition['locale'] ?? ''),
            ],
            [
                'description' => $definition['description'] ?: null,
                'is_active' => (bool) ($definition['is_active'] ?? true),
            ],
        );

        $categoryIdsToKeep = [];
        $questionsByReference = [];
        $pendingDisplayConditions = [];

        foreach (($definition['categories'] ?? []) as $categoryDefinition) {
            $category = QuestionnaireCategory::query()->updateOrCreate(
                [
                    'questionnaire_id' => $questionnaire->id,
                    'sort_order' => (int) ($categoryDefinition['sort_order'] ?? 0),
                ],
                [
                    'title' => (string) ($categoryDefinition['title'] ?? ''),
                    'description' => $categoryDefinition['description'] ?: null,
                ],
            );

            $categoryIdsToKeep[] = $category->id;
            $questionIdsToKeep = [];

            foreach (($categoryDefinition['questions'] ?? []) as $questionDefinition) {
                $question = QuestionnaireQuestion::query()->updateOrCreate(
                    [
                        'questionnaire_category_id' => $category->id,
                        'sort_order' => (int) ($questionDefinition['sort_order'] ?? 0),
                    ],
                    [
                        'locale' => $questionnaire->locale,
                        'prompt' => (string) ($questionDefinition['prompt'] ?? ''),
                        'help_text' => $questionDefinition['help_text'] ?: null,
                        'type' => (string) ($questionDefinition['type'] ?? QuestionnaireQuestion::TYPE_SHORT_TEXT),
                        'options' => $this->normalizeOptions($questionDefinition['options'] ?? []),
                        'is_required' => (bool) ($questionDefinition['is_required'] ?? false),
                        'display_condition_question_id' => null,
                        'display_condition_operator' => null,
                        'display_condition_answer' => null,
                    ],
                );

                $questionIdsToKeep[] = $question->id;
                $questionsByReference[$this->questionReference(
                    (int) $category->sort_order,
                    (int) $question->sort_order,
                )] = $question;

                if (is_array($questionDefinition['display_condition'] ?? null)) {
                    $pendingDisplayConditions[$question->id] = $questionDefinition['display_condition'];
                }
            }

            $category->questions()
                ->whereNotIn('id', $questionIdsToKeep)
                ->delete();
        }

        $questionnaire->categories()
            ->whereNotIn('id', $categoryIdsToKeep)
            ->delete();

        foreach ($pendingDisplayConditions as $questionId => $displayCondition) {
            $targetReference = $this->questionReference(
                (int) ($displayCondition['category_sort_order'] ?? 0),
                (int) ($displayCondition['question_sort_order'] ?? 0),
            );

            $targetQuestion = $questionsByReference[$targetReference] ?? null;

            QuestionnaireQuestion::query()
                ->whereKey($questionId)
                ->update([
                    'display_condition_question_id' => $targetQuestion?->id,
                    'display_condition_operator' => $displayCondition['operator'] ?? null,
                    'display_condition_answer' => $displayCondition['answer'] ?? null,
                ]);
        }
    }

    /**
     * @return array<int, string>|null
     */
    protected function normalizeOptions(mixed $options): ?array
    {
        if (! is_array($options) || $options === []) {
            return null;
        }

        return array_values(array_map(
            static fn (mixed $option): string => (string) $option,
            $options,
        ));
    }

    protected function questionReference(int $categorySortOrder, int $questionSortOrder): string
    {
        return $categorySortOrder.':'.$questionSortOrder;
    }
}
