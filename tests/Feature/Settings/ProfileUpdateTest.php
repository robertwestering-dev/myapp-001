<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee(route('dashboard', absolute: false), false)
        ->assertSee(__('hermes.dashboard.title'))
        ->assertSee(__('hermes.nav.questionnaires'))
        ->assertSee(__('hermes.nav.academy'))
        ->assertSee(__('hermes.nav.blog'))
        ->assertSee(__('hermes.nav.profile'))
        ->assertSee(__('hermes.dashboard.logout'))
        ->assertSee(__('hermes.settings.shell.nav_profile'))
        ->assertDontSee(__('hermes.settings.shell.page_subtitle'))
        ->assertDontSee('relative mb-6 w-full', false)
        ->assertDontSee('/settings/security', false)
        ->assertDontSee('/settings/appearance', false)
        ->assertSee('user-panel', false)
        ->assertSee('user-page-heading', false)
        ->assertSee(__('hermes.settings.profile.personal.title'))
        ->assertSee(__('hermes.settings.profile.password.title'))
        ->assertSee(__('hermes.settings.profile.verification.title'))
        ->assertSee(__('hermes.settings.profile.locale.badge'))
        ->assertSee(__('hermes.settings.profile.verification.badge_verified'))
        ->assertSee('user-info-grid', false)
        ->assertSee('user-info-card', false)
        ->assertSee('user-action-row', false)
        ->assertSee('class="pill"', false)
        ->assertDontSee('autofocus', false)
        ->assertSee('user-feedback user-feedback--status', false)
        ->assertSeeInOrder([
            __('hermes.settings.delete_account.prefix'),
            __('hermes.settings.delete_account.link'),
            __('hermes.settings.delete_account.suffix'),
        ], false)
        ->assertSee('class="delete-user-link"', false)
        ->assertSee('class="delete-user-link-wrap"', false)
        ->assertDontSee('Delete your account and all of its resources')
        ->assertDontSee('Delete account');
});

test('profile information can be updated and sends a verification mail when email changes', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('first_name', 'Test')
        ->set('gender', User::GENDER_FEMALE)
        ->set('birth_date', '1990-05-10')
        ->set('city', 'Utrecht')
        ->set('country', User::COUNTRY_NETHERLANDS)
        ->set('locale', 'en')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->first_name)->toEqual('Test');
    expect($user->gender)->toEqual(User::GENDER_FEMALE);
    expect($user->birth_date?->toDateString())->toEqual('1990-05-10');
    expect($user->city)->toEqual('Utrecht');
    expect($user->country)->toEqual(User::COUNTRY_NETHERLANDS);
    expect($user->locale)->toEqual('en');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
    Notification::assertSentTo($user->fresh(), VerifyEmail::class);
});

test('email verification status is unchanged when email address is unchanged', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('first_name', 'Test')
        ->set('gender', User::GENDER_MALE)
        ->set('birth_date', '1988-01-15')
        ->set('city', 'Amsterdam')
        ->set('country', User::COUNTRY_NETHERLANDS)
        ->set('locale', 'nl')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
    Notification::assertNothingSent();
});

test('profile locale can be cleared so the session locale remains leading', function () {
    $user = User::factory()->create([
        'locale' => 'nl',
    ]);

    $this->withSession(['locale' => 'en'])->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('first_name', 'Test')
        ->set('gender', User::GENDER_MALE)
        ->set('birth_date', '1988-01-15')
        ->set('city', 'Amsterdam')
        ->set('country', User::COUNTRY_NETHERLANDS)
        ->set('locale', '')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->fresh()->locale)->toBeNull();
    expect(session(config('locales.session_key', 'locale')))->toBe('en');
});

test('profile information validates extra profile fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->set('name', 'Test User')
        ->set('first_name', 'Test')
        ->set('gender', 'onbekend')
        ->set('birth_date', 'geen-datum')
        ->set('city', 'Rotterdam')
        ->set('country', 'Spanje')
        ->set('locale', 'fr')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation')
        ->assertHasErrors(['gender', 'birth_date', 'country', 'locale']);
});

test('password can be updated from the profile page', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct current password must be provided to update password from the profile page', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});

test('user can anonymize their account and is logged out afterwards', function () {
    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'first_name' => 'Jane',
        'email' => 'jane@example.com',
        'city' => 'Utrecht',
        'country' => 'Nederland',
        'gender' => User::GENDER_FEMALE,
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.delete-user-modal')
        ->call('anonymizeUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $user->refresh();

    expect($user->name)->toBe((string) $user->id);
    expect($user->first_name)->toBe((string) $user->id);
    expect($user->email)->toBe("deleted-user+{$user->id}@hermesresults.com");
    expect($user->city)->toBe('Utrecht');
    expect($user->country)->toBe('Nederland');
    expect($user->gender)->toBe(User::GENDER_FEMALE);
    expect($user->email_verified_at)->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('anonymized email addresses remain unique per deleted user', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    $firstUser->anonymizeForStatistics();
    $secondUser->anonymizeForStatistics();

    expect($firstUser->fresh()->email)->toBe("deleted-user+{$firstUser->id}@hermesresults.com");
    expect($secondUser->fresh()->email)->toBe("deleted-user+{$secondUser->id}@hermesresults.com");
    expect($firstUser->fresh()->email)->not->toBe($secondUser->fresh()->email);
});

test('profile page shows verification resend guidance for unverified email addresses', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee(__('hermes.settings.profile.verification.badge_unverified'))
        ->assertSee(__('hermes.settings.profile.verification.resend'));
});

test('verification notification can be resent from the profile page', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect(route('profile.edit'));

    Notification::assertSentTo($user->fresh(), VerifyEmail::class);
});

test('admin can enable two factor authentication from the profile page', function () {
    $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

    $user = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::settings.profile')
        ->call('enableTwoFactor')
        ->assertHasNoErrors()
        ->assertSee('2FA is ingeschakeld. Scan nu de QR-code en bevestig met je verificatiecode.')
        ->assertSee('Sla ook deze herstelcodes meteen op.')
        ->assertSee('<code', false)
        ->assertSee('2FA bevestigen');

    expect($user->fresh()->two_factor_secret)->not->toBeNull();
    expect($user->fresh()->two_factor_confirmed_at)->toBeNull();
});

test('anonymize confirmation modal is shown on the profile page', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Als u doorgaat, dan worden uw gegevens verwijderd, en kunt u niet meer inloggen!')
        ->assertSee('Weet u zeker dat u uw account wilt verwijderen?')
        ->assertSee(__('hermes.settings.delete_account.cancel'))
        ->assertSee(__('hermes.settings.delete_account.confirm'))
        ->assertSee('delete-user-modal-actions', false)
        ->assertSee('user-action-row', false)
        ->assertSee('pill pill--neutral', false)
        ->assertSee('data-test="confirm-delete-user-button"', false);
});
