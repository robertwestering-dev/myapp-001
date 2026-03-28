<?php

namespace App\Models;

use Database\Factories\QuestionnaireFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['title', 'description', 'is_active'])]
class Questionnaire extends Model
{
    /** @use HasFactory<QuestionnaireFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(QuestionnaireCategory::class)->orderBy('sort_order');
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(
            QuestionnaireQuestion::class,
            QuestionnaireCategory::class,
            'questionnaire_id',
            'questionnaire_category_id',
            'id',
            'id',
        );
    }

    public function organizationQuestionnaires(): HasMany
    {
        return $this->hasMany(OrganizationQuestionnaire::class);
    }
}
