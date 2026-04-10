<?php

namespace App\Concerns;

use Illuminate\Support\Arr;

trait HasTranslations
{
    public function translation(string $attribute, string $locale): mixed
    {
        return Arr::get($this->getAttribute($attribute) ?? [], $locale);
    }

    protected function translatedString(string $attribute, ?string $locale = null): string
    {
        $value = $this->translatedValue($attribute, $locale);

        return is_string($value) ? $value : '';
    }

    protected function translatedValue(string $attribute, ?string $locale = null): mixed
    {
        /** @var array<string, mixed> $values */
        $values = $this->getAttribute($attribute) ?? [];
        $preferredLocale = $locale ?? app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        return $values[$preferredLocale]
            ?? $values[$fallbackLocale]
            ?? Arr::first($values);
    }
}
