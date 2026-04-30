<?php

namespace App\Models;

use Database\Factories\JournalEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'entry_date',
    'entry_type',
    'content',
    'what_went_well',
    'my_contribution',
])]
class JournalEntry extends Model
{
    /** @use HasFactory<JournalEntryFactory> */
    use HasFactory;

    public const TYPE_THREE_GOOD_THINGS = 'three_good_things';

    public const TYPE_STRENGTHS_REFLECTION = 'strengths_reflection';

    protected $table = 'three_good_things_entries';

    protected $attributes = [
        'entry_type' => self::TYPE_THREE_GOOD_THINGS,
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'content' => 'array',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function entryTypeOptions(): array
    {
        return [
            self::TYPE_THREE_GOOD_THINGS,
            self::TYPE_STRENGTHS_REFLECTION,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function strengthOptions(): array
    {
        $labels = trans('hermes.academy.strengths_widget.options');

        return is_array($labels) ? $labels : [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, User $user): void
    {
        $query->whereBelongsTo($user);
    }

    public function scopeRecent(Builder $query): void
    {
        $query
            ->orderByDesc('entry_date')
            ->orderByDesc('id');
    }

    public function isThreeGoodThings(): bool
    {
        return $this->entry_type === self::TYPE_THREE_GOOD_THINGS;
    }

    public function isStrengthsReflection(): bool
    {
        return $this->entry_type === self::TYPE_STRENGTHS_REFLECTION;
    }

    public function contentValue(string $key): ?string
    {
        $value = $this->content[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    public function strengthLabel(): ?string
    {
        $key = $this->contentValue('strength_key');

        if ($key === null) {
            return null;
        }

        return static::strengthOptions()[$key] ?? $key;
    }
}
