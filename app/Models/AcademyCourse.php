<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use Database\Factories\AcademyCourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

#[Fillable([
    'slug',
    'theme',
    'path',
    'localized_paths',
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
            'localized_paths' => 'array',
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

    public function launchUrl(?string $locale = null): ?string
    {
        if (! $this->isAvailable($locale)) {
            return null;
        }

        return Route::has('academy-courses.show')
            ? route('academy-courses.show', [
                'academyCoursePath' => $this->contentRouteSegment(),
                'asset' => $this->launchAssetPath($locale),
            ])
            : null;
    }

    public function launchPageUrl(?string $locale = null): ?string
    {
        if (! $this->isAvailable($locale)) {
            return null;
        }

        return Route::has('academy.courses.launch')
            ? route('academy.courses.launch', $this->slug)
            : null;
    }

    public function isAvailable(?string $locale = null): bool
    {
        return is_file($this->contentPath(locale: $locale ?? app()->getLocale()));
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

    public function contentDirectory(?string $locale = null): string
    {
        $directory = storage_path('app/private/'.$this->normalizedPath());

        if ($locale === null) {
            return $directory;
        }

        $localizedPath = $this->localizedPathForLocale($locale);

        if ($localizedPath === null) {
            return $directory;
        }

        return $directory.'/'.$localizedPath;
    }

    public function contentPath(?string $relativePath = null, ?string $locale = null): string
    {
        $courseDirectory = $this->contentDirectory($locale);

        if ($relativePath === null) {
            return $courseDirectory.'/index.html';
        }

        return $courseDirectory.'/'.ltrim($relativePath, '/');
    }

    public function contentRouteSegment(): string
    {
        return Str::after($this->normalizedPath(), 'academy-courses/');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(AcademyCourseProgress::class);
    }

    public function localizedPathForLocale(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $localizedPath = $this->localized_paths[$locale] ?? null;

        if (! is_string($localizedPath) || trim($localizedPath, '/') === '') {
            return null;
        }

        return trim(str_replace('\\', '/', $localizedPath), '/');
    }

    public function launchAssetPath(?string $locale = null): string
    {
        $localizedPath = $this->localizedPathForLocale($locale);

        if ($localizedPath === null) {
            return 'index.html';
        }

        return $localizedPath.'/index.html';
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
