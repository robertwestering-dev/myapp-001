<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Support\Carbon;

test('managers can make a questionnaire available for their own organization', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Org',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Inzetbaarheidsscan',
    ]);

    $this->actingAs($manager)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_id' => $organization->org_id,
            'available_from' => '2026-04-01',
            'available_until' => '2026-05-01',
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $this->assertDatabaseHas('organization_questionnaires', [
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'is_active' => true,
    ]);
});

test('managers cannot make a questionnaire available for another organization', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create();

    $this->actingAs($manager)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_id' => $otherOrganization->org_id,
            'available_from' => '2026-04-01',
            'is_active' => '1',
        ])
        ->assertSessionHasErrors('org_id');
});

test('admins can configure and update questionnaire availability for any organization', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Atlas Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_id' => $organization->org_id,
            'available_from' => '2026-04-10',
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $availability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $organization->org_id)
        ->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.questionnaires.availability.update', [$questionnaire, $availability]), [
            'org_id' => $organization->org_id,
            'available_from' => '2026-04-12',
            'available_until' => '2026-05-12',
            'is_active' => '0',
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $availability->refresh();

    expect($availability->available_from?->toDateString())->toBe('2026-04-12');
    expect($availability->available_until?->toDateString())->toBe('2026-05-12');
    expect($availability->is_active)->toBeFalse();
});

test('users do not see inactive questionnaires on the dashboard even when availability exists', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'org_id' => $organization->org_id,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Verborgen scan',
        'is_active' => false,
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
        ->assertDontSee('Verborgen scan');
});

test('admins see a warning when making an inactive questionnaire available', function () {
    $admin = User::factory()->admin()->create();
    $questionnaire = Questionnaire::factory()->create([
        'is_active' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.availability.create', $questionnaire))
        ->assertOk()
        ->assertSee('Deze questionnaire staat momenteel inactief in de bibliotheek.');
});
