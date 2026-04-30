<?php

use App\Models\AdminActivityLog;
use App\Models\User;

test('regular user can upgrade to pro', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user)
        ->post(route('pro-upgrade.store'))
        ->assertRedirect(route('pro-upgrade.show'));

    expect($user->fresh()->role)->toBe(User::ROLE_USER_PRO);
});

test('pro upgrade logs audit entry', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user)
        ->post(route('pro-upgrade.store'));

    $this->assertDatabaseHas('admin_activity_logs', [
        'user_id' => $user->id,
        'action' => 'user.pro_upgrade',
    ]);
});

test('upgrade is idempotent: calling upgrade twice only upgrades once and logs once', function () {
    $user = User::factory()->create(['role' => User::ROLE_USER]);

    $this->actingAs($user)->post(route('pro-upgrade.store'));
    $this->actingAs($user)->post(route('pro-upgrade.store'));

    expect($user->fresh()->role)->toBe(User::ROLE_USER_PRO);

    expect(AdminActivityLog::where('user_id', $user->id)->where('action', 'user.pro_upgrade')->count())->toBe(1);
});

test('admin role is not affected by pro-upgrade endpoint', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('pro-upgrade.store'));

    expect($admin->fresh()->role)->toBe(User::ROLE_ADMIN);
});

test('manager role is not affected by pro-upgrade endpoint', function () {
    $manager = User::factory()->manager()->create();

    $this->actingAs($manager)
        ->post(route('pro-upgrade.store'));

    expect($manager->fresh()->role)->toBe(User::ROLE_MANAGER);
});

test('unauthenticated user is redirected from pro-upgrade post', function () {
    $this->post(route('pro-upgrade.store'))
        ->assertRedirect(route('login'));
});

test('unverified user cannot access pro-upgrade post', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('pro-upgrade.store'))
        ->assertRedirect(route('verification.notice'));
});
