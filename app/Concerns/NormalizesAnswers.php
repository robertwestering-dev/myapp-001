<?php

namespace App\Concerns;

use App\Models\QuestionnaireQuestion;

trait NormalizesAnswers
{
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
