<?php

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Database\Seeders\PositiveFoundationQuestionnaireSeeder;

test('the positive foundation questionnaire is available in the questionnaire library baseline', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
        ->first();

    expect($questionnaire)->not->toBeNull();
    expect($questionnaire?->locale)->toBe('nl');
    expect($questionnaire?->is_active)->toBeTrue();
    expect($questionnaire?->categories)->toHaveCount(5);
    expect($questionnaire?->categories->pluck('title')->all())->toBe([
        'Positieve emotie',
        'Betrokkenheid',
        'Relaties',
        'Zingeving',
        'Voldoening',
    ]);
    expect($questionnaire?->categories->sum(fn ($category) => $category->questions->count()))->toBe(20);
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->count() === 4))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->type === QuestionnaireQuestion::TYPE_LIKERT_SCALE))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->is_required))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Nooit / niet',
        'Zelden',
        'Soms',
        'Vaak',
        'Altijd / Volledig',
    ]))->toBeTrue();
    expect($questionnaire?->questions->pluck('prompt')->all())->toContain(
        'Hoe vaak voel je je tevreden met hoe je dag verloopt?',
        'Hoe vaak heb je het gevoel dat je vooruitgaat - hoe klein ook?',
    );
});

test('seeding the positive foundation questionnaire twice does not create duplicates', function () {
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);
    $this->seed(PositiveFoundationQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->withCount(['categories', 'questions'])
        ->where('title', SyncPositiveFoundationQuestionnaire::TITLE)
        ->firstOrFail();

    expect(Questionnaire::query()->where('title', SyncPositiveFoundationQuestionnaire::TITLE)->count())->toBe(1);
    expect($questionnaire->categories_count)->toBe(5);
    expect($questionnaire->questions_count)->toBe(20);
});
