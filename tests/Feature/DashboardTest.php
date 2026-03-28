<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk()
        ->assertSee('Welkom: '.$user->email)
        ->assertSee('Logout')
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('home page uses the hermes favicon instead of the laravel default icons', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('rel="icon"', false)
        ->assertSee('/favicon.ico?v=', false)
        ->assertSee('rel="shortcut icon"', false)
        ->assertDontSee('/favicon.svg', false);
});

test('dashboard uses the hermes favicon instead of the laravel default icons', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('rel="icon"', false)
        ->assertSee('/favicon.ico?v=', false)
        ->assertSee('rel="shortcut icon"', false)
        ->assertDontSee('/favicon.svg', false);
});
