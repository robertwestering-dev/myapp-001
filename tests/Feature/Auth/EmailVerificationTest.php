<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyFeature(Features::emailVerification());
});

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk();
});

test('email verification mail uses hermes results branding', function () {
    $user = User::factory()->unverified()->create();
    $notification = new VerifyEmail;
    $mail = $notification->toMail($user);
    $renderedMail = (string) $mail->render();

    expect(config('app.name'))->toBe('Hermes Results');
    expect(config('mail.from.address'))->toBe('support@hermesresults.com');
    expect(config('mail.from.name'))->toBe('Hermes Results');
    expect($renderedMail)
        ->toContain('Hermes Results')
        ->not->toContain('notification-logo-v2.1.png')
        ->not->toContain('>Laravel<');
});

test('verification mail uses the public signed verification route', function () {
    Notification::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Nieuwe Gebruiker',
        'email' => 'nieuw@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'nieuw@example.com')->firstOrFail();

    Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification, array $channels) use ($user) {
        $mail = $notification->toMail($user);
        $renderedMail = (string) $mail->render();

        expect($channels)->toContain('mail');
        expect($renderedMail)->toContain('/verify-email/'.$user->id.'/'.sha1($user->email));

        return true;
    });
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('guests can verify email from the signed mail link', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.public',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    $this->assertAuthenticatedAs($user);
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')],
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user)->get($verificationUrl)
        ->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertNotDispatched(Verified::class);
});
