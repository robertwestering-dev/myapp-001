<?php

use App\Models\User;

test('admin without 2FA is redirected to the 2FA notice page', function () {
    $admin = User::factory()->admin()->create([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.portal'))
        ->assertRedirect(route('admin.two-factor.notice'));
});

test('manager without 2FA is redirected to the 2FA notice page', function () {
    $manager = User::factory()->manager()->create([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ]);

    $this->actingAs($manager)
        ->get(route('admin.portal'))
        ->assertRedirect(route('admin.two-factor.notice'));
});

test('admin with confirmed 2FA can access the admin portal', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.portal'))
        ->assertOk();
});

test('the 2FA notice page is accessible without 2FA set up', function () {
    $admin = User::factory()->admin()->create([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.two-factor.notice'))
        ->assertOk()
        ->assertSee('2FA instellen in profielbeheer');
});

test('regular users are not affected by the 2FA enforcement middleware', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.portal'))
        ->assertForbidden();
});
