<x-layouts.hermes-auth
    :title="__('Two-factor authentication')"
    :back-href="route('login')"
    :back-label="__('Back to login')"
    eyebrow="Security Check"
    :heading="__('Bevestig veilig dat jij het bent.')"
    :lead="__('Rond je aanmelding af met de code uit je authenticator-app. Heb je die niet bij de hand, dan kun je ook een herstelcode gebruiken.')"
    :form-title="__('Twee-factor-authenticatie')"
    :announcement="__('hermes.auth.announcement')"
    helper=""
    :points="[
        __('Gebruik de 6-cijferige code uit je authenticator-app.'),
        __('Lukt dat niet, schakel dan over naar een eenmalige herstelcode.'),
        __('Na verificatie sturen we je direct terug naar je account.'),
    ]"
>
    <div
        x-cloak
        x-data="{
            showRecoveryInput: @js($errors->has('recovery_code')),
            code: '',
            recovery_code: '',
            toggleInput() {
                this.showRecoveryInput = !this.showRecoveryInput;

                this.code = '';
                this.recovery_code = '';

                $nextTick(() => {
                    this.showRecoveryInput
                        ? this.$refs.recovery_code?.focus()
                        : this.$refs.code?.focus();
                });
            },
        }"
    >
        <div x-show="!showRecoveryInput">
            <p class="helper">{{ __('Voer de code in die nu zichtbaar is in je authenticator-app.') }}</p>
        </div>

        <div x-show="showRecoveryInput">
            <p class="helper">{{ __('Gebruik een eerder opgeslagen herstelcode om toegang tot je account te bevestigen.') }}</p>
        </div>

        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div x-show="!showRecoveryInput" style="display: grid; gap: 18px;">
                <label>
                    <span>{{ __('Authenticatiecode') }}</span>
                    <input
                        type="text"
                        name="code"
                        x-ref="code"
                        x-model="code"
                        autocomplete="one-time-code"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        placeholder="000000"
                        autofocus
                    >
                </label>

                @error('code')
                    <div class="errors">{{ $message }}</div>
                @enderror
            </div>

            <div x-show="showRecoveryInput" style="display: grid; gap: 18px;">
                <label>
                    <span>{{ __('Herstelcode') }}</span>
                    <input
                        type="text"
                        name="recovery_code"
                        x-ref="recovery_code"
                        x-bind:required="showRecoveryInput"
                        autocomplete="one-time-code"
                        x-model="recovery_code"
                        placeholder="xxxx-xxxx"
                    >
                </label>

                @error('recovery_code')
                    <div class="errors">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="pill pill--strong submit" style="width: 100%; margin-top: 22px;">
                {{ __('Continue') }}
            </button>
        </form>

        <div class="row" style="margin-top: 18px; justify-content: center;">
            <span class="helper">{{ __('or you can') }}</span>
            <button type="button" class="helper" @click="toggleInput()" style="border: 0; background: none; padding: 0; color: #a84a19; font-weight: 700; cursor: pointer;">
                <span x-show="!showRecoveryInput">{{ __('login using a recovery code') }}</span>
                <span x-show="showRecoveryInput">{{ __('login using an authentication code') }}</span>
            </button>
        </div>
    </div>

    <x-slot:secondary>
        {{ __('Twijfel je of dit scherm klopt?') }}
        <a href="{{ route('login') }}">{{ __('Ga terug naar inloggen') }}</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        {{ __('Voor beheerders en accounts met extra beveiliging is deze extra controle verplicht voordat je toegang krijgt tot het dashboard of admin-portal.') }}
    </x-slot:sideNote>
</x-layouts.hermes-auth>
