<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\User;
use Illuminate\Support\Carbon;

test('users see available questionnaires on the dashboard', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Werkbeleving')
        ->assertSee('Open questionnaire')
        ->assertSee('Nog niet ingevuld');
});

test('users can submit and revisit questionnaire responses', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Inzetbaarheid',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Algemeen',
    ]);
    $textQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoe ervaart u uw werkweek?',
        'type' => QuestionnaireQuestion::TYPE_LONG_TEXT,
        'is_required' => true,
    ]);
    $choiceQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoeveel energie heeft u meestal?',
        'is_required' => true,
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Hoe ervaart u uw werkweek?')
        ->assertSee('Hoeveel energie heeft u meestal?');

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'answers' => [
                $textQuestion->id => 'Druk maar goed te doen.',
                $choiceQuestion->id => 'Soms',
            ],
        ])
        ->assertRedirect(route('questionnaire-responses.show', $availability));

    $this->assertDatabaseHas('questionnaire_responses', [
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $textQuestion->id,
        'answer' => 'Druk maar goed te doen.',
    ]);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $choiceQuestion->id,
        'answer' => 'Soms',
    ]);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Druk maar goed te doen.')
        ->assertSee('Laatst opgeslagen op');
});

test('users cannot access questionnaires from another organization or unavailable questionnaires', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create();

    $foreignAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $otherOrganization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $inactiveAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $foreignAvailability))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $inactiveAvailability))
        ->assertForbidden();
});

test('required questionnaire questions must be answered with valid values', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $requiredChoice = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'is_required' => true,
    ]);
    $requiredNumber = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'type' => QuestionnaireQuestion::TYPE_NUMBER,
        'is_required' => true,
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'answers' => [
                $requiredChoice->id => 'Onjuiste optie',
                $requiredNumber->id => 'geen getal',
            ],
        ])
        ->assertSessionHasErrors([
            "answers.{$requiredChoice->id}",
            "answers.{$requiredNumber->id}",
        ]);
});
