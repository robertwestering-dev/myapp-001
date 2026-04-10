<?php

use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Database\Seeders\DigitalMirrorQuestionnaireSeeder;

test('the digital mirror questionnaire is available in the questionnaire library baseline', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->with(['categories.questions'])
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
        ->first();

    expect($questionnaire)->not->toBeNull();
    expect($questionnaire?->locale)->toBe('nl');
    expect($questionnaire?->is_active)->toBeTrue();
    expect($questionnaire?->categories)->toHaveCount(7);
    expect($questionnaire?->categories->pluck('title')->all())->toBe([
        'Positief fundament',
        'Groeimindset en grit',
        'Weerbaarheid',
        'Stress en het brein',
        'Zelfleiderschap',
        'Afleren en aanpassen',
        'Digitale weerbaarheid in de praktijk',
    ]);
    expect($questionnaire?->categories->sum(fn ($category) => $category->questions->count()))->toBe(28);
    expect($questionnaire?->categories->every(fn ($category) => $category->questions->count() === 4))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->type === QuestionnaireQuestion::TYPE_LIKERT_SCALE))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->is_required))->toBeTrue();
    expect($questionnaire?->questions->every(fn (QuestionnaireQuestion $question) => $question->options === [
        'Zeer mee oneens',
        'Mee oneens',
        'Neutraal',
        'Mee eens',
        'Zeer mee eens',
    ]))->toBeTrue();
});

test('seeding the digital mirror questionnaire twice does not create duplicates', function () {
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);
    $this->seed(DigitalMirrorQuestionnaireSeeder::class);

    $questionnaire = Questionnaire::query()
        ->withCount(['categories', 'questions'])
        ->where('title', SyncDigitalMirrorQuestionnaire::TITLE)
        ->firstOrFail();

    expect(Questionnaire::query()->where('title', SyncDigitalMirrorQuestionnaire::TITLE)->count())->toBe(1);
    expect($questionnaire->categories_count)->toBe(7);
    expect($questionnaire->questions_count)->toBe(28);
});
