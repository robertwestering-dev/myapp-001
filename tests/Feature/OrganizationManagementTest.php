<?php

use App\Models\Organization;
use App\Models\User;

test('admins can view the organizations list', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->create([
        'name' => 'Anja Contact',
    ]);
    $organization = Organization::factory()->create([
        'naam' => 'Atlas BV',
        'contact_id' => $contact->id,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.organizations.index'));

    $response->assertOk()
        ->assertDontSee('Organisatieoverzicht')
        ->assertSee($organization->naam)
        ->assertSee('Anja Contact')
        ->assertSee('Nieuwe organisatie')
        ->assertSee('Resultaten 1 t/m 2 van 2')
        ->assertSee('ghost-pill icon-button', false)
        ->assertSee('danger-pill icon-button icon-button--danger', false)
        ->assertSee('aria-label="Wijzig '.$organization->naam.'"', false)
        ->assertSee('aria-label="Verwijder '.$organization->naam.'"', false);
});

test('admins can view the create organization page', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->manager()->create([
        'name' => 'Bram Contact',
    ]);
    $regularUser = User::factory()->create([
        'name' => 'Sanne Gebruiker',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.organizations.create'));

    $response->assertOk()
        ->assertSee('Nieuwe organisatie')
        ->assertSee('Organisatie toevoegen')
        ->assertSee('Bram Contact')
        ->assertDontSee('Sanne Gebruiker')
        ->assertSee('Kies een land')
        ->assertSee('Nederland')
        ->assertSee('België')
        ->assertSee('Duitsland')
        ->assertSee('Frankrijk')
        ->assertSee('UK')
        ->assertSee('VS')
        ->assertSee('Anders');
});

test('admins can create a new organization', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->manager()->create();

    $response = $this->actingAs($admin)->post(route('admin.organizations.store'), [
        'naam' => 'Nova Partners',
        'adres' => 'Markt 12',
        'postcode' => '1234 AB',
        'plaats' => 'Rotterdam',
        'land' => 'Nederland',
        'telefoon' => '+31 10 123 4567',
        'contact_id' => $contact->id,
    ]);

    $response->assertRedirect(route('admin.organizations.index'));

    $this->assertDatabaseHas('organizations', [
        'naam' => 'Nova Partners',
        'contact_id' => $contact->id,
    ]);
});

test('admins can update an organization', function () {
    $admin = User::factory()->admin()->create();
    $oldContact = User::factory()->manager()->create();
    $newContact = User::factory()->admin()->create([
        'name' => 'Nieuwe Contactpersoon',
    ]);
    $organization = Organization::factory()->create([
        'naam' => 'Oude Organisatie',
        'contact_id' => $oldContact->id,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.organizations.update', $organization), [
        'naam' => 'Nieuwe Organisatie',
        'adres' => 'Nieuwe Straat 5',
        'postcode' => '5678 CD',
        'plaats' => 'Utrecht',
        'land' => 'Nederland',
        'telefoon' => '+31 30 765 4321',
        'contact_id' => $newContact->id,
    ]);

    $response->assertRedirect(route('admin.organizations.index'));

    $this->assertDatabaseHas('organizations', [
        'org_id' => $organization->org_id,
        'naam' => 'Nieuwe Organisatie',
        'contact_id' => $newContact->id,
    ]);
});

test('admins cannot create an organization with a country outside the fixed list', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->manager()->create();

    $response = $this->from(route('admin.organizations.create'))
        ->actingAs($admin)
        ->post(route('admin.organizations.store'), [
            'naam' => 'Nova Partners',
            'adres' => 'Markt 12',
            'postcode' => '1234 AB',
            'plaats' => 'Rotterdam',
            'land' => 'Spanje',
            'telefoon' => '+31 10 123 4567',
            'contact_id' => $contact->id,
        ]);

    $response->assertRedirect(route('admin.organizations.create'))
        ->assertSessionHasErrors('land');

    $this->assertDatabaseMissing('organizations', [
        'naam' => 'Nova Partners',
    ]);
});

test('admins cannot create an organization with a regular user as contact person', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->create();

    $response = $this->from(route('admin.organizations.create'))
        ->actingAs($admin)
        ->post(route('admin.organizations.store'), [
            'naam' => 'Nova Partners',
            'adres' => 'Markt 12',
            'postcode' => '1234 AB',
            'plaats' => 'Rotterdam',
            'land' => 'Nederland',
            'telefoon' => '+31 10 123 4567',
            'contact_id' => $contact->id,
        ]);

    $response->assertRedirect(route('admin.organizations.create'))
        ->assertSessionHasErrors('contact_id');

    $this->assertDatabaseMissing('organizations', [
        'naam' => 'Nova Partners',
    ]);
});

test('admins can delete an organization without linked users', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Te Verwijderen BV',
        'contact_id' => $contact->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.organizations.destroy', $organization));

    $response->assertRedirect(route('admin.organizations.index'));
    $this->assertDatabaseMissing('organizations', [
        'org_id' => $organization->org_id,
    ]);
});

test('admins cannot delete Hermes Results as default organization', function () {
    $admin = User::factory()->admin()->create();
    $organization = Organization::query()->where('naam', 'Hermes Results')->firstOrFail();

    $response = $this->actingAs($admin)->delete(route('admin.organizations.destroy', $organization));

    $response->assertRedirect(route('admin.organizations.index'))
        ->assertSessionHasErrors('organization');

    $this->assertDatabaseHas('organizations', [
        'org_id' => $organization->org_id,
    ]);
});

test('admins cannot delete an organization with linked users', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->create();
    $organization = Organization::factory()->create([
        'naam' => 'Actieve Organisatie',
        'contact_id' => $contact->id,
    ]);
    User::factory()->create([
        'org_id' => $organization->org_id,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.organizations.destroy', $organization));

    $response->assertRedirect(route('admin.organizations.index'))
        ->assertSessionHasErrors('organization');

    $this->assertDatabaseHas('organizations', [
        'org_id' => $organization->org_id,
    ]);
});

test('non admins cannot manage organizations', function () {
    $user = User::factory()->create();
    $contact = User::factory()->create();
    $organization = Organization::factory()->create([
        'contact_id' => $contact->id,
    ]);

    $this->actingAs($user)
        ->get(route('admin.organizations.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.organizations.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('admin.organizations.store'), [
            'naam' => 'Verboden BV',
            'adres' => 'Straat 1',
            'postcode' => '1111 AA',
            'plaats' => 'Den Haag',
            'land' => 'Nederland',
            'telefoon' => '+31 70 111 1111',
            'contact_id' => $contact->id,
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.organizations.edit', $organization))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.organizations.confirm-delete', $organization))
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('admin.organizations.update', $organization), [
            'naam' => 'Verboden Update',
            'adres' => 'Straat 2',
            'postcode' => '2222 BB',
            'plaats' => 'Leiden',
            'land' => 'Nederland',
            'telefoon' => '+31 71 222 2222',
            'contact_id' => $contact->id,
        ])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete(route('admin.organizations.destroy', $organization))
        ->assertForbidden();
});

test('guests are redirected to login for the organizations list', function () {
    $response = $this->get(route('admin.organizations.index'));

    $response->assertRedirect(route('login'));
});

test('managers only see their own organization', function () {
    $organization = Organization::factory()->create([
        'naam' => 'Eigen Organisatie',
    ]);
    $otherOrganization = Organization::factory()->create([
        'naam' => 'Andere Organisatie',
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);

    $response = $this->actingAs($manager)->get(route('admin.organizations.index'));

    $response->assertOk()
        ->assertSee('Eigen Organisatie')
        ->assertDontSee('Andere Organisatie')
        ->assertDontSee('Nieuwe organisatie');
});

test('managers can edit their own organization but cannot create or delete organizations', function () {
    $contact = User::factory()->create();
    $organization = Organization::factory()->create([
        'contact_id' => $contact->id,
    ]);
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);

    $this->actingAs($manager)
        ->get(route('admin.organizations.edit', $organization))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('admin.organizations.create'))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('admin.organizations.confirm-delete', $organization))
        ->assertForbidden();
});

test('managers cannot view organizations from another organization', function () {
    $organization = Organization::factory()->create();
    $otherOrganization = Organization::factory()->create();
    $manager = User::factory()->manager()->create([
        'org_id' => $organization->org_id,
    ]);

    $this->actingAs($manager)
        ->get(route('admin.organizations.edit', $otherOrganization))
        ->assertForbidden();
});

test('organizations overview still offers a clear next step when only the default organization remains', function () {
    $admin = User::factory()->admin()->create();

    Organization::query()->where('naam', '!=', 'Hermes Results')->delete();

    $this->actingAs($admin)
        ->get(route('admin.organizations.index'))
        ->assertOk()
        ->assertSee('Hermes Results')
        ->assertSee('Nieuwe organisatie');
});
