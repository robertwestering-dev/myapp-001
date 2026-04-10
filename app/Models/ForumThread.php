<?php

namespace App\Models;

use Database\Factories\ForumThreadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'slug',
    'discussion_type',
    'title',
    'body',
    'tags',
    'is_locked',
    'last_activity_at',
])]
class ForumThread extends Model
{
    /** @use HasFactory<ForumThreadFactory> */
    use HasFactory;

    public const TYPE_QUESTION = 'question';

    public const TYPE_EXPERIENCE = 'experience';

    public const TYPE_INSIGHT = 'insight';

    protected static function booted(): void
    {
        static::creating(function (self $forumThread): void {
            if (blank($forumThread->slug)) {
                $forumThread->slug = static::generateCandidateSlug($forumThread->title);
            }

            if ($forumThread->last_activity_at === null) {
                $forumThread->last_activity_at = now();
            }
        });
    }

    /**
     * Retry slug generation on unique constraint violation to handle race conditions
     * where two concurrent requests pass the existence check before either inserts.
     */
    public static function createWithUniqueSlug(array $attributes): static
    {
        $attempts = 0;

        while (true) {
            try {
                return static::query()->create($attributes);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempts++ >= 5) {
                    throw $e;
                }

                $baseSlug = Str::slug($attributes['title'] ?? 'discussion');
                $attributes['slug'] = ($baseSlug !== '' ? $baseSlug : 'discussion').'-'.Str::lower(Str::random(4));
            }
        }
    }

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_locked' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<int, string>
     */
    public static function discussionTypeOptions(): array
    {
        return [
            self::TYPE_QUESTION,
            self::TYPE_EXPERIENCE,
            self::TYPE_INSIGHT,
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class)->orderBy('created_at');
    }

    public function scopeRecent(Builder $query): void
    {
        $query
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id');
    }

    public function renderedBody(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function excerpt(int $limit = 220): string
    {
        return Str::limit(trim(strip_tags($this->renderedBody())), $limit);
    }

    /**
     * @return Collection<int, string>
     */
    public function tagList(): Collection
    {
        return collect($this->tags ?? [])
            ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
            ->values();
    }

    protected static function generateCandidateSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug !== '' ? $baseSlug : 'discussion';
        $counter = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $counter++;
            $slug = ($baseSlug !== '' ? $baseSlug : 'discussion').'-'.$counter;
        }

        return $slug;
    }
}
