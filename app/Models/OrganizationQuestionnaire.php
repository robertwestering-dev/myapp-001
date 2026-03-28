<?php

namespace App\Models;

use Database\Factories\OrganizationQuestionnaireFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'questionnaire_id',
    'org_id',
    'available_from',
    'available_until',
    'is_active',
])]
class OrganizationQuestionnaire extends Model
{
    /** @use HasFactory<OrganizationQuestionnaireFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'available_from' => 'date',
            'available_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }

    public function isAvailable(): bool
    {
        $today = Carbon::today();

        if (! $this->is_active || ! $this->questionnaire?->is_active) {
            return false;
        }

        if ($this->available_from !== null && $this->available_from->isAfter($today)) {
            return false;
        }

        if ($this->available_until !== null && $this->available_until->isBefore($today)) {
            return false;
        }

        return true;
    }
}
