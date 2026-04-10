<?php

namespace App\Concerns;

trait HandlesLocalizedPayload
{
    /**
     * Normalize a localized string field: trim whitespace, convert empty strings to null.
     *
     * @param  array<string, string>  $values
     * @return array<string, string|null>
     */
    protected function localizedFieldPayload(array $values): array
    {
        return collect($values)
            ->mapWithKeys(function (mixed $value, string $locale): array {
                $normalizedValue = is_string($value) ? trim($value) : '';

                return [$locale => $normalizedValue === '' ? null : $normalizedValue];
            })
            ->all();
    }

    /**
     * Normalize a localized list field: split by newlines, trim, filter empties.
     *
     * @param  array<string, string>  $values
     * @return array<string, array<int, string>>
     */
    protected function localizedListPayload(array $values): array
    {
        return collect($values)
            ->map(function (string $value): array {
                return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
                    ->map(fn (string $line): string => trim($line))
                    ->filter()
                    ->values()
                    ->all();
            })
            ->all();
    }
}
