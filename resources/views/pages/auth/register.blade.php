<x-layouts.hermes-auth
    :title="__('hermes.auth.register.title')"
    :back-href="route('login')"
    :back-label="__('hermes.auth.register.back_label')"
    :eyebrow="__('hermes.auth.register.eyebrow')"
    :heading="__('hermes.auth.register.heading')"
    :lead="__('hermes.auth.register.lead')"
    :form-title="__('hermes.auth.register.form_title')"
    :announcement="__('hermes.auth.announcement')"
    :helper="__('hermes.auth.register.helper')"
    :points="[
        __('hermes.auth.register.point_1'),
        __('hermes.auth.register.point_2'),
        __('hermes.auth.register.point_3'),
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('login') }}">{{ __('hermes.auth.register.hero_login') }}</a>
    </x-slot:heroActions>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label>
            <span>{{ __('hermes.auth.register.name') }}</span>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                autocomplete="name"
                placeholder="{{ __('hermes.auth.register.name_placeholder') }}"
                required
                autofocus
            >
        </label>

        <label>
            <span>{{ __('hermes.auth.register.email') }}</span>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                placeholder="{{ __('hermes.auth.register.email_placeholder') }}"
                required
            >
        </label>

        <label>
            <span>{{ __('hermes.auth.register.password') }}</span>
            <input
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="{{ __('hermes.auth.register.password_placeholder') }}"
                required
            >
        </label>

        <label>
            <span>{{ __('hermes.auth.register.password_confirmation') }}</span>
            <input
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="{{ __('hermes.auth.register.password_confirmation_placeholder') }}"
                required
            >
        </label>

        <button type="submit" class="pill pill--strong" data-test="register-user-button">
            {{ __('hermes.auth.register.submit') }}
        </button>
    </form>

    <x-slot:secondary>
        {{ __('hermes.auth.register.secondary') }}
        <a href="{{ route('login') }}">{{ __('hermes.auth.register.secondary_link') }}</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        
    </x-slot:sideNote>
</x-layouts.hermes-auth>
