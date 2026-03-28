<?php

use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Models\Questionnaire;
use Database\Seeders\DigitalResilienceQuickScanQuestionnaireSeeder;

test('the digital resilience quick scan questionnaire is available in the questionnaire library baseline', function () {
    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncDigitalResilienceQuickScanQuestionnaire::TITLE)
        ->first();

    expect($questionnaire)->not->toBeNull();
    expect($questionnaire?->is_active)->toBeTrue();
    expect($questionnaire?->categories)->toHaveCount(3);
    expect($questionnaire?->categories->pluck('title')->all())->toBe([
        'Leerhouding en nieuwsgierigheid',
        'Digitaal leergedrag in de praktijk',
        'Wendbaarheid in digitale verandering',
    ]);
    expect($questionnaire?->categories->sum(fn ($category) => $category->questions->count()))->toBe(15);
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->count() === 5))->toBeTrue();
});

test('seeding the digital resilience quick scan questionnaire twice does not create duplicates', function () {
    $this->seed(DigitalResilienceQuickScanQuestionnaireSeeder::class);
    $this->seed(DigitalResilienceQuickScanQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->withCount(['categories', 'questions'])
        ->where('title', SyncDigitalResilienceQuickScanQuestionnaire::TITLE)
        ->firstOrFail();

    expect(Questionnaire::query()->where('title', SyncDigitalResilienceQuickScanQuestionnaire::TITLE)->count())->toBe(1);
    expect($questionnaire->categories_count)->toBe(3);
    expect($questionnaire->questions_count)->toBe(15);
});
