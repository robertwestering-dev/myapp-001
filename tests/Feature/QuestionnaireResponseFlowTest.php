<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Carbon;

test('users see available questionnaires on the questionnaire library page', function () {
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
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(__('hermes.questionnaires.library_eyebrow'))
        ->assertSee('Werkbeleving')
        ->assertSee(__('hermes.questionnaires.start_questionnaire'))
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
        ->assertSee(__('hermes.questionnaire.instructions_text'))
        ->assertDontSee('Invulinstructie')
        ->assertSee('color: #fff;', false)
        ->assertSee('Stap 1 van 1')
        ->assertSee('user-page-heading', false)
        ->assertSee('user-section-heading', false)
        ->assertSee('user-action-row', false)
        ->assertSee('user-inline-meta', false)
        ->assertSee('user-info-grid', false)
        ->assertSee('user-info-card', false)
        ->assertSee('questionnaire-feedback', false)
        ->assertSee('user-feedback', false);

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
            'answers' => [
                $textQuestion->id => 'Druk maar goed te doen.',
                $choiceQuestion->id => 'Soms',
            ],
        ])
        ->assertRedirect();

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

    $response = $availability->responses()->firstOrFail();

    $this->actingAs($user)
        ->get(route('questionnaire-responses.results', $response))
        ->assertOk()
        ->assertSee(__('hermes.questionnaire.results.result_intro', ['datetime' => $response->submitted_at->format('d-m-Y H:i')]))
        ->assertSee('Analyse van uw resultaten')
        ->assertSee('Uw antwoorden zijn opgeslagen.');
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
            __('hermes.questionnaire.instructions_text'),
            'Stap 1 van 3',
            'Start',
        ])
        ->assertSee('topbar__menu', false)
        ->assertSee(route('questionnaires.index', absolute: false), false)
        ->assertSee('Vorige')
        ->assertSee('Volgende')
        ->assertSee('Indienen')
        ->assertSee('user-action-row', false)
        ->assertSee('data-questionnaire-step')
        ->assertSee('data-step-total="3"', false);
});

test('likert scale questions are rendered as a full-width horizontal scale', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Samenwerking',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Beleving',
    ]);

    QuestionnaireQuestion::factory()->likertScale()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Ik voel me gehoord binnen het team.',
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
        ->assertSee('Ik voel me gehoord binnen het team.')
        ->assertSee('likert-scale__track', false)
        ->assertSee('Helemaal oneens')
        ->assertSee('Helemaal eens');
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

test('users cannot access questionnaires in a different language than their active profile language', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'en',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'locale' => 'nl',
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
        ->assertForbidden();
});

test('questionnaire view shows when session locale is leading for users without a profile locale', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => null,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Work rhythm',
        'locale' => 'en',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->withSession(['locale' => 'en'])
        ->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee(__('hermes.questionnaire.instructions_text'))
        ->assertDontSee(__('hermes.questionnaire.active_language'))
        ->assertDontSee(__('hermes.questionnaire.active_language_session'));
});

test('switching to an unavailable locale from a questionnaire page redirects to the questionnaire overview', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'nl',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'locale' => 'nl',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->from(route('questionnaire-responses.show', $availability))
        ->actingAs($user)
        ->post(route('locale.update'), [
            'locale' => 'en',
        ])
        ->assertRedirect(route('questionnaires.index'));

    $this->followRedirects(
        $this->from(route('questionnaire-responses.show', $availability))
            ->actingAs($user)
            ->post(route('locale.update'), [
                'locale' => 'en',
            ])
    )
        ->assertOk()
        ->assertSee(__('hermes.questionnaire.locale_switch_unavailable'));
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

test('completed questionnaire responses are preserved when pro users start a new attempt', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->pro()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Afronding',
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Wat is uw definitieve antwoord?',
        'is_required' => true,
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $response = QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'submitted_at' => now()->subMinute(),
        'last_saved_at' => now()->subMinute(),
    ]);

    $response->answers()->create([
        'questionnaire_question_id' => $question->id,
        'answer' => 'Oorspronkelijk definitief antwoord',
    ]);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertDontSee(__('hermes.questionnaire.completed_locked_draft'))
        ->assertDontSee('Oorspronkelijk definitief antwoord')
        ->assertDontSee(__('hermes.questionnaire.resume_link'))
        ->assertSee('name="intent" value="draft"', false)
        ->assertSee('data-submit-step', false)
        ->assertSee('data-is-completed="false"', false);

    $this->actingAs($user)
        ->from(route('questionnaire-responses.show', $availability))
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'draft',
            'answers' => [
                $question->id => 'Poging tot overschrijven',
            ],
        ])
        ->assertRedirect(route('questionnaire-responses.show', $availability))
        ->assertSessionHasNoErrors();

    $response->refresh();

    expect($response->submitted_at)->not->toBeNull();
    expect(QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->count())->toBe(2);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $question->id,
        'answer' => 'Oorspronkelijk definitief antwoord',
    ]);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $question->id,
        'answer' => 'Poging tot overschrijven',
    ]);
});
