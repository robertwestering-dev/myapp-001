<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Carbon;

test('users can save a questionnaire draft and resume it via a unique link', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Wat wilt u later aanvullen?',
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
            'intent' => 'draft',
            'answers' => [
                $question->id => 'Eerst concept opslaan.',
            ],
        ])
        ->assertRedirect(route('questionnaire-responses.show', $availability));

    $response = QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    expect($response->submitted_at)->toBeNull();
    expect($response->last_saved_at)->not->toBeNull();
    expect($response->resume_token)->not->toBeEmpty();

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Concept opslaan')
        ->assertSee('formnovalidate', false)
        ->assertSee('Stap 1 van 1')
        ->assertSee('user-action-row', false)
        ->assertSee('user-inline-meta', false)
        ->assertSee($category->title)
        ->assertSee('1 van 1 vragen in deze stap ingevuld')
        ->assertSee('Eerst concept opslaan.')
        ->assertSee(route('questionnaire-responses.resume', $response->resume_token), false);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.resume', $response->resume_token))
        ->assertRedirect(route('questionnaire-responses.show', $availability));
});

test('autosave stores draft progress and the active questionnaire step', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme',
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
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $secondCategory->id,
        'prompt' => 'Wat wilt u later aanvullen?',
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
        ->postJson(route('questionnaire-responses.store', $availability), [
            'intent' => 'autosave',
            'current_category_id' => $secondCategory->id,
            'answers' => [
                $question->id => 'Automatisch opgeslagen.',
            ],
        ])
        ->assertOk()
        ->assertJsonPath('step_label', 'Vervolg');

    $response = QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    expect($response->isDraft())->toBeTrue();
    expect($response->current_questionnaire_category_id)->toBe($secondCategory->id);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertSee('Automatisch opslaan staat aan terwijl u invult.')
        ->assertSee('data-initial-step="1"', false)
        ->assertSee('value="'.$secondCategory->id.'"', false);
});

test('autosave accepts temporarily invalid in-progress answers without failing', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Start',
        'sort_order' => 1,
    ]);
    $numberQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Hoeveel uren?',
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
        ->postJson(route('questionnaire-responses.store', $availability), [
            'intent' => 'autosave',
            'current_category_id' => $category->id,
            'answers' => [
                $numberQuestion->id => 'nog niet af',
            ],
        ])
        ->assertOk()
        ->assertJsonStructure(['message', 'last_saved_at']);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $numberQuestion->id,
        'answer' => 'nog niet af',
    ]);
});

test('pro users can autosave a new draft attempt after a completed questionnaire', function () {
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
        'prompt' => 'Definitief antwoord',
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
        'answer' => 'Bewaard antwoord',
    ]);

    $this->actingAs($user)
        ->postJson(route('questionnaire-responses.store', $availability), [
            'intent' => 'autosave',
            'current_category_id' => $category->id,
            'answers' => [
                $question->id => 'Nieuwe autosave poging',
            ],
        ])
        ->assertOk()
        ->assertJsonStructure(['message', 'last_saved_at']);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $question->id,
        'answer' => 'Bewaard antwoord',
    ]);

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $question->id,
        'answer' => 'Nieuwe autosave poging',
    ]);

    expect(QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->count())->toBe(2);
});

test('pro users can submit the same questionnaire multiple times and view every result', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->pro()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Herhaalbare scan',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Start',
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Wat is uw focus?',
        'is_required' => true,
    ]);

    Carbon::setTestNow(Carbon::parse('2026-04-18 09:00:00'));

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
                $question->id => 'Eerste meting',
            ],
        ])
        ->assertRedirect();

    $firstResponse = QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    Carbon::setTestNow(Carbon::parse('2026-04-19 10:30:00'));

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
            'answers' => [
                $question->id => 'Tweede meting',
            ],
        ])
        ->assertRedirect();

    $responses = QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->orderBy('submitted_at')
        ->get();

    expect($responses)->toHaveCount(2);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(__('hermes.questionnaires.completed_history_title'))
        ->assertSee('Ingevuld op 18-04-2026 09:00')
        ->assertSee('Ingevuld op 19-04-2026 10:30')
        ->assertSee(route('questionnaire-responses.results', $firstResponse), false)
        ->assertSee(route('questionnaire-responses.results', $responses->last()), false);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertOk()
        ->assertDontSee('Eerste meting')
        ->assertDontSee('Tweede meting');

    $this->actingAs($user)
        ->get(route('questionnaire-responses.results', $firstResponse))
        ->assertOk()
        ->assertSee('Herhaalbare scan')
        ->assertSee(__('hermes.questionnaire.results.generic_title'));

    Carbon::setTestNow();
});

test('regular users need pro to repeat a completed questionnaire', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Eenmalige scan',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'title' => 'Start',
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Wat is uw focus?',
        'is_required' => true,
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'submitted_at' => now()->subDay(),
    ])->answers()->create([
        'questionnaire_question_id' => $question->id,
        'answer' => 'Eerste meting',
    ]);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.show', $availability))
        ->assertRedirect(route('questionnaires.index'))
        ->assertSessionHas('pro_required_modal');

    $this->actingAs($user)
        ->withSession(['pro_required_modal' => true])
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(__('hermes.questionnaires.pro_required_title'))
        ->assertSee(__('hermes.questionnaires.pro_required_message'))
        ->assertSee(__('hermes.questionnaires.pro_required_close'));

    $this->actingAs($user)
        ->postJson(route('questionnaire-responses.store', $availability), [
            'intent' => 'autosave',
            'current_category_id' => $category->id,
            'answers' => [
                $question->id => 'Tweede poging',
            ],
        ])
        ->assertForbidden()
        ->assertJsonPath('message', __('hermes.questionnaires.pro_required_message'));

    expect(QuestionnaireResponse::query()
        ->where('organization_questionnaire_id', $availability->id)
        ->where('user_id', $user->id)
        ->count())->toBe(1);
});

test('conditional questions only become required when their display rule matches', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->pro()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $triggerQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Heeft u extra ondersteuning nodig?',
        'options' => ['Ja', 'Nee'],
        'is_required' => true,
    ]);
    $followUpQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Welke ondersteuning helpt het meest?',
        'is_required' => true,
        'display_condition_question_id' => $triggerQuestion->id,
        'display_condition_operator' => QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS,
        'display_condition_answer' => ['Ja'],
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
                $triggerQuestion->id => 'Nee',
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseMissing('questionnaire_response_answers', [
        'questionnaire_question_id' => $followUpQuestion->id,
    ]);

    $this->actingAs($user)
        ->from(route('questionnaire-responses.show', $availability))
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
            'answers' => [
                $triggerQuestion->id => 'Ja',
            ],
        ])
        ->assertRedirect(route('questionnaire-responses.show', $availability))
        ->assertSessionHasErrors([
            "answers.{$followUpQuestion->id}",
        ]);

    $this->actingAs($user)
        ->post(route('questionnaire-responses.store', $availability), [
            'intent' => 'submit',
            'answers' => [
                $triggerQuestion->id => 'Ja',
                $followUpQuestion->id => 'Meer begeleiding bij prioriteiten.',
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $followUpQuestion->id,
        'answer' => 'Meer begeleiding bij prioriteiten.',
    ]);
});

test('hidden conditional follow-up chains do not validate or persist stale answers', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $triggerQuestion = QuestionnaireQuestion::factory()->singleChoice()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Wilt u vervolgvragen zien?',
        'options' => ['Ja', 'Nee'],
        'is_required' => true,
    ]);
    $followUpQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Verborgen vervolgvraag',
        'is_required' => true,
        'display_condition_question_id' => $triggerQuestion->id,
        'display_condition_operator' => QuestionnaireQuestion::DISPLAY_CONDITION_EQUALS,
        'display_condition_answer' => ['Ja'],
    ]);
    $nestedFollowUpQuestion = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
        'prompt' => 'Verborgen vervolgvraag niveau 2',
        'is_required' => true,
        'display_condition_question_id' => $followUpQuestion->id,
        'display_condition_operator' => QuestionnaireQuestion::DISPLAY_CONDITION_ANSWERED,
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
                $triggerQuestion->id => 'Nee',
                $followUpQuestion->id => 'Deze waarde hoort verborgen te blijven.',
                $nestedFollowUpQuestion->id => 'Ook deze waarde mag niet worden opgeslagen.',
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('questionnaire_response_answers', [
        'questionnaire_question_id' => $followUpQuestion->id,
    ]);

    $this->assertDatabaseMissing('questionnaire_response_answers', [
        'questionnaire_question_id' => $nestedFollowUpQuestion->id,
    ]);
});

test('draft questionnaire responses are excluded from admin reports', function () {
    $organization = Organization::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Teamritme',
    ]);
    $category = QuestionnaireCategory::factory()->create([
        'questionnaire_id' => $questionnaire->id,
    ]);
    $question = QuestionnaireQuestion::factory()->create([
        'questionnaire_category_id' => $category->id,
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    QuestionnaireResponse::factory()->draft()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
    ])->answers()->create([
        'questionnaire_question_id' => $question->id,
        'answer' => 'Nog niet definitief',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.index'))
        ->assertOk()
        ->assertDontSee('Nog niet definitief')
        ->assertDontSee('Niet ingezonden');
});

test('admin reports can be filtered to draft questionnaire responses', function () {
    $organization = Organization::factory()->create();
    $admin = User::factory()->admin()->create();
    $draftUser = User::factory()->create([
        'org_id' => $organization->org_id,
        'name' => 'Draft Gebruiker',
        'email' => 'draft@example.com',
    ]);
    $completedUser = User::factory()->create([
        'org_id' => $organization->org_id,
        'name' => 'Voltooide Gebruiker',
        'email' => 'completed@example.com',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Teamritme',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    QuestionnaireResponse::factory()->draft()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $draftUser->id,
        'last_saved_at' => now(),
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $completedUser->id,
        'submitted_at' => now()->subHour(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaire-responses.index', ['response_state' => 'draft']))
        ->assertOk()
        ->assertSee('<div>Draft Gebruiker</div>', false)
        ->assertSee(__('hermes.reports.state_draft'))
        ->assertDontSee('<div>Voltooide Gebruiker</div>', false);
});
