<?php

use App\Models\AdminActivityLog;
use App\Models\BlogPost;
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

test('manager cannot access the audit log', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->get(route('admin.audit-logs.index'))
        ->assertForbidden();
});

test('admin portal shows the audit log card and count', function () {
    $admin = User::factory()->admin()->create();

    AdminActivityLog::create([
        'user_id' => $admin->getKey(),
        'action' => 'user.created',
        'description' => 'Test',
        'ip_address' => '127.0.0.1',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.portal'))
        ->assertOk()
        ->assertSee(__('hermes.admin_portal.audit_title'))
        ->assertSee(route('admin.audit-logs.index', absolute: false), false);
});

test('admin menu contains the audit log link', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.portal'))
        ->assertOk()
        ->assertSee(route('admin.audit-logs.index', absolute: false), false)
        ->assertSee(__('hermes.admin_menu.audit_logs'));
});

test('pro upgrade creates an audit log entry', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user)->post(route('pro-upgrade.store'));

    expect(AdminActivityLog::query()->where('action', 'user.pro_upgrade')->exists())->toBeTrue();
});

test('role change creates a dedicated audit log entry', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($admin)->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'role' => User::ROLE_USER_PRO,
        'org_id' => $user->org_id,
    ]);

    expect(AdminActivityLog::query()->where('action', 'user.role_changed')->exists())->toBeTrue();
});

test('no role change audit log when role stays the same', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($admin)->put(route('admin.users.update', $user), [
        'name' => 'Andere naam',
        'email' => $user->email,
        'role' => User::ROLE_USER,
        'org_id' => $user->org_id,
    ]);

    expect(AdminActivityLog::query()->where('action', 'user.role_changed')->exists())->toBeFalse();
});

test('blog post creation creates an audit log entry', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), [
        'slug' => 'test-auditlog-blogpost',
        'title' => ['nl' => 'Test auditlog blogpost', 'en' => '', 'de' => ''],
        'excerpt' => ['nl' => 'Korte samenvatting', 'en' => '', 'de' => ''],
        'content' => ['nl' => 'Inhoud van de blogpost.', 'en' => '', 'de' => ''],
        'is_published' => false,
    ]);

    expect(AdminActivityLog::query()->where('action', 'blog_post.created')->exists())->toBeTrue();
});

test('blog post deletion creates an audit log entry', function () {
    $admin = User::factory()->admin()->create();
    $blogPost = BlogPost::factory()->create();

    $this->actingAs($admin)->delete(route('admin.blog-posts.destroy', $blogPost));

    expect(AdminActivityLog::query()->where('action', 'blog_post.deleted')->exists())->toBeTrue();
});
