<?php

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

test('questionnaires can be exported to json and imported again', function () {
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Lokale vragenlijst',
        'description' => 'Testexport voor live import.',
        'locale' => 'nl',
        'is_active' => true,
    ]);

    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Eerste categorie',
        'description' => 'Beschrijving van de eerste categorie.',
        'sort_order' => 1,
    ]);

    $triggerQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'locale' => 'nl',
        'prompt' => 'Heeft u behoefte aan verdieping?',
        'help_text' => 'Kies ja of nee.',
        'options' => ['Ja', 'Nee'],
        'sort_order' => 1,
    ]);

    QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'locale' => 'nl',
        'prompt' => 'Welke verdieping wilt u ontvangen?',
        'help_text' => 'Beschrijf uw voorkeur.',
        'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
        'display_condition_question_id' => $triggerQuestion->id,
        'display_condition_operator' => QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS,
        'display_condition_answer' => ['Ja'],
        'sort_order' => 2,
    ]);

    $exportPath = storage_path('app/testing/questionnaires-roundtrip.json');

    Artisan::call('questionnaires:export', [
        '--questionnaire' => [$questionnaire->id],
        '--path' => $exportPath,
    ]);

    expect(File::exists($exportPath))->toBeTrue();

    Questionnaire::query()->delete();

    Artisan::call('questionnaires:import', [
        'path' => $exportPath,
    ]);

    $importedQuestionnaire = Questionnaire::query()
        ->with('categories.questions')
        ->where('title', 'Lokale vragenlijst')
        ->where('locale', 'nl')
        ->firstOrFail();

    expect($importedQuestionnaire->categories)->toHaveCount(1);
    expect($importedQuestionnaire->categories->first()->questions)->toHaveCount(2);

    $importedFollowUpQuestion = $importedQuestionnaire->categories
        ->first()
        ->questions
        ->firstWhere('sort_order', 2);

    expect($importedFollowUpQuestion->display_condition_operator)->toBe(QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS);
    expect($importedFollowUpQuestion->display_condition_answer)->toBe(['Ja']);
    expect($importedFollowUpQuestion->display_condition_question_id)->not->toBeNull();

    File::delete($exportPath);
});
