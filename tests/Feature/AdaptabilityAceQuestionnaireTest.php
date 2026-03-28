<?php

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Models\Questionnaire;
use Database\Seeders\AdaptabilityAceQuestionnaireSeeder;

test('the adaptability ace questionnaire is available in the questionnaire library baseline', function () {
    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncAdaptabilityAceQuestionnaire::TITLE)
        ->first();

    expect($questionnaire)->not->toBeNull();
    expect($questionnaire?->is_active)->toBeTrue();
    expect($questionnaire?->categories)->toHaveCount(3);
    expect($questionnaire?->categories->pluck('title')->all())->toBe([
        'Ability',
        'Character',
        'Environment',
    ]);
    expect($questionnaire?->categories->sum(fn ($category) => $category->questions->count()))->toBe(15);
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->count() === 5))->toBeTrue();
});

test('seeding the adaptability ace questionnaire twice does not create duplicates', function () {
    $this->seed(AdaptabilityAceQuestionnaireSeeder::class);
    $this->seed(AdaptabilityAceQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->withCount(['categories', 'questions'])
        ->where('title', SyncAdaptabilityAceQuestionnaire::TITLE)
        ->firstOrFail();

    expect(Questionnaire::query()->where('title', SyncAdaptabilityAceQuestionnaire::TITLE)->count())->toBe(1);
    expect($questionnaire->categories_count)->toBe(3);
    expect($questionnaire->questions_count)->toBe(15);
});
