<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyFeature(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('Hermes Results')
        ->assertSee('Create Access')
        ->assertSee('Create account')
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('new users can register', function () {
    Notification::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Test Gebruiker',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(User::first()?->name)->toBe('Test Gebruiker');
    expect(User::first()?->role)->toBe(User::ROLE_USER);
    Notification::assertSentTo(User::first(), VerifyEmail::class);
});

test('new users must register with a unique email address', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Test Gebruiker',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('new users must register with a valid email address', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Test Gebruiker',
        'email' => 'geen-geldig-emailadres',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('new users must register with a name', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => '',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors('name');

    $this->assertGuest();
});
