<?php

use App\Models\User;

test('security settings route is no longer available', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/security')
        ->assertNotFound();
});

test('appearance settings route is no longer available', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/appearance')
        ->assertNotFound();
});

test('settings route redirects to the profile page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings')
        ->assertRedirect(route('profile.edit'));
});
