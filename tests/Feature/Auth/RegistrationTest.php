<?php

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyFeature(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('new users must register with a unique email address', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->from(route('register'))->post(route('register.store'), [
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
        'email' => 'geen-geldig-emailadres',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});
