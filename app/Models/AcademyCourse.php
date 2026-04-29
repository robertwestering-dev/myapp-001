<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use Database\Factories\AcademyCourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

#[Fillable([
    'slug',
    'theme',
    'path',
    'estimated_minutes',
    'sort_order',
    'is_active',
    'pro_only',
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
    use HasFactory, HasTranslations;

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
            'pro_only' => 'boolean',
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

    public function launchUrl(): ?string
    {
        if (! $this->isAvailable()) {
            return null;
        }

        return Route::has('academy-courses.show')
            ? route('academy-courses.show', ['academyCoursePath' => $this->contentRouteSegment()])
            : null;
    }

    public function isAvailable(): bool
    {
        return is_file($this->contentPath());
    }

    public function canBeLaunchedBy(User $user): bool
    {
        if (! in_array($user->role, [User::ROLE_USER, User::ROLE_USER_PRO], true)) {
            return false;
        }

        if ($this->pro_only && ! $user->isProUser()) {
            return false;
        }

        return true;
    }

    public function contentPath(?string $relativePath = null): string
    {
        $coursePath = storage_path('app/private/'.$this->normalizedPath());

        if ($relativePath === null) {
            return $coursePath.'/index.html';
        }

        return $coursePath.'/'.ltrim($relativePath, '/');
    }

    public function contentRouteSegment(): string
    {
        return Str::after($this->normalizedPath(), 'academy-courses/');
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

    protected function normalizedPath(): string
    {
        return trim($this->path, '/');
    }
}
