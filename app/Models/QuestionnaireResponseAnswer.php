<?php

namespace App\Models;

use Database\Factories\QuestionnaireResponseAnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['questionnaire_response_id', 'questionnaire_question_id', 'answer', 'answer_list'])]
class QuestionnaireResponseAnswer extends Model
{
    /** @use HasFactory<QuestionnaireResponseAnswerFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'answer_list' => 'array',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'questionnaire_question_id');
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireResponse::class, 'questionnaire_response_id');
    }
}
