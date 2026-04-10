<?php

namespace App\Support\Questionnaires;

use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Collection;

class QuestionnaireConditionEvaluator
{
    /**
     * @param  array<int|string, mixed>  $answers
     */
    public function isVisible(QuestionnaireQuestion $question, array $answers): bool
    {
        if ($question->display_condition_question_id === null || $question->display_condition_operator === null) {
            return true;
        }

        $answer = data_get($answers, (string) $question->display_condition_question_id);
        $normalizedAnswer = $this->normalizeValue($answer);
        $expected = $this->normalizeValue($question->display_condition_answer);

        return match ($question->display_condition_operator) {
            QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS => count($normalizedAnswer) === 1
                && count($expected) === 1
                && $normalizedAnswer[0] === $expected[0],
            QuestionnaireQuestion::DISPLAY_CONDITION_NOT_EQUALS => count($normalizedAnswer) !== 1
                || count($expected) !== 1
                || $normalizedAnswer[0] !== $expected[0],
            QuestionnaireQuestion::DISPLAY_CONDITION_CONTAINS => collect($expected)
                ->intersect($normalizedAnswer)
                ->isNotEmpty(),
            QuestionnaireQuestion::DISPLAY_CONDITION_NOT_CONTAINS => collect($expected)
                ->intersect($normalizedAnswer)
                ->isEmpty(),
            QuestionnaireQuestion::DISPLAY_CONDITION_ANSWERED => $normalizedAnswer !== [],
            QuestionnaireQuestion::DISPLAY_CONDITION_NOT_ANSWERED => $normalizedAnswer === [],
            default => true,
        };
    }

    /**
     * @param  array<int|string, mixed>  $answers
     * @return array<int, QuestionnaireQuestion>
     */
    public function visibleQuestionIds(iterable $questions, array $answers): array
    {
        return collect($this->visibleQuestions($questions, $answers))
            ->map(fn (QuestionnaireQuestion $question): int => $question->id)
            ->all();
    }

    /**
     * @param  array<int|string, mixed>  $answers
     * @return array<int, QuestionnaireQuestion>
     */
    public function visibleQuestions(iterable $questions, array $answers): array
    {
        $questionMap = collect($questions)
            ->filter(fn (mixed $question): bool => $question instanceof QuestionnaireQuestion)
            ->keyBy(fn (QuestionnaireQuestion $question): string => (string) $question->id);
        $visibilityCache = [];

        return $questionMap
            ->filter(function (QuestionnaireQuestion $question) use ($answers, $questionMap, &$visibilityCache): bool {
                return $this->isQuestionVisible($question, $questionMap, $answers, $visibilityCache);
            })
            ->values()
            ->all();
    }

    /**
     * @param  Collection<string, QuestionnaireQuestion>  $questionMap
     * @param  array<int|string, mixed>  $answers
     * @param  array<string, bool>  $visibilityCache
     */
    protected function isQuestionVisible(
        QuestionnaireQuestion $question,
        Collection $questionMap,
        array $answers,
        array &$visibilityCache
    ): bool {
        $cacheKey = (string) $question->id;

        if (array_key_exists($cacheKey, $visibilityCache)) {
            return $visibilityCache[$cacheKey];
        }

        $dependencyId = $question->display_condition_question_id;

        if ($dependencyId === null || $question->display_condition_operator === null) {
            return $visibilityCache[$cacheKey] = true;
        }

        $dependency = $questionMap->get((string) $dependencyId);

        if (! $dependency instanceof QuestionnaireQuestion) {
            return $visibilityCache[$cacheKey] = false;
        }

        if (! $this->isQuestionVisible($dependency, $questionMap, $answers, $visibilityCache)) {
            return $visibilityCache[$cacheKey] = false;
        }

        return $visibilityCache[$cacheKey] = $this->isVisible($question, $answers);
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeValue(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn (mixed $item): string => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? [] : [$normalized];
    }
}
