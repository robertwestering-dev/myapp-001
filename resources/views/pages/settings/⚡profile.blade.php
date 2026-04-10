<?php

use App\Concerns\ProfileValidationRules;
use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->first_name = Auth::user()->first_name;
        $this->gender = Auth::user()->gender;
        $this->birth_date = Auth::user()->birth_date?->toDateString();
        $this->city = Auth::user()->city;
        $this->country = Auth::user()->country;
        $this->locale = Auth::user()->locale;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        if ($this->locale === '') {
            $this->locale = null;
        }

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

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

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
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
        :heading="__('hermes.settings.profile.personal.title')"
        :subheading="__('hermes.settings.profile.personal.intro')"
    >
        <section class="profile-card user-panel">
            <x-user-info-grid columns="2" class="mt-6">
                <x-user-info-card
                    :badge="$this->hasUnverifiedEmail
                        ? __('hermes.settings.profile.verification.badge_unverified')
                        : __('hermes.settings.profile.verification.badge_verified')"
                    :title="__('hermes.settings.profile.verification.title')"
                    :text="$this->hasUnverifiedEmail
                        ? __('hermes.settings.profile.verification.help_unverified')
                        : __('hermes.settings.profile.verification.help_verified')"
                    :tone="$this->hasUnverifiedEmail ? 'warning' : 'default'"
                />

                <x-user-info-card
                    :badge="__('hermes.settings.profile.locale.badge')"
                    :title="__('hermes.settings.profile.locale.title')"
                    :text="__('hermes.settings.profile.locale.help')"
                />
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
                                <button type="button" class="verification-link" wire:click.prevent="resendVerificationNotification">
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

            @if ($this->showDeleteUser)
                <div class="profile-delete">
                    <livewire:pages::settings.delete-user-form />
                </div>
            @endif
        </section>
    </x-pages::settings.layout>
</section>
