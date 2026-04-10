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
            'org_ids' => [$organization->org_id],
            'available_from_by_org' => [
                $organization->org_id => '2026-04-10',
            ],
            'is_active_by_org' => [
                $organization->org_id => '1',
            ],
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $availability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $organization->org_id)
        ->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.questionnaires.availability.update', [$questionnaire, $availability]), [
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

test('admins can update a questionnaire availability for a specific organization', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Focus Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'available_from' => '2026-04-10',
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.availability.edit', [$questionnaire, $availability]))
        ->assertOk()
        ->assertSee('Focus Org')
        ->assertDontSee('Alle organisaties')
        ->assertDontSee('>Organisatie<', false)
        ->assertSee('Extra organisaties koppelen');

    $this->actingAs($admin)
        ->put(route('admin.questionnaires.availability.update', [$questionnaire, $availability]), [
            'available_from' => '2026-04-12',
            'available_until' => '2026-05-12',
            'is_active' => '0',
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $availability->refresh();

    expect($availability->org_id)->toBe($organization->org_id);
    expect($availability->available_from?->toDateString())->toBe('2026-04-12');
    expect($availability->available_until?->toDateString())->toBe('2026-05-12');
    expect($availability->is_active)->toBeFalse();
});

test('admins can make a questionnaire available for multiple organizations in one action', function () {
    $admin = User::factory()->admin()->create();
    $firstOrganization = Organization::factory()->create();
    $secondOrganization = Organization::factory()->create();
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Meervoudige scan',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_ids' => [$firstOrganization->org_id, $secondOrganization->org_id],
            'available_from_by_org' => [
                $firstOrganization->org_id => '2026-04-10',
                $secondOrganization->org_id => '2026-04-15',
            ],
            'available_until_by_org' => [
                $firstOrganization->org_id => '2026-05-10',
                $secondOrganization->org_id => '2026-05-15',
            ],
            'is_active_by_org' => [
                $firstOrganization->org_id => '1',
            ],
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $this->assertDatabaseHas('organization_questionnaires', [
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('organization_questionnaires', [
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $secondOrganization->org_id,
        'is_active' => false,
    ]);

    $firstAvailability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $firstOrganization->org_id)
        ->firstOrFail();

    $secondAvailability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $secondOrganization->org_id)
        ->firstOrFail();

    expect($firstAvailability->available_from?->toDateString())->toBe('2026-04-10');
    expect($firstAvailability->available_until?->toDateString())->toBe('2026-05-10');
    expect($secondAvailability->available_from?->toDateString())->toBe('2026-04-15');
    expect($secondAvailability->available_until?->toDateString())->toBe('2026-05-15');
});

test('admins can still open the create screen to add extra organization links after an existing availability', function () {
    $admin = User::factory()->admin()->create();
    $firstOrganization = Organization::factory()->create([
        'naam' => 'Eerste Org',
    ]);
    $secondOrganization = Organization::factory()->create([
        'naam' => 'Tweede Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.index'))
        ->assertOk()
        ->assertSee('Extra organisaties koppelen');

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.availability.create', $questionnaire))
        ->assertOk()
        ->assertDontSee('Alle organisaties')
        ->assertSee('Tweede Org');

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_ids' => [$secondOrganization->org_id],
            'available_from_by_org' => [
                $secondOrganization->org_id => '2026-04-15',
            ],
            'available_until_by_org' => [
                $secondOrganization->org_id => '2026-05-20',
            ],
            'is_active_by_org' => [
                $secondOrganization->org_id => '1',
            ],
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $this->assertDatabaseHas('organization_questionnaires', [
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
    ]);

    $secondAvailability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $secondOrganization->org_id)
        ->firstOrFail();

    expect($secondAvailability->available_from?->toDateString())->toBe('2026-04-15');
    expect($secondAvailability->available_until?->toDateString())->toBe('2026-05-20');
});

test('admins can add extra organizations from the edit page with their own dates', function () {
    $admin = User::factory()->admin()->create();
    $firstOrganization = Organization::factory()->create([
        'naam' => 'Alpha Org',
    ]);
    $secondOrganization = Organization::factory()->create([
        'naam' => 'Beta Org',
    ]);
    $thirdOrganization = Organization::factory()->create([
        'naam' => 'Gamma Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
    ]);
    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $thirdOrganization->org_id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.availability.edit', [$questionnaire, $availability]))
        ->assertOk()
        ->assertSee('Beschikbaarheid huidige koppeling')
        ->assertSee('Extra organisaties koppelen')
        ->assertSee('Beta Org')
        ->assertDontSee('Gamma Org')
        ->assertDontSee('>Organisatie<', false);

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_ids' => [$secondOrganization->org_id],
            'available_from_by_org' => [
                $secondOrganization->org_id => '2026-06-01',
            ],
            'available_until_by_org' => [
                $secondOrganization->org_id => '2026-06-30',
            ],
            'is_active_by_org' => [
                $secondOrganization->org_id => '1',
            ],
        ])
        ->assertRedirect(route('admin.questionnaires.index'));

    $this->assertDatabaseHas('organization_questionnaires', [
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $secondOrganization->org_id,
        'is_active' => true,
    ]);

    $secondAvailability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $secondOrganization->org_id)
        ->firstOrFail();

    expect($secondAvailability->available_from?->toDateString())->toBe('2026-06-01');
    expect($secondAvailability->available_until?->toDateString())->toBe('2026-06-30');
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

test('inactive questionnaires cannot be saved as actively available for an organization', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create();
    $questionnaire = Questionnaire::factory()->create([
        'is_active' => false,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.store', $questionnaire), [
            'org_ids' => [$organization->org_id],
            'available_from_by_org' => [
                $organization->org_id => '2026-04-01',
            ],
            'is_active_by_org' => [
                $organization->org_id => '1',
            ],
        ])
        ->assertSessionHasErrors('org_ids');

    $this->assertDatabaseMissing('organization_questionnaires', [
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
        'is_active' => true,
    ]);
});

test('admins can only select organizations that are not already linked on the edit page', function () {
    $admin = User::factory()->admin()->create();
    $firstOrganization = Organization::factory()->create([
        'naam' => 'Eerste Org',
    ]);
    $secondOrganization = Organization::factory()->create([
        'naam' => 'Tweede Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
    ]);

    OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $secondOrganization->org_id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.availability.edit', [$questionnaire, $availability]))
        ->assertOk()
        ->assertDontSee('Tweede Org');
});

test('edit page shows a clear message when no extra organizations are available to link', function () {
    $admin = User::factory()->admin()->create();
    $firstOrganization = Organization::factory()->create([
        'naam' => 'Eerste Org',
    ]);
    $secondOrganization = Organization::factory()->create([
        'naam' => 'Tweede Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
    ]);

    Organization::query()->pluck('org_id')->each(function (int $organizationId) use ($questionnaire): void {
        OrganizationQuestionnaire::query()->firstOrCreate([
            'questionnaire_id' => $questionnaire->id,
            'org_id' => $organizationId,
        ]);
    });

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.availability.edit', [$questionnaire, $availability]))
        ->assertOk()
        ->assertSee('Er zijn geen extra organisaties meer beschikbaar om te koppelen.')
        ->assertSee('Eerste Org')
        ->assertSee('Tweede Org');
});

test('questionnaire index shows availability as one row per organization with dates and actions', function () {
    $admin = User::factory()->admin()->create();
    $firstOrganization = Organization::factory()->create([
        'naam' => 'Atlas Org',
    ]);
    $secondOrganization = Organization::factory()->create([
        'naam' => 'Beacon Org',
    ]);
    $thirdOrganization = Organization::factory()->create([
        'naam' => 'Comet Org',
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Beschikbaarheidsmatrix',
    ]);

    $firstAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $firstOrganization->org_id,
        'available_from' => '2026-04-10',
        'available_until' => '2026-05-10',
        'is_active' => true,
    ]);

    $secondAvailability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $secondOrganization->org_id,
        'available_from' => '2026-06-01',
        'available_until' => null,
        'is_active' => false,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.questionnaires.index'))
        ->assertOk()
        ->assertSee('Beschikbaarheidsmatrix')
        ->assertSee('Atlas Org')
        ->assertSee('Beacon Org')
        ->assertSee('Comet Org')
        ->assertSee('10-04-2026')
        ->assertSee('10-05-2026')
        ->assertSee('01-06-2026')
        ->assertSee('Niet gekoppeld')
        ->assertSee('title="Wijzigen"', false)
        ->assertSee('title="Verwijderen"', false)
        ->assertSee('title="Activeren"', false)
        ->assertSee('title="Deactiveren"', false)
        ->assertSee(route('admin.questionnaires.availability.edit', [$questionnaire, $firstAvailability]), false)
        ->assertSee(route('admin.questionnaires.availability.edit', [$questionnaire, $secondAvailability]), false);
});

test('admins can activate and deactivate questionnaire availability directly from the index', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Toggle Org',
    ]);
    $questionnaire = Questionnaire::factory()->create();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.toggle', [$questionnaire, $organization]))
        ->assertRedirect(route('admin.questionnaires.index'));

    $availability = OrganizationQuestionnaire::query()
        ->where('questionnaire_id', $questionnaire->id)
        ->where('org_id', $organization->org_id)
        ->firstOrFail();

    expect($availability->is_active)->toBeTrue();

    $this->actingAs($admin)
        ->post(route('admin.questionnaires.availability.toggle', [$questionnaire, $organization]))
        ->assertRedirect(route('admin.questionnaires.index'));

    $availability->refresh();

    expect($availability->is_active)->toBeFalse();
});
