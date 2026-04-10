<?php

namespace App\Models;

use Database\Factories\QuestionnaireResponseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_questionnaire_id', 'user_id', 'submitted_at', 'last_saved_at', 'resume_token', 'current_questionnaire_category_id', 'analysis_snapshot'])]
class QuestionnaireResponse extends Model
{
    /** @use HasFactory<QuestionnaireResponseFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'last_saved_at' => 'datetime',
            'analysis_snapshot' => 'array',
        ];
    }

    public function isDraft(): bool
    {
        return $this->submitted_at === null;
    }

    public function state(): string
    {
        return $this->isDraft() ? 'draft' : 'completed';
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionnaireResponseAnswer::class);
    }

    public function organizationQuestionnaire(): BelongsTo
    {
        return $this->belongsTo(OrganizationQuestionnaire::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentQuestionnaireCategory(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireCategory::class, 'current_questionnaire_category_id');
    }
}
