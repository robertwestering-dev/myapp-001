<x-layouts.hermes-auth
    :title="__('hermes.auth.forgot_password.title')"
    :back-href="route('login')"
    :back-label="__('hermes.auth.forgot_password.back_label')"
    :eyebrow="__('hermes.auth.forgot_password.eyebrow')"
    :heading="__('hermes.auth.forgot_password.heading')"
    :lead="__('hermes.auth.forgot_password.lead')"
    :form-title="__('hermes.auth.forgot_password.form_title')"
    :helper="__('hermes.auth.forgot_password.helper')"
    :points="[
        __('hermes.auth.forgot_password.point_1'),
        __('hermes.auth.forgot_password.point_2'),
        __('hermes.auth.forgot_password.point_3'),
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('login') }}">{{ __('hermes.auth.forgot_password.hero_back') }}</a>
    </x-slot:heroActions>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label>
            <span>{{ __('hermes.auth.forgot_password.email') }}</span>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                required
            >
        </label>

        <button type="submit" class="pill pill--strong submit" data-test="email-password-reset-link-button">
            {{ __('hermes.auth.forgot_password.submit') }}
        </button>
    </form>

    <x-slot:secondary>
        {{ __('hermes.auth.forgot_password.secondary') }} <a href="{{ route('login') }}">{{ __('hermes.auth.forgot_password.secondary_link') }}</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        
    </x-slot:sideNote>
</x-layouts.hermes-auth>
