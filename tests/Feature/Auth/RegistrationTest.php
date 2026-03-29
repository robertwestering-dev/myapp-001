<?php

use App\Mail\NewAccountRegistered;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Password;
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
    Mail::fake();
    Notification::fake();

    $hermesOrganizationId = DB::table('organizations')
        ->where('naam', 'Hermes Results')
        ->value('org_id');

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
    expect(User::first()?->org_id)->toBe($hermesOrganizationId);
    Notification::assertSentTo(User::first(), VerifyEmail::class);
    Mail::assertSent(NewAccountRegistered::class, 'robert.van.westering@outlook.com');

    /** @var NewAccountRegistered $mail */
    $mail = Mail::sent(NewAccountRegistered::class)->first();

    expect($mail->envelope()->subject)->toBe('Nieuw account Hermes Results');
    expect($mail->name)->toBe('Test Gebruiker');
    expect($mail->email)->toBe('test@example.com');
    expect($mail->render())->toContain('Er is een nieuw account aangemaakt door Test Gebruiker (test@example.com).');
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

test('new users must register with a password of at least eight characters when production password defaults apply', function () {
    Password::defaults(fn (): Password => Password::min(8)
        ->mixedCase()
        ->letters()
        ->numbers()
        ->symbols()
        ->uncompromised());

    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Test Gebruiker',
        'email' => 'test@example.com',
        'password' => 'Aa1!aaa',
        'password_confirmation' => 'Aa1!aaa',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors('password');

    $this->assertGuest();
});

test('new users can register with an eight character password when production password defaults apply', function () {
    Notification::fake();

    Password::defaults(fn (): Password => Password::min(8)
        ->mixedCase()
        ->letters()
        ->numbers()
        ->symbols()
        ->uncompromised());

    $response = $this->post(route('register.store'), [
        'name' => 'Test Gebruiker',
        'email' => 'test@example.com',
        'password' => 'Aa1!aaaa',
        'password_confirmation' => 'Aa1!aaaa',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    Notification::assertSentTo(User::first(), VerifyEmail::class);
});
