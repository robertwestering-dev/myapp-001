<x-layouts.hermes-auth
    :title="__('hermes.auth.reset_password.title')"
    :back-href="route('login')"
    :back-label="__('hermes.auth.reset_password.back_label')"
    :eyebrow="__('hermes.auth.reset_password.eyebrow')"
    :heading="__('hermes.auth.reset_password.heading')"
    :lead="__('hermes.auth.reset_password.lead')"
    :form-title="__('hermes.auth.reset_password.form_title')"
    :helper="__('hermes.auth.reset_password.helper')"
    :points="[
        __('hermes.auth.reset_password.point_1'),
        __('hermes.auth.reset_password.point_2'),
        __('hermes.auth.reset_password.point_3'),
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('login') }}">{{ __('hermes.auth.reset_password.hero_back') }}</a>
    </x-slot:heroActions>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <label>
            <span>{{ __('hermes.auth.reset_password.email') }}</span>
            <input
                type="email"
                name="email"
                value="{{ request('email') }}"
                autocomplete="email"
                placeholder="email@example.com"
                required
                autofocus
            >
        </label>

        <label>
            <span>{{ __('hermes.auth.reset_password.password') }}</span>
            <input
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="{{ __('hermes.auth.reset_password.password_placeholder') }}"
                required
            >
        </label>

        <label>
            <span>{{ __('hermes.auth.reset_password.password_confirmation') }}</span>
            <input
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="{{ __('hermes.auth.reset_password.password_confirmation_placeholder') }}"
                required
            >
        </label>

        <button type="submit" class="pill pill--strong submit" data-test="reset-password-button">
            {{ __('hermes.auth.reset_password.submit') }}
        </button>
    </form>

    <x-slot:secondary>
        {{ __('hermes.auth.reset_password.secondary') }}
        <a href="{{ route('login') }}">{{ __('hermes.auth.reset_password.secondary_link') }}</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        {{ __('hermes.auth.reset_password.side_note') }}
    </x-slot:sideNote>
</x-layouts.hermes-auth>
