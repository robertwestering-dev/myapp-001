<?php

namespace App\Models;

use Database\Factories\QuestionnaireResponseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_questionnaire_id', 'user_id', 'submitted_at'])]
class QuestionnaireResponse extends Model
{
    /** @use HasFactory<QuestionnaireResponseFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
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
}
