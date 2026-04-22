<?php

use App\Concerns\ProfileValidationRules;
use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('components.layouts.hermes-dashboard')] #[Title('Profiel')] class extends Component {
    use PasswordValidationRules;
    use ProfileValidationRules;

    public string $name = '';
    public ?string $first_name = null;
    public ?string $gender = null;
    public ?string $birth_date = null;
    public ?string $city = null;
    public ?string $country = null;
    public ?string $locale = null;
    public string $email = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $showProfilePrompt = false;

    public function mount(): void
    {
        $user = $this->currentUser();

        $this->name = $user->name;
        $this->first_name = $user->first_name;
        $this->gender = $user->gender;
        $this->birth_date = $user->birth_date?->toDateString();
        $this->city = $user->city;
        $this->country = $user->country;
        $this->locale = $user->locale;
        $this->email = $user->email;
        $this->showProfilePrompt = session()->has('profile_incomplete_prompt');
    }

    public function updateProfileInformation(): void
    {
        $user = $this->currentUser();

        if ($this->locale === '') {
            $this->locale = null;
        }

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        Auth::setUser($user->fresh());

        if ($user->wasChanged('email')) {
            $user->sendEmailVerificationNotification();
            Session::flash('status', 'verification-link-sent');
        }

        $activeLocale = $user->locale
            ?? Session::get(config('locales.session_key', 'locale'))
            ?? config('app.locale');

        Session::put(config('locales.session_key', 'locale'), $activeLocale);
        app()->setLocale($activeLocale);

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ]);

        $this->currentUser()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }

    public string $twoFactorCode = '';

    public function enableTwoFactor(EnableTwoFactorAuthentication $enable): void
    {
        $user = $this->currentUser();

        $enable($user);
        Auth::setUser($user->fresh());
        Session::flash('status', 'two-factor-authentication-enabled');
        $this->dispatch('two-factor-updated');
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirm): void
    {
        $user = $this->currentUser();

        $confirm($user, $this->twoFactorCode);
        Auth::setUser($user->fresh());
        $this->twoFactorCode = '';
        Session::flash('status', 'two-factor-authentication-confirmed');
        $this->dispatch('two-factor-updated');
    }

    public function disableTwoFactor(DisableTwoFactorAuthentication $disable): void
    {
        $user = $this->currentUser();

        $disable($user);
        Auth::setUser($user->fresh());
        Session::flash('status', 'two-factor-authentication-disabled');
        $this->dispatch('two-factor-updated');
    }

    #[Computed]
    public function twoFactorEnabled(): bool
    {
        return ! is_null($this->currentUser()->two_factor_secret);
    }

    #[Computed]
    public function twoFactorPendingConfirmation(): bool
    {
        return $this->twoFactorEnabled && ! $this->twoFactorConfirmed;
    }

    #[Computed]
    public function twoFactorConfirmed(): bool
    {
        return ! is_null($this->currentUser()->two_factor_confirmed_at);
    }

    #[Computed]
    public function twoFactorQrCode(): ?string
    {
        if (! $this->twoFactorEnabled) {
            return null;
        }

        return $this->currentUser()->twoFactorQrCodeSvg();
    }

    #[Computed]
    public function recoveryCodes(): array
    {
        if (! $this->twoFactorEnabled) {
            return [];
        }

        return $this->currentUser()->recoveryCodes();
    }

    public function resendVerificationEmail(): void
    {
        $user = $this->currentUser();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            Session::flash('status', 'verification-link-sent');
        }
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        $user = $this->currentUser();

        return $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        $user = $this->currentUser();

        return ! $user instanceof MustVerifyEmail
            || ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail());
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user()->fresh();

        return $user;
    }
}; ?>

<section class="profile-page">
    <style>
        .profile-page {
            display: grid;
            gap: 28px;
        }

        .profile-card {
            padding: 40px;
        }

        .profile-heading {
            display: grid;
            gap: 10px;
        }

        .profile-heading h1,
        .profile-heading h2 {
            margin: 0;
            font-size: clamp(1.4rem, 2.4vw, 2.5rem);
            line-height: 1.05;
            color: #16211d;
        }

        .profile-heading p,
        .verification-note {
            margin: 0;
            color: #5a6762;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            line-height: 1.6;
        }

        .profile-form {
            display: grid;
            gap: 24px;
            margin-top: 32px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 24px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field--full {
            grid-column: 1 / -1;
        }

        .field label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.92rem;
            font-weight: 700;
            color: #20453a;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(22, 33, 29, 0.12);
            background: rgba(255, 255, 255, 0.92);
            color: #16211d;
            font: inherit;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
        }

        .field input:focus,
        .field select:focus {
            outline: 2px solid rgba(217, 106, 43, 0.22);
            border-color: rgba(217, 106, 43, 0.5);
        }

        .field-error {
            color: #b14d1a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
        }

        .verification-block {
            display: grid;
            gap: 12px;
            padding: 18px 20px;
            margin-top: 12px;
            border-radius: 22px;
            background: rgba(217, 106, 43, 0.08);
            border: 1px solid rgba(217, 106, 43, 0.16);
        }

        .verification-link {
            width: fit-content;
            padding: 0;
            border: 0;
            background: none;
            color: #a84a19;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
        }

        .profile-delete {
            margin-top: 28px;
            padding-top: 28px;
            border-top: 1px solid rgba(22, 33, 29, 0.12);
        }

        .profile-section {
            margin-top: 28px;
            padding-top: 28px;
            border-top: 1px solid rgba(22, 33, 29, 0.12);
        }

        .tfa-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 999px;
            border: 1px solid rgba(22, 33, 29, 0.18);
            background: rgba(255, 255, 255, 0.72);
            color: #16211d;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }

        .tfa-btn--primary {
            background: #20453a;
            border-color: #20453a;
            color: #fff;
        }

        .tfa-btn--danger {
            color: #b14d1a;
            border-color: rgba(177, 77, 26, 0.3);
        }

        .profile-upgrade-card__action {
            justify-self: start;
            margin-top: 6px;
            text-decoration: none;
        }

        @media (max-width: 780px) {
            .profile-card {
                padding: 28px;
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <x-pages::settings.layout
        :eyebrow="__('hermes.settings.shell.nav_profile')"
    >
        <section class="profile-card user-panel">
            <x-user-info-grid columns="2" class="mt-6">
                <x-user-info-card
                    :title="__('hermes.settings.profile.verification.title')"
                    :text="$this->hasUnverifiedEmail
                        ? __('hermes.settings.profile.verification.help_unverified')
                        : __('hermes.settings.profile.verification.help_verified')"
                    :tone="$this->hasUnverifiedEmail ? 'warning' : 'default'"
                    :prompt="$this->showProfilePrompt ? __('hermes.settings.profile.verification.profile_incomplete_prompt') : null"
                />

                <article class="user-info-card profile-upgrade-card">
                    <strong>{{ $this->currentUser()->role === User::ROLE_USER ? __('hermes.settings.profile.pro_upgrade.title') : __('hermes.settings.profile.pro_upgrade.pro_title') }}</strong>
                    <p>{{ __('hermes.settings.profile.pro_upgrade.text') }}</p>
                    @if ($this->currentUser()->role === User::ROLE_USER)
                        <a href="{{ route('pro-upgrade.show') }}" class="pill profile-upgrade-card__action">
                            {{ __('hermes.settings.profile.pro_upgrade.action') }}
                        </a>
                    @endif
                </article>
            </x-user-info-grid>

            <form wire:submit="updateProfileInformation" class="profile-form">
                <div class="profile-grid">
                    <div class="field">
                        <label for="name">{{ __('Name') }}</label>
                        <input id="name" wire:model="name" type="text" required autocomplete="name">
                        @error('name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="first_name">{{ __('hermes.settings.profile.fields.first_name') }}</label>
                        <input id="first_name" wire:model="first_name" type="text" autocomplete="given-name">
                        @error('first_name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field field--full">
                        <label for="email">{{ __('Email') }}</label>
                        <input id="email" wire:model="email" type="email" required autocomplete="email">
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror

                        @if ($this->hasUnverifiedEmail)
                            <div class="verification-block">
                                <p class="verification-note">{{ __('hermes.settings.profile.verification.notice') }}</p>
                                <button type="button" wire:click="resendVerificationEmail" class="verification-link">
                                    {{ __('hermes.settings.profile.verification.resend') }}
                                </button>

                                @if (session('status') === 'verification-link-sent')
                                    <x-user-feedback
                                        variant="status"
                                        class="mt-2"
                                        :messages="[__('hermes.settings.profile.verification.sent')]"
                                    />
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <label for="gender">{{ __('hermes.settings.profile.fields.gender') }}</label>
                        <select id="gender" wire:model="gender">
                            <option value="">{{ __('hermes.settings.profile.fields.choose_option') }}</option>
                            @foreach (User::genderOptions() as $genderOption)
                                <option value="{{ $genderOption }}">{{ ucfirst($genderOption) }}</option>
                            @endforeach
                        </select>
                        @error('gender') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="birth_date">{{ __('hermes.settings.profile.fields.birth_date') }}</label>
                        <input id="birth_date" wire:model="birth_date" type="date" autocomplete="bday">
                        @error('birth_date') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="city">{{ __('hermes.settings.profile.fields.city') }}</label>
                        <input id="city" wire:model="city" type="text" autocomplete="address-level2">
                        @error('city') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="country">{{ __('hermes.settings.profile.fields.country') }}</label>
                        <select id="country" wire:model="country" autocomplete="country-name">
                            <option value="">{{ __('hermes.settings.profile.fields.choose_option') }}</option>
                            @foreach (User::countryOptions() as $countryOption)
                                <option value="{{ $countryOption }}">{{ $countryOption }}</option>
                            @endforeach
                        </select>
                        @error('country') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="locale">{{ __('hermes.settings.profile.fields.locale') }}</label>
                        <select id="locale" wire:model="locale">
                            <option value="">{{ __('hermes.settings.profile.fields.locale_fallback') }}</option>
                            @foreach (config('locales.supported', []) as $localeCode => $localeLabel)
                                <option value="{{ $localeCode }}">{{ strtoupper($localeCode) }} · {{ $localeLabel }}</option>
                            @endforeach
                        </select>
                        @error('locale') <span class="field-error">{{ $message }}</span> @enderror
                        <span class="verification-note">{{ __('hermes.settings.profile.locale.session_note') }}</span>
                    </div>
                </div>

                <x-user-action-row align="end" class="profile-actions">
                    <x-action-message on="profile-updated" class="user-feedback user-feedback--status">
                        {{ __('Saved.') }}
                    </x-action-message>

                    <button type="submit" class="pill" data-test="update-profile-button">
                        {{ __('Save') }}
                    </button>
                </x-user-action-row>
            </form>

            <section class="profile-section">
                <div class="profile-heading">
                    <h2>{{ __('hermes.settings.profile.password.title') }}</h2>
                    <p>{{ __('hermes.settings.profile.password.intro') }}</p>
                </div>

                <form wire:submit="updatePassword" class="profile-form">
                    <div class="profile-grid">
                        <div class="field field--full">
                            <label for="current_password">{{ __('Current password') }}</label>
                            <input id="current_password" wire:model="current_password" type="password" required autocomplete="current-password">
                            @error('current_password') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field">
                            <label for="password">{{ __('New password') }}</label>
                            <input id="password" wire:model="password" type="password" required autocomplete="new-password">
                            @error('password') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field">
                            <label for="password_confirmation">{{ __('Confirm password') }}</label>
                            <input id="password_confirmation" wire:model="password_confirmation" type="password" required autocomplete="new-password">
                        </div>
                    </div>

                    <x-user-action-row align="end" class="profile-actions">
                        <x-action-message on="password-updated" class="user-feedback user-feedback--status">
                            {{ __('Saved.') }}
                        </x-action-message>

                        <button type="submit" class="pill" data-test="update-password-button">
                            {{ __('Save') }}
                        </button>
                    </x-user-action-row>
                </form>
            </section>

            @if (Auth::user()->canAccessAdminPortal())
                <section class="profile-section">
                    <div class="profile-heading">
                        <h2>Twee-factor-authenticatie (2FA)</h2>
                        <p>Beveilig je beheerdersaccount extra met een tijdgebonden verificatiecode via een authenticator-app zoals Google Authenticator of Authy.</p>
                    </div>

                    @if (session('status') === 'two-factor-authentication-enabled')
                        <x-user-feedback
                            variant="status"
                            class="mt-4"
                            :messages="['2FA is ingeschakeld. Scan nu de QR-code en bevestig met je verificatiecode.']"
                        />
                    @endif

                    @if (session('status') === 'two-factor-authentication-confirmed')
                        <x-user-feedback
                            variant="status"
                            class="mt-4"
                            :messages="['2FA is bevestigd en actief op jouw account.']"
                        />
                    @endif

                    @if (session('status') === 'two-factor-authentication-disabled')
                        <x-user-feedback
                            variant="status"
                            class="mt-4"
                            :messages="['2FA is uitgeschakeld voor jouw account.']"
                        />
                    @endif

                    @if ($this->twoFactorConfirmed)
                        <div class="verification-block" style="background: rgba(34,197,94,0.07); border-color: rgba(34,197,94,0.2); margin-top: 20px;">
                            <p class="verification-note" style="color: #166534; font-weight: 700;">2FA is actief en bevestigd op jouw account.</p>
                        </div>
                        <div class="verification-block" style="margin-top: 16px;">
                            <p class="verification-note">Bewaar deze herstelcodes op een veilige plek. Je kunt hiermee nog inloggen als je tijdelijk geen toegang hebt tot je authenticator-app.</p>
                            <div style="display: grid; gap: 8px; margin-top: 8px;">
                                @foreach ($this->recoveryCodes as $recoveryCode)
                                    <code style="display: inline-flex; width: fit-content; padding: 8px 12px; border-radius: 12px; background: rgba(22, 33, 29, 0.06); color: #16211d;">{{ $recoveryCode }}</code>
                                @endforeach
                            </div>
                        </div>
                        <div style="margin-top: 16px;">
                            <button type="button" class="tfa-btn tfa-btn--danger" wire:click="disableTwoFactor" wire:confirm="Weet je zeker dat je 2FA wilt uitschakelen?">
                                2FA uitschakelen
                            </button>
                        </div>
                    @elseif ($this->twoFactorPendingConfirmation)
                        <div class="verification-block" style="margin-top: 20px;">
                            <p class="verification-note">Scan de QR-code hieronder met je authenticator-app en voer daarna de zescijferige code in. 2FA wordt pas actief zodra deze bevestiging gelukt is.</p>
                        </div>
                        <div style="margin-top: 20px; max-width: 200px;">
                            {!! $this->twoFactorQrCode !!}
                        </div>
                        <div class="verification-block" style="margin-top: 16px;">
                            <p class="verification-note">Sla ook deze herstelcodes meteen op. Zodra 2FA bevestigd is, kun je hiermee nog inloggen als je je toestel niet bij de hand hebt.</p>
                            <div style="display: grid; gap: 8px; margin-top: 8px;">
                                @foreach ($this->recoveryCodes as $recoveryCode)
                                    <code style="display: inline-flex; width: fit-content; padding: 8px 12px; border-radius: 12px; background: rgba(22, 33, 29, 0.06); color: #16211d;">{{ $recoveryCode }}</code>
                                @endforeach
                            </div>
                        </div>
                        <form wire:submit="confirmTwoFactor" class="profile-form" style="margin-top: 16px; max-width: 320px;">
                            <div class="field">
                                <label for="twoFactorCode">Verificatiecode</label>
                                <input id="twoFactorCode" wire:model="twoFactorCode" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" required>
                                @error('twoFactorCode') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px;">
                                <button type="submit" class="tfa-btn tfa-btn--primary">2FA bevestigen</button>
                                <button type="button" class="tfa-btn" wire:click="disableTwoFactor">Annuleren</button>
                            </div>
                        </form>
                    @else
                        <div style="margin-top: 20px;">
                            <button type="button" class="tfa-btn tfa-btn--primary" wire:click="enableTwoFactor">
                                2FA inschakelen
                            </button>
                        </div>
                    @endif
                </section>
            @endif

            @if ($this->showDeleteUser)
                <div class="profile-delete">
                    <livewire:pages::settings.delete-user-form />
                </div>
            @endif
        </section>
    </x-pages::settings.layout>
</section>
