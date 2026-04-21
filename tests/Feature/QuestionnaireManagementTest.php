<?php

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\User;

test('admins can view the questionnaire list and create page', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkdrukmeting',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.index'))
        ->assertOk()
        ->assertSee('Questionnaire-overzicht')
        ->assertSee('admin-status-badge', false)
        ->assertSee('Werkdrukmeting')
        ->assertSee(SyncAdaptabilityAceQuestionnaire::TITLE)
        ->assertSee(SyncDigitalResilienceQuickScanQuestionnaire::TITLE)
        ->assertSee('Nieuwe questionnaire');

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.create'))
        ->assertOk()
        ->assertSee('Nieuwe questionnaire');
});

test('default questionnaires are synchronized once with dutch and english questions', function () {
    (new SyncAdaptabilityAceQuestionnaire)->handle();
    (new SyncDigitalResilienceQuickScanQuestionnaire)->handle();

    $this->assertDatabaseHas('questionnaires', [
        'title' => SyncAdaptabilityAceQuestionnaire::TITLE,
        'locale' => 'nl',
    ]);

    $this->assertDatabaseHas('questionnaires', [
        'title' => SyncDigitalResilienceQuickScanQuestionnaire::TITLE,
        'locale' => 'nl',
    ]);

    expect(Questionnaire::query()->where('title', SyncAdaptabilityAceQuestionnaire::TITLE)->count())->toBe(1);
    expect(Questionnaire::query()->where('title', SyncDigitalResilienceQuickScanQuestionnaire::TITLE)->count())->toBe(1);

    $this->assertDatabaseHas('questionnaire_questions', [
        'prompt' => 'I generally stay calm and constructive when outcomes are uncertain.',
        'locale' => 'en',
    ]);

    $this->assertDatabaseHas('questionnaire_questions', [
        'prompt' => 'I deliberately set aside time to explore new digital tools, applications, or ways of working.',
        'locale' => 'en',
    ]);
});

test('admins can compose a questionnaire with categories and questions', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.store'), [
            'title' => 'Vitaliteitsscan',
            'description' => 'Meet vitaliteit in meerdere domeinen.',
            'locale' => 'nl',
            'is_active' => '1',
        ])
        ->assertRedirect();

    $questionnaire = Questionnaire::query()->where('title', 'Vitaliteitsscan')->firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.categories.store', $questionnaire), [
            'title' => 'Energie',
            'description' => 'Vragen over energie en herstel.',
            'sort_order' => 1,
        ])
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $category = $questionnaire->categories()->firstOrFail();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.questions.store', $questionnaire), [
            'questionnaire_category_id' => $category->id,
            'locale' => 'nl',
            'prompt' => 'Hoeveel energie ervaart u gemiddeld tijdens uw werkdag?',
            'help_text' => 'Kies het antwoord dat het beste past.',
            'type' => 'single_choice',
            'options' => "Veel\nGemiddeld\nWeinig",
            'is_required' => '1',
            'sort_order' => 1,
        ])
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $this->assertDatabaseHas('questionnaires', [
        'title' => 'Vitaliteitsscan',
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('questionnaire_categories', [
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Energie',
    ]);

    $this->assertDatabaseHas('questionnaire_questions', [
        'questionnaire_category_id' => $category->id,
        'type' => 'single_choice',
        'is_required' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.edit', $questionnaire))
        ->assertOk()
        ->assertSee('Categorieen en vragen')
        ->assertSee('Energie')
        ->assertSee('Hoeveel energie ervaart u gemiddeld tijdens uw werkdag?');
});

test('admins can configure conditional questionnaire questions', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $triggerQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Is extra verdieping nodig?',
        'options' => ['Ja', 'Nee'],
    ]);

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.questions.store', $questionnaire), [
            'questionnaire_category_id' => $category->id,
            'locale' => 'nl',
            'prompt' => 'Welke verdieping wilt u ontvangen?',
            'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
            'display_condition_question_id' => $triggerQuestion->id,
            'display_condition_operator' => QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS,
            'display_condition_answer' => 'Ja',
            'sort_order' => 2,
        ])
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $this->assertDatabaseHas('questionnaire_questions', [
        'questionnaire_category_id' => $category->id,
        'display_condition_question_id' => $triggerQuestion->id,
        'display_condition_operator' => QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS,
    ]);
});

test('admins can add likert scale questions with multiple scale options', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.questions.store', $questionnaire), [
            'questionnaire_category_id' => $category->id,
            'locale' => 'nl',
            'prompt' => 'Ik voel me gesteund door mijn team.',
            'type' => QuestionnaireQuestion::TYPE_LIKERT_SCALE,
            'options' => "Helemaal oneens\nOneens\nNeutraal\nEens\nHelemaal eens",
            'is_required' => '1',
            'sort_order' => 1,
        ])
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $question = QuestionnaireQuestion::query()
        ->where('questionnaire_category_id', $category->id)
        ->where('type', QuestionnaireQuestion::TYPE_LIKERT_SCALE)
        ->firstOrFail();

    expect($question->options)->toBe(['Helemaal oneens', 'Oneens', 'Neutraal', 'Eens', 'Helemaal eens']);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.edit', $questionnaire))
        ->assertOk()
        ->assertSee('Likert-schaal')
        ->assertSee('Helemaal eens');
});

test('admins can update and delete questionnaire categories', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Werkdruk',
        'sort_order' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.categories.edit', [$questionnaire, $category]))
        ->assertOk()
        ->assertSee('Categorie wijzigen')
        ->assertSee('Werkdruk');

    $this->actingAs($admin)
        ->put(route('admin.questionnaires.categories.update', [$questionnaire, $category]), [
            'title' => 'Werkbelasting',
            'description' => 'Vragen over ervaren belasting.',
            'sort_order' => 2,
        ])
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $this->assertDatabaseHas('questionnaire_categories', [
        'id' => $category->id,
        'title' => 'Werkbelasting',
        'sort_order' => 2,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.questionnaires.categories.destroy', [$questionnaire, $category]))
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $this->assertDatabaseMissing('questionnaire_categories', [
        'id' => $category->id,
    ]);
});

test('admins can update and delete questionnaire questions', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $question = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Oude vraag?',
        'sort_order' => 1,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.questions.edit', [$questionnaire, $question]))
        ->assertOk()
        ->assertSee('Vraag wijzigen')
        ->assertSee('Oude vraag?');

    $this->actingAs($admin)
        ->put(route('admin.questionnaires.questions.update', [$questionnaire, $question]), [
            'questionnaire_category_id' => $category->id,
            'locale' => 'nl',
            'prompt' => 'Nieuwe vraag?',
            'help_text' => 'Werk de antwoordkeuze bij.',
            'type' => QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE,
            'options' => "Optie A\nOptie B\nOptie C",
            'is_required' => '1',
            'sort_order' => 2,
        ])
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $question->refresh();

    expect($question->prompt)->toBe('Nieuwe vraag?');
    expect($question->type)->toBe(QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE);
    expect($question->options)->toBe(['Optie A', 'Optie B', 'Optie C']);
    expect($question->is_required)->toBeTrue();
    expect($question->sort_order)->toBe(2);

    $this->actingAs($admin)
        ->delete(route('admin.questionnaires.questions.destroy', [$questionnaire, $question]))
        ->assertRedirect(route('admin.questionnaires.edit', $questionnaire));

    $this->assertDatabaseMissing('questionnaire_questions', [
        'id' => $question->id,
    ]);
});

test('admins cannot attach a question to a category from another questionnaire', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create();
    $otherQuestionnaire = Questionnaire::factory()->create();
    $otherCategory = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $otherQuestionnaire->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.questions.store', $questionnaire), [
            'questionnaire_category_id' => $otherCategory->id,
            'locale' => 'nl',
            'prompt' => 'Onjuiste koppeling?',
            'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
            'sort_order' => 1,
        ])
        ->assertSessionHasErrors('questionnaire_category_id');
});

test('managers can view the questionnaire library but cannot change it', function () {
    $manager = User::factory()->manager()->create();
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
    ]);

    $this->actingAs($manager)
        ->get(route('admin.questionnaires.index'))
        ->assertOk()
        ->assertSee($questionnaire->title)
        ->assertDontSee('Nieuwe questionnaire');

    $this->actingAs($manager)
        ->get(route('admin.questionnaires.create'))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('admin.questionnaires.edit', $questionnaire))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('admin.questionnaires.categories.create', $questionnaire))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('admin.questionnaires.questions.edit', [$questionnaire, $question]))
        ->assertForbidden();
});

test('regular users cannot access the questionnaire module', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.questionnaires.index'))
        ->assertForbidden();
});
