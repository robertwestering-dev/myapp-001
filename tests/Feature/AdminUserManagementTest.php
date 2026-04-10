<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

test('admins can view the users list', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create();

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk()
        ->assertDontSee('Gebruikersoverzicht')
        ->assertSee($users[0]->email)
        ->assertSee('Export CSV')
        ->assertSee('Organisatie')
        ->assertSee('Rol')
        ->assertSee('Land')
        ->assertSee('admin-status-badge', false)
        ->assertSee('ghost-pill icon-button', false)
        ->assertSee('danger-pill icon-button icon-button--danger', false)
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
        ->assertSee(User::ROLE_USER_PRO)
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
        ->assertSee('NEE, annuleren')
        ->assertSee('confirm-card', false)
        ->assertSee('confirm-actions', false);
});

test('admins can create a new user', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Nova Org',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Nieuwe Gebruiker',
        'first_name' => 'Nieuw',
        'gender' => User::GENDER_FEMALE,
        'birth_date' => '1992-06-14',
        'city' => 'Amersfoort',
        'country' => 'Nederland',
        'email' => 'nieuwe@example.com',
        'role' => User::ROLE_USER,
        'org_id' => $organization->org_id,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $createdUser = User::query()->where('email', 'nieuwe@example.com')->firstOrFail();

    expect($createdUser->name)->toBe('Nieuwe Gebruiker');
    expect($createdUser->first_name)->toBe('Nieuw');
    expect($createdUser->gender)->toBe(User::GENDER_FEMALE);
    expect($createdUser->birth_date?->toDateString())->toBe('1992-06-14');
    expect($createdUser->city)->toBe('Amersfoort');
    expect($createdUser->country)->toBe('Nederland');
    expect($createdUser->role)->toBe(User::ROLE_USER);
    expect($createdUser->org_id)->toBe($organization->org_id);
    Notification::assertSentTo($createdUser, VerifyEmail::class);
});

test('admins can create a new pro user', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Pro Org',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Pro Gebruiker',
        'first_name' => 'Pro',
        'gender' => User::GENDER_MALE,
        'birth_date' => '1990-01-10',
        'city' => 'Utrecht',
        'country' => 'Nederland',
        'email' => 'pro@example.com',
        'role' => User::ROLE_USER_PRO,
        'org_id' => $organization->org_id,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    expect(User::query()->where('email', 'pro@example.com')->firstOrFail()->role)
        ->toBe(User::ROLE_USER_PRO);
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
        'first_name' => 'Nina',
        'gender' => User::GENDER_OTHER,
        'birth_date' => '1989-11-04',
        'city' => 'Zwolle',
        'country' => 'Nederland',
        'email' => 'nieuwe@example.com',
        'role' => User::ROLE_ADMIN,
        'org_id' => $organization->org_id,
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();

    expect($user->name)->toBe('Nieuwe Naam');
    expect($user->first_name)->toBe('Nina');
    expect($user->gender)->toBe(User::GENDER_OTHER);
    expect($user->birth_date?->toDateString())->toBe('1989-11-04');
    expect($user->city)->toBe('Zwolle');
    expect($user->country)->toBe('Nederland');
    expect($user->email)->toBe('nieuwe@example.com');
    expect($user->role)->toBe(User::ROLE_ADMIN);
    expect($user->org_id)->toBe($organization->org_id);
});

test('admins can delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $user));

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('admins cannot delete a user who is the contact person for an organization', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->manager()->create([
        'name' => 'Bram Contact',
    ]);
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
        'contact_id' => $contact->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $contact));

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHasErrors([
            'user' => __('hermes.admin.users.delete_is_contact', [
                'organization' => $organization->naam,
            ]),
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $contact->id,
    ]);
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
        ->assertSee('Actieve filters')
        ->assertSee('Zoek op naam of emailadres')
        ->assertSee('Zoeken')
        ->assertSee('Export CSV')
        ->assertSee('alpha@example.com')
        ->assertSee('Alpha Person')
        ->assertSee('admin-status-badge', false)
        ->assertDontSee('Beta Person');
});

test('admins can filter users by organization role and country', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Nova BV',
    ]);

    User::factory()->create([
        'name' => 'Matchende Gebruiker',
        'email' => 'match@example.com',
        'role' => User::ROLE_MANAGER,
        'country' => User::COUNTRY_NETHERLANDS,
        'org_id' => $organization->org_id,
    ]);

    User::factory()->create([
        'name' => 'Andere Rol',
        'email' => 'other-role@example.com',
        'role' => User::ROLE_USER,
        'country' => User::COUNTRY_NETHERLANDS,
        'org_id' => $organization->org_id,
    ]);

    User::factory()->create([
        'name' => 'Ander Land',
        'email' => 'other-country@example.com',
        'role' => User::ROLE_MANAGER,
        'country' => User::COUNTRY_BELGIUM,
        'org_id' => $organization->org_id,
    ]);

    User::factory()->create([
        'name' => 'Andere Organisatie',
        'email' => 'other-org@example.com',
        'role' => User::ROLE_MANAGER,
        'country' => User::COUNTRY_NETHERLANDS,
        'org_id' => $otherOrganization->org_id,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index', [
        'organization' => (string) $organization->org_id,
        'role' => User::ROLE_MANAGER,
        'country' => User::COUNTRY_NETHERLANDS,
    ]));

    $response->assertOk()
        ->assertSee('Actieve filters')
        ->assertSee('Organisatie: Atlas BV')
        ->assertSee('Rol: Beheerder')
        ->assertSee('Land: Nederland')
        ->assertSee('match@example.com')
        ->assertDontSee('other-role@example.com')
        ->assertDontSee('other-country@example.com')
        ->assertDontSee('other-org@example.com');
});

test('users index shows a clear empty state for filter combinations without matches', function () {
    $admin = User::factory()->admin()->create();

    User::factory()->create([
        'name' => 'Alpha Person',
        'email' => 'alpha@example.com',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['country' => User::COUNTRY_FRANCE]))
        ->assertOk()
        ->assertSee('Actieve filters')
        ->assertSee('Land: Frankrijk')
        ->assertSee('Er zijn geen gebruikers gevonden voor deze filtercombinatie.')
        ->assertSee('Reset')
        ->assertSee('Nieuwe gebruiker');
});

test('admins can export the users list to csv', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Export Org',
    ]);

    User::factory()->create([
        'name' => 'Anna Export',
        'email' => 'anna@example.com',
        'org_id' => $organization->org_id,
        'country' => User::COUNTRY_NETHERLANDS,
        'email_verified_at' => Carbon::parse('2026-03-01 10:15:00'),
    ]);

    User::factory()->create([
        'name' => 'Bram Export',
        'email' => 'bram@example.com',
        'role' => User::ROLE_ADMIN,
        'country' => User::COUNTRY_BELGIUM,
        'email_verified_at' => Carbon::parse('2026-03-02 11:30:00'),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.export', [
        'search' => 'Export',
        'organization' => (string) $organization->org_id,
        'country' => User::COUNTRY_NETHERLANDS,
    ]));

    $response->assertDownload('users.csv');

    $content = $response->streamedContent();

    expect($content)
        ->toContain('Naam,Emailadres,Rol,"Email verified"')
        ->toContain('"Anna Export",anna@example.com,User,"2026-03-01 10:15:00"')
        ->not->toContain('"Bram Export",bram@example.com,Admin,"2026-03-02 11:30:00"');
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
        ->assertSee('<option value="user_pro"', false)
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
