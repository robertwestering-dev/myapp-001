<?php

namespace App\Support\Questionnaires;

use App\Models\QuestionnaireQuestion;

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
        $normalizedAnswer = $this->normalizeAnswer($answer);
        $expected = $this->normalizeExpected($question->display_condition_answer);

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
        $visibleQuestionIds = [];

        foreach ($questions as $question) {
            if ($question instanceof QuestionnaireQuestion && $this->isVisible($question, $answers)) {
                $visibleQuestionIds[] = $question->id;
            }
        }

        return $visibleQuestionIds;
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeAnswer(mixed $answer): array
    {
        if (is_array($answer)) {
            return collect($answer)
                ->map(fn (mixed $value): string => trim((string) $value))
                ->filter()
                ->values()
                ->all();
        }

        $value = trim((string) $answer);

        return $value === '' ? [] : [$value];
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeExpected(mixed $expected): array
    {
        if (is_array($expected)) {
            return collect($expected)
                ->map(fn (mixed $value): string => trim((string) $value))
                ->filter()
                ->values()
                ->all();
        }

        $value = trim((string) $expected);

        return $value === '' ? [] : [$value];
    }
}
