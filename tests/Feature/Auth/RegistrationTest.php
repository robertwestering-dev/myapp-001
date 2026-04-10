<?php

use App\Mail\NewAccountRegistered;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyFeature(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $announcement = __('hermes.auth.announcement', locale: 'nl');

    $response->assertOk()
        ->assertSee('Hermes Results')
        ->assertSee('Create Access')
        ->assertSee('Create account')
        ->assertSeeText($announcement)
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('registration screen shows the localized announcement in english and german', function () {
    $this->withSession(['locale' => 'en'])
        ->get(route('register'))
        ->assertOk()
        ->assertSeeText(__('hermes.auth.announcement', locale: 'en'));

    $this->withSession(['locale' => 'de'])
        ->get(route('register'))
        ->assertOk()
        ->assertSeeText(__('hermes.auth.announcement', locale: 'de'));
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

    // Mock the HaveIBeenPwned API so tests are not dependent on an external service.
    // An empty response body means the password hash is not in the breach database.
    Http::fake([
        'api.pwnedpasswords.com/*' => Http::response('', 200),
    ]);

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
