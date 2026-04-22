<?php

use App\Models\User;
use Livewire\Livewire;

test('user with incomplete profile is redirected to profile page after login', function () {
    $user = User::factory()->incompleteProfile()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('profile.edit'));
});

test('user with complete profile is redirected to dashboard after login', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));
});

test('admin with incomplete profile is not redirected to profile page after login', function () {
    $user = User::factory()->admin()->incompleteProfile()->create();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    expect($response->headers->get('Location'))->not->toBe(route('profile.edit'));
});

test('profile incomplete prompt is shown when session flag is set', function () {
    $user = User::factory()->incompleteProfile()->create();

    $this->actingAs($user)
        ->withSession(['profile_incomplete_prompt' => true])
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Wil je even je profiel volledig invullen? Alvast dank!');
});

test('profile incomplete prompt is not shown without session flag', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('Wil je even je profiel volledig invullen? Alvast dank!');
});

test('profile incomplete prompt is shown on profile page after redirect from login', function () {
    $user = User::factory()->incompleteProfile()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('profile.edit'));

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Wil je even je profiel volledig invullen? Alvast dank!');
});

test('isProfileComplete returns false when required fields are missing', function () {
    $user = User::factory()->incompleteProfile()->create();

    expect($user->isProfileComplete())->toBeFalse();
});

test('isProfileComplete returns true when all profile fields are filled', function () {
    $user = User::factory()->create();

    expect($user->isProfileComplete())->toBeTrue();
});

test('profile page showProfilePrompt is true when session flag is set', function () {
    $user = User::factory()->incompleteProfile()->create();

    $this->actingAs($user)->withSession(['profile_incomplete_prompt' => true]);

    Livewire::test('pages::settings.profile')
        ->assertSet('showProfilePrompt', true)
        ->assertSee('Wil je even je profiel volledig invullen? Alvast dank!');
});
