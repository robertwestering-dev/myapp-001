<?php

use App\Models\User;

test('guests are redirected to the login page for the admin portal', function () {
    $response = $this->get(route('admin.portal'));

    $response->assertRedirect(route('login'));
});

test('admins can visit the admin portal', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.portal'));

    $response->assertOk()
        ->assertSee('Admin-portal')
        ->assertSee($admin->email)
        ->assertSee('Beheer');
});

test('non admins cannot visit the admin portal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.portal'));

    $response->assertForbidden();
});
