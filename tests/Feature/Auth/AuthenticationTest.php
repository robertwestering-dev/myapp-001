<?php

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $announcement = __('hermes.auth.announcement', locale: 'nl');

    $response->assertOk()
        ->assertSee('Hermes Results')
        ->assertSee('Secure Access')
        ->assertSee('Log in')
        ->assertSeeText($announcement)
        ->assertSee(__('hermes.auth.login.hero_register'))
        ->assertDontSee(__('hermes.auth.login.hero_back'))
        ->assertSee('/images/hermes-results-logo.png')
        ->assertSee('(c) Copyright 2026 by Hermes Results');
});

test('login screen shows the localized announcement in english and german', function () {
    $this->withSession(['locale' => 'en'])
        ->get(route('login'))
        ->assertOk()
        ->assertSeeText(__('hermes.auth.announcement', locale: 'en'));

    $this->withSession(['locale' => 'de'])
        ->get(route('login'))
        ->assertOk()
        ->assertSeeText(__('hermes.auth.announcement', locale: 'de'));
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users see a dashboard login summary after successful authentication', function () {
    $organization = Organization::factory()->create();
    $previousLoginAt = Carbon::parse('2026-04-18 08:15:00');
    $submittedAt = Carbon::parse('2026-04-17 12:30:00');
    $user = User::factory()->create([
        'first_name' => 'Robert',
        'org_id' => $organization->org_id,
        'last_login_at' => $previousLoginAt,
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme Scan',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'submitted_at' => $submittedAt,
    ]);

    Carbon::setTestNow(Carbon::parse('2026-04-19 09:00:00'));

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    expect($user->refresh()->last_login_at?->format('Y-m-d H:i:s'))->toBe('2026-04-19 09:00:00');

    Carbon::setTestNow();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Welkom Robert')
        ->assertSee('Laatste login was op 18-04-2026 08:15')
        ->assertSee('Laatste zelftest was Werkritme Scan op 17-04-2026 12:30');

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Welkom Robert');
});

test('dashboard login summary encourages repeating self-tests after three months', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create([
        'first_name' => 'Robert',
        'org_id' => $organization->org_id,
        'last_login_at' => Carbon::parse('2026-04-18 08:15:00'),
    ]);
    $questionnaire = Questionnaire::factory()->create([
        'title' => 'Werkritme Scan',
    ]);
    $availability = OrganizationQuestionnaire::factory()->create([
        'questionnaire_id' => $questionnaire->id,
        'org_id' => $organization->org_id,
    ]);

    QuestionnaireResponse::factory()->create([
        'organization_questionnaire_id' => $availability->id,
        'user_id' => $user->id,
        'submitted_at' => Carbon::parse('2026-01-19 12:30:00'),
    ]);

    Carbon::setTestNow(Carbon::parse('2026-04-19 09:00:00'));

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    Carbon::setTestNow();

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('De zelftest Werkritme Scan heb je al meer dan 3 maanden niet gedaan. Doe hem opnieuw en volg je voortgang.')
        ->assertDontSee('Laatste zelftest was Werkritme Scan op 19-01-2026 12:30');
});

test('admins are redirected to the 2FA challenge after login', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.login'));
});

test('managers are redirected to the 2FA challenge after login', function () {
    $manager = User::factory()->manager()->create();

    $response = $this->post(route('login.store'), [
        'email' => $manager->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('two-factor.login'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('email');

    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users with unconfirmed two factor setup are not blocked from logging in', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
        'two_factor_confirmed_at' => null,
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.portal', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));

    $this->assertGuest();
});
