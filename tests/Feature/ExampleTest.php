<?php

use App\Models\User;

test('home page can be rendered', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertDontSee('U bent niet ingelogd')
        ->assertSee('We verbinden mensen, leren en technologie voor tastbare groei.')
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('guests can see the login link on the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee(route('login'))
        ->assertDontSee(route('register'))
        ->assertSee('Inloggen');
});

test('authenticated users still see the guest homepage text on the home page', function () {
    $response = $this->actingAs(User::factory()->create())->get(route('home'));

    $response->assertRedirect(route('dashboard'));
});
