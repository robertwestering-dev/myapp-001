<?php

namespace App\Models;

use Database\Factories\AcademyCourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

#[Fillable([
    'slug',
    'theme',
    'path',
    'estimated_minutes',
    'sort_order',
    'is_active',
    'title',
    'audience',
    'goal',
    'summary',
    'learning_goals',
    'contents',
])]
class AcademyCourse extends Model
{
    /** @use HasFactory<AcademyCourseFactory> */
    use HasFactory;

    public const THEME_ADAPTABILITY = 'adaptability';

    public const THEME_RESILIENCE = 'resilience';

    /**
     * @return array<string, string>
     */
    public static function themes(): array
    {
        return [
            self::THEME_ADAPTABILITY => 'Adaptability',
            self::THEME_RESILIENCE => 'Digitale weerbaarheid',
        ];
    }

    protected function casts(): array
    {
        return [
            'estimated_minutes' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'title' => 'array',
            'audience' => 'array',
            'goal' => 'array',
            'summary' => 'array',
            'learning_goals' => 'array',
            'contents' => 'array',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function titleForLocale(?string $locale = null): string
    {
        return $this->translatedString('title', $locale);
    }

    public function audienceForLocale(?string $locale = null): string
    {
        return $this->translatedString('audience', $locale);
    }

    public function goalForLocale(?string $locale = null): string
    {
        return $this->translatedString('goal', $locale);
    }

    public function summaryForLocale(?string $locale = null): string
    {
        return $this->translatedString('summary', $locale);
    }

    /**
     * @return array<int, string>
     */
    public function learningGoalsForLocale(?string $locale = null): array
    {
        return $this->translatedList('learning_goals', $locale);
    }

    /**
     * @return array<int, string>
     */
    public function contentsForLocale(?string $locale = null): array
    {
        return $this->translatedList('contents', $locale);
    }

    public function translation(string $attribute, string $locale): mixed
    {
        return Arr::get($this->getAttribute($attribute) ?? [], $locale);
    }

    public function launchUrl(): ?string
    {
        if (! $this->isAvailable()) {
            return null;
        }

        return asset($this->normalizedPath().'/index.html');
    }

    public function isAvailable(): bool
    {
        return file_exists(public_path($this->normalizedPath().'/index.html'));
    }

    protected function translatedString(string $attribute, ?string $locale = null): string
    {
        $value = $this->translatedValue($attribute, $locale);

        return is_string($value) ? $value : '';
    }

    /**
     * @return array<int, string>
     */
    protected function translatedList(string $attribute, ?string $locale = null): array
    {
        $value = $this->translatedValue($attribute, $locale);

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, fn (mixed $item): bool => is_string($item) && $item !== ''));
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

    protected function normalizedPath(): string
    {
        return trim($this->path, '/');
    }
}
