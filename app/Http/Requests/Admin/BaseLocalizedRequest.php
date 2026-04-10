<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseLocalizedRequest extends FormRequest
{
    /**
     * Generate validation rules for a localized string field.
     *
     * When $primaryOnly is true, only the primary locale is required; all
     * other supported locales are nullable. Set $primaryOnly to false to
     * require every supported locale (e.g. Academy courses).
     *
     * @return array<string, array<int, mixed>>
     */
    protected function localizedStringRules(string $attribute, ?int $maxLength = null, bool $primaryOnly = true): array
    {
        $rules = [
            $attribute => ['required', 'array'],
        ];

        foreach (array_keys(config('locales.supported', [])) as $locale) {
            $fieldRules = (! $primaryOnly || $locale === config('locales.primary'))
                ? ['required', 'string']
                : ['nullable', 'string'];

            if ($maxLength !== null) {
                $fieldRules[] = 'max:'.$maxLength;
            }

            $rules["{$attribute}.{$locale}"] = $fieldRules;
        }

        return $rules;
    }
}
