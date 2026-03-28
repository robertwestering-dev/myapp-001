<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Carbon;

test('admins can view the users list', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk()
        ->assertSee('Gebruikersoverzicht')
        ->assertSee($users[0]->email)
        ->assertSee('Export CSV')
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results')
        ->assertSee('aria-label="Wijzig', false)
        ->assertSee('aria-label="Verwijder', false);
});

test('admins can view the create user page', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.create'));

    $response->assertOk()
        ->assertSee('Nieuwe gebruiker')
        ->assertSee('Gebruiker toevoegen')
        ->assertSee('Organisatie')
        ->assertSee(User::ROLE_MANAGER)
        ->assertSee($organization->naam)
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('admins must confirm before deleting a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create([
        'name' => 'Te Verwijderen',
        'email' => 'delete-me@example.com',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.confirm-delete', $user));

    $response->assertOk()
        ->assertSee('Wilt u deze gebruiker echt verwijderen?')
        ->assertSee('Te Verwijderen')
        ->assertSee('delete-me@example.com')
        ->assertSee('JA, verwijderen')
        ->assertSee('NEE, annuleren');
});

test('admins can create a new user', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Nova Org',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Nieuwe Gebruiker',
        'email' => 'nieuwe@example.com',
        'role' => User::ROLE_USER,
        'org_id' => $organization->org_id,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Nieuwe Gebruiker',
        'email' => 'nieuwe@example.com',
        'role' => User::ROLE_USER,
        'org_id' => $organization->org_id,
    ]);
});

test('admins can update a user', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Gewijzigde Organisatie',
    ]);
    $user = User::factory()->create([
        'name' => 'Oude Naam',
        'email' => 'oude@example.com',
        'role' => User::ROLE_USER,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
        'name' => 'Nieuwe Naam',
        'email' => 'nieuwe@example.com',
        'role' => User::ROLE_ADMIN,
        'org_id' => $organization->org_id,
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nieuwe Naam',
        'email' => 'nieuwe@example.com',
        'role' => User::ROLE_ADMIN,
        'org_id' => $organization->org_id,
    ]);
});

test('admins can delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $user));

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('users list is paginated with fifteen users per page and sorted by name', function () {
    $admin = User::factory()->admin()->create([
        'name' => 'AAA Admin',
    ]);

    collect(range(1, 16))->each(function (int $number): void {
        User::factory()->create([
            'name' => sprintf('User %02d', $number),
            'email' => sprintf('user%02d@example.com', $number),
        ]);
    });

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk()
        ->assertSeeInOrder(['User 01', 'User 02', 'User 03'])
        ->assertSee('User 14')
        ->assertDontSee('User 15')
        ->assertSee('Resultaten 1 t/m 15 van 17');
});

test('admins can search users by name or email address', function () {
    $admin = User::factory()->admin()->create();

    User::factory()->create([
        'name' => 'Alpha Person',
        'email' => 'alpha@example.com',
    ]);

    User::factory()->create([
        'name' => 'Beta Person',
        'email' => 'beta@example.com',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'alpha@example.com']));

    $response->assertOk()
        ->assertSee('Alpha Person')
        ->assertDontSee('Beta Person');
});

test('admins can export the users list to csv', function () {
    $admin = User::factory()->admin()->create();

    User::factory()->create([
        'name' => 'Anna Export',
        'email' => 'anna@example.com',
        'email_verified_at' => Carbon::parse('2026-03-01 10:15:00'),
    ]);

    User::factory()->create([
        'name' => 'Bram Export',
        'email' => 'bram@example.com',
        'role' => User::ROLE_ADMIN,
        'email_verified_at' => Carbon::parse('2026-03-02 11:30:00'),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.export', ['search' => 'Export']));

    $response->assertDownload('users.csv');

    $content = $response->streamedContent();

    expect($content)
        ->toContain('Naam,Emailadres,Rol,"Email verified"')
        ->toContain('"Anna Export",anna@example.com,User,"2026-03-01 10:15:00"')
        ->toContain('"Bram Export",bram@example.com,Admin,"2026-03-02 11:30:00"');
});

test('non admins cannot access the users list or export', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.users.export'))
        ->assertForbidden();
});

test('non admins cannot create update or delete users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('admin.users.store'), [
            'name' => 'Verboden',
            'email' => 'verboden@example.com',
            'role' => User::ROLE_USER,
            'org_id' => Organization::query()->value('org_id'),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.users.edit', $otherUser))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.users.confirm-delete', $otherUser))
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('admin.users.update', $otherUser), [
            'name' => 'Verboden Update',
            'email' => 'verboden-update@example.com',
            'role' => User::ROLE_ADMIN,
            'org_id' => Organization::query()->value('org_id'),
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete(route('admin.users.destroy', $otherUser))
        ->assertForbidden();
});

test('guests are redirected to login for the users list', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});

test('managers only see users from their own organization', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Org',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Andere Org',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);
    $ownUser = User::factory()->create([
        'name' => 'Eigen Gebruiker',
        'email' => 'eigen@example.com',
        'org_id' => $organization->org_id,
    ]);
    $otherUser = User::factory()->create([
        'name' => 'Andere Gebruiker',
        'email' => 'ander@example.com',
        'org_id' => $otherOrganization->org_id,
    ]);

    $response = $this->actingAs($manager)->get(route('admin.users.index'));

    $response->assertOk()
        ->assertSee($ownUser->email)
        ->assertDontSee($otherUser->email);
});

test('managers cannot edit users from another organization', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);
    $otherUser = User::factory()->create([
        'org_id' => $otherOrganization->org_id,
    ]);

    $this->actingAs($manager)
        ->get(route('admin.users.edit', $otherUser))
        ->assertForbidden();
});

test('managers can only assign users inside their own organization', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Org',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Andere Org',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);

    $response = $this->actingAs($manager)->get(route('admin.users.create'));

    $response->assertOk()
        ->assertSee('Eigen Org')
        ->assertDontSee('Andere Org')
        ->assertSee('<option value="Beheerder"', false)
        ->assertDontSee('<option value="Admin"', false);

    $this->actingAs($manager)
        ->post(route('admin.users.store'), [
            'name' => 'Scoped User',
            'email' => 'scoped@example.com',
            'role' => User::ROLE_USER,
            'org_id' => $otherOrganization->org_id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertSessionHasErrors('org_id');
});
