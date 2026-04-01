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
        ->assertSee('Eerst concept opslaan.')
        ->assertSee(route('questionnaire-responses.resume', $response->resume_token), false);

    $this->actingAs($user)
        ->get(route('questionnaire-responses.resume', $response->resume_token))
        ->assertRedirect(route('questionnaire-responses.show', $availability));
});

test('conditional questions only become required when their display rule matches', function () {
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
        ->assertRedirect(route('questionnaire-responses.show', $availability));

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
        ->assertRedirect(route('questionnaire-responses.show', $availability));

    $this->assertDatabaseHas('questionnaire_response_answers', [
        'questionnaire_question_id' => $followUpQuestion->id,
        'answer' => 'Meer begeleiding bij prioriteiten.',
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
