<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Carbon;

test('guests are redirected to the login page when visiting the questionnaire library', function () {
    $this->get(route('questionnaires.index'))
        ->assertRedirect(route('login'));
});

test('users can view available questionnaires on the questionnaire library page', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkbeleving',
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
        'last_saved_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee(__('hermes.dashboard.title'))
        ->assertSee('Werkbeleving')
        ->assertSee('user-panel', false)
        ->assertSee('user-page-heading', false)
        ->assertSee('user-action-row', false)
        ->assertSee(__('hermes.dashboard.draft_badge'))
        ->assertSee(__('hermes.dashboard.resume_draft'))
        ->assertSee(__('hermes.dashboard.resume_ready'))
        ->assertSee('user-surface-card', false);
});

test('users see one available questionnaire in their profile language without separate questionnaire records', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
        'locale' => 'en',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme',
        'locale' => 'nl',
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
        ->assertSee('Werkritme');

    expect(Questionnaire::query()->where('title', 'Werkritme')->count())->toBe(1);
});

test('session locale keeps the same questionnaire availability visible when the user profile has no locale', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $user->forceFill(['locale' => null])->save();

    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme',
        'locale' => 'nl',
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->withSession(['locale' => 'en'])
        ->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee('Werkritme');
});

test('questionnaire library shows a clear empty state with a dashboard action when nothing is available', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee(__('hermes.dashboard.empty_title'))
        ->assertSee(__('hermes.dashboard.empty_text'))
        ->assertSee(__('hermes.questionnaires.empty_action'))
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee('user-guidance-card', false);
});

test('users only see questionnaires for their own organization', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $ownQuestionnaire = Questionnaire::factory()->create([
        'title' => 'Eigen scan',
    ]);
    $otherQuestionnaire = Questionnaire::factory()->create([
        'title' => 'Andere scan',
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $ownQuestionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $otherQuestionnaire->id,
        'org_id' => $otherOrganization->org_id,
        'available_from' => Carbon::today()->subDay()->toDateString(),
        'available_until' => Carbon::today()->addDay()->toDateString(),
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('questionnaires.index'))
        ->assertOk()
        ->assertSee('Eigen scan')
        ->assertDontSee('Andere scan');
});
