<?php

namespace App\Models;

use Database\Factories\QuestionnaireQuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'questionnaire_category_id',
    'prompt',
    'help_text',
    'type',
    'options',
    'is_required',
    'sort_order',
])]
class QuestionnaireQuestion extends Model
{
    /** @use HasFactory<QuestionnaireQuestionFactory> */
    use HasFactory;

    public const TYPE_SHORT_TEXT = 'short_text';

    public const TYPE_LONG_TEXT = 'long_text';

    public const TYPE_SINGLE_CHOICE = 'single_choice';

    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';

    public const TYPE_NUMBER = 'number';

    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_DATE = 'date';

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_SHORT_TEXT,
            self::TYPE_LONG_TEXT,
            self::TYPE_SINGLE_CHOICE,
            self::TYPE_MULTIPLE_CHOICE,
            self::TYPE_NUMBER,
            self::TYPE_BOOLEAN,
            self::TYPE_DATE,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function typeLabels(): array
    {
        return [
            self::TYPE_SHORT_TEXT => __('hermes.question_types.short_text'),
            self::TYPE_LONG_TEXT => __('hermes.question_types.long_text'),
            self::TYPE_SINGLE_CHOICE => __('hermes.question_types.single_choice'),
            self::TYPE_MULTIPLE_CHOICE => __('hermes.question_types.multiple_choice'),
            self::TYPE_NUMBER => __('hermes.question_types.number'),
            self::TYPE_BOOLEAN => __('hermes.question_types.boolean'),
            self::TYPE_DATE => __('hermes.question_types.date'),
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireCategory::class, 'questionnaire_category_id');
    }
}
