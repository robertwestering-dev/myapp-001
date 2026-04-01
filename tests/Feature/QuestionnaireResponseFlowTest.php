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
        ->assertSee('Hoeveel energie heeft u meestal?')
        ->assertSee('Invulinstructie')
        ->assertSee('Stap 1 van 1');

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
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

test('questionnaire categories are shown as paginated steps', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Digitale samenwerking',
    ]);
    $firstCategory = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Start',
        'sort_order' => 1,
    ]);
    $secondCategory = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Vervolg',
        'sort_order' => 2,
    ]);
    $thirdCategory = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Afronding',
        'sort_order' => 3,
    ]);

    QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $firstCategory->id,
        'prompt' => 'Vraag uit start',
        'sort_order' => 1,
    ]);
    QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $secondCategory->id,
        'prompt' => 'Vraag uit vervolg',
        'sort_order' => 1,
    ]);
    QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $thirdCategory->id,
        'prompt' => 'Vraag uit afronding',
        'sort_order' => 1,
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
        ->assertSeeInOrder([
            'Invulinstructie',
            'Stap 1 van 3',
            'Start',
        ])
        ->assertSee('Vorige stap')
        ->assertSee('Volgende stap')
        ->assertSee('Antwoorden opslaan')
        ->assertSee('data-questionnaire-step')
        ->assertSee('data-step-total="3"', false);
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
            'intent' => 'submit',
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

test('required questions on earlier questionnaire pages must still be completed before submission', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $firstCategory = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'sort_order' => 1,
    ]);
    $secondCategory = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'sort_order' => 2,
    ]);
    $requiredFirstQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $firstCategory->id,
        'prompt' => 'Eerste verplichte vraag',
        'is_required' => true,
    ]);
    $requiredSecondQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $secondCategory->id,
        'prompt' => 'Tweede verplichte vraag',
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
        ->from(route('questionnaire-responses.show', $availability))
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
            'answers' => [
                $requiredSecondQuestion->id => 'Antwoord op de tweede pagina',
            ],
        ])
        ->assertRedirect(route('questionnaire-responses.show', $availability))
        ->assertSessionHasErrors([
            "answers.{$requiredFirstQuestion->id}",
        ]);
});
