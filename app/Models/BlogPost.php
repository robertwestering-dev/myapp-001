<?php

namespace App\Models;

use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

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
    use HasFactory;

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

    public function contentForLocale(?string $locale = null): string
    {
        return $this->translatedString('content', $locale);
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

    public function translation(string $attribute, string $locale): mixed
    {
        return Arr::get($this->getAttribute($attribute) ?? [], $locale);
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
