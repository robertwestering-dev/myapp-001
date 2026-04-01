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
    'display_condition_question_id',
    'display_condition_operator',
    'display_condition_answer',
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

    public const DISPLAY_CONDITION_EQUALS = 'equals';

    public const DISPLAY_CONDITION_NOT_EQUALS = 'not_equals';

    public const DISPLAY_CONDITION_CONTAINS = 'contains';

    public const DISPLAY_CONDITION_NOT_CONTAINS = 'not_contains';

    public const DISPLAY_CONDITION_ANSWERED = 'answered';

    public const DISPLAY_CONDITION_NOT_ANSWERED = 'not_answered';

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'display_condition_answer' => 'array',
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

    /**
     * @return array<int, string>
     */
    public static function displayConditionOperators(): array
    {
        return [
            self::DISPLAY_CONDITION_EQUALS,
            self::DISPLAY_CONDITION_NOT_EQUALS,
            self::DISPLAY_CONDITION_CONTAINS,
            self::DISPLAY_CONDITION_NOT_CONTAINS,
            self::DISPLAY_CONDITION_ANSWERED,
            self::DISPLAY_CONDITION_NOT_ANSWERED,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function displayConditionOperatorLabels(): array
    {
        return [
            self::DISPLAY_CONDITION_EQUALS => 'Is gelijk aan',
            self::DISPLAY_CONDITION_NOT_EQUALS => 'Is niet gelijk aan',
            self::DISPLAY_CONDITION_CONTAINS => 'Bevat',
            self::DISPLAY_CONDITION_NOT_CONTAINS => 'Bevat niet',
            self::DISPLAY_CONDITION_ANSWERED => 'Is ingevuld',
            self::DISPLAY_CONDITION_NOT_ANSWERED => 'Is niet ingevuld',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireCategory::class, 'questionnaire_category_id');
    }

    public function displayConditionQuestion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'display_condition_question_id');
    }
}
