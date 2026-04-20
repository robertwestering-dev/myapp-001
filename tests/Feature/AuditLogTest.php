<?php

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('creating a user creates an audit log entry', function () {
    $admin = User::factory()->admin()->create();
    $orgId = DB::table('organizations')->where('naam', 'Hermes Results')->value('org_id');

    $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Test Gebruiker',
        'email' => 'testgebruiker@example.com',
        'role' => User::ROLE_USER,
        'org_id' => $orgId,
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ]);

    expect(AdminActivityLog::query()->where('action', 'user.created')->exists())->toBeTrue();
});

test('updating a user creates an audit log entry', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->put(route('admin.users.update', $user), [
        'name' => 'Bijgewerkte Naam',
        'email' => $user->email,
        'role' => $user->role,
        'org_id' => $user->org_id,
    ]);

    expect(AdminActivityLog::query()->where('action', 'user.updated')->exists())->toBeTrue();
});

test('deleting a user creates an audit log entry', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)->delete(route('admin.users.destroy', $user));

    expect(AdminActivityLog::query()->where('action', 'user.deleted')->exists())->toBeTrue();
});

test('creating an organization creates an audit log entry', function () {
    $admin = User::factory()->admin()->create();
    $contact = User::factory()->manager()->create();

    $this->actingAs($admin)->post(route('admin.organizations.store'), [
        'naam' => 'Audit Test Organisatie',
        'adres' => 'Teststraat 1',
        'postcode' => '1234 AB',
        'plaats' => 'Amsterdam',
        'land' => 'Nederland',
        'telefoon' => '+31 20 123 4567',
        'contact_id' => $contact->id,
    ]);

    expect(AdminActivityLog::query()->where('action', 'organization.created')->exists())->toBeTrue();
});

test('admin can view the audit log index', function () {
    $admin = User::factory()->admin()->create();

    AdminActivityLog::create([
        'user_id' => $admin->getKey(),
        'action' => 'user.created',
        'description' => 'Gebruiker aangemaakt: Jan Jansen (jan@example.com)',
        'ip_address' => '127.0.0.1',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.audit-logs.index'))
        ->assertOk()
        ->assertSee('user.created')
        ->assertSee('Jan Jansen');
});
