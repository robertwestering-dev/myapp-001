<?php

namespace App\Models;

use App\Concerns\HasTranslations;
use App\Services\BlogPostRenderer;
use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

#[Fillable([
    'author_id',
    'slug',
    'cover_image_url',
    'tags',
    'title',
    'excerpt',
    'content',
    'is_published',
    'is_featured',
    'published_at',
])]
class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory, HasTranslations;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'title' => 'array',
            'excerpt' => 'array',
            'content' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function titleForLocale(?string $locale = null): string
    {
        return $this->translatedString('title', $locale);
    }

    public function excerptForLocale(?string $locale = null): string
    {
        return $this->translatedString('excerpt', $locale);
    }

    public function metaTitleForLocale(?string $locale = null): string
    {
        return $this->titleForLocale($locale);
    }

    public function metaDescriptionForLocale(?string $locale = null): string
    {
        $excerpt = trim($this->excerptForLocale($locale));

        if ($excerpt !== '') {
            return Str::limit($excerpt, 160);
        }

        return Str::limit(trim(strip_tags($this->renderedContentForLocale($locale))), 160);
    }

    public function contentForLocale(?string $locale = null): string
    {
        return $this->translatedString('content', $locale);
    }

    public function renderedContentForLocale(?string $locale = null): string
    {
        return app(BlogPostRenderer::class)->render($this->contentForLocale($locale));
    }

    /**
     * @return array<int, string>
     */
    public function tagsList(): array
    {
        return collect($this->tags ?? [])
            ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
            ->map(fn (string $tag): string => trim($tag))
            ->values()
            ->all();
    }

    public function normalizedTags(): Collection
    {
        return collect($this->tagsList())
            ->map(fn (string $tag): string => Str::lower($tag));
    }

    public function readingTimeInMinutes(?string $locale = null): int
    {
        $wordCount = str_word_count(strip_tags($this->contentForLocale($locale)));

        return max(1, (int) ceil($wordCount / 220));
    }

    public function isPublished(): bool
    {
        return $this->is_published
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function publicationStatus(): string
    {
        if (! $this->is_published) {
            return 'draft';
        }

        if ($this->published_at !== null && $this->published_at->isFuture()) {
            return 'scheduled';
        }

        return 'published';
    }

    public function publicUrl(): string
    {
        return route('blog.show', $this);
    }

    protected function translatedValue(string $attribute, ?string $locale = null): mixed
    {
        /** @var array<string, mixed> $values */
        $values = $this->getAttribute($attribute) ?? [];
        $preferredLocale = $locale ?? app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');
        $preferredValue = $values[$preferredLocale] ?? null;

        if ($this->isMeaningfulTranslation($preferredValue)) {
            return $preferredValue;
        }

        $fallbackValue = $values[$fallbackLocale] ?? null;

        if ($this->isMeaningfulTranslation($fallbackValue)) {
            return $fallbackValue;
        }

        return collect($values)
            ->first(fn (mixed $value): bool => $this->isMeaningfulTranslation($value));
    }

    protected function isMeaningfulTranslation(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $normalizedValue = trim($value);

        if ($normalizedValue === '') {
            return false;
        }

        return preg_match('/[\p{L}\p{N}]/u', $normalizedValue) === 1;
    }
}
