<x-layouts.hermes-auth
    :title="__('hermes.auth.login.title')"
    :back-href="route('home')"
    :back-label="__('hermes.auth.login.back_label')"
    :eyebrow="__('hermes.auth.login.eyebrow')"
    :heading="__('hermes.auth.login.heading')"
    :lead="__('hermes.auth.login.lead')"
    :form-title="__('hermes.auth.login.form_title')"
    helper=""
    :points="[
        __('hermes.auth.login.point_1'),
        __('hermes.auth.login.point_2'),
        __('hermes.auth.login.point_3'),
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('home') }}">{{ __('hermes.auth.login.hero_back') }}</a>
        @if (Route::has('register'))
            <a class="pill" href="{{ route('register') }}">{{ __('hermes.auth.login.hero_register') }}</a>
        @endif
    </x-slot:heroActions>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label>
            <span>{{ __('hermes.auth.login.email') }}</span>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                placeholder="email@example.com"
                required
                autofocus
            >
        </label>

        <label>
            <span>{{ __('hermes.auth.login.password') }}</span>
            <input
                type="password"
                name="password"
                autocomplete="current-password"
                placeholder="Password"
                required
            >
        </label>

        <div class="row">
            <label class="checkbox">
                <input type="checkbox" name="remember" @checked(old('remember'))>
                <span>{{ __('hermes.auth.login.remember') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="helper" href="{{ route('password.request') }}">{{ __('hermes.auth.login.forgot_password') }}</a>
            @endif
        </div>

        <button type="submit" class="pill pill--strong submit">{{ __('hermes.auth.login.submit') }}</button>
    </form>

    @if (Route::has('register'))
        <x-slot:secondary>
            {{ __('hermes.auth.login.secondary') }}
            <a href="{{ route('register') }}">{{ __('hermes.auth.login.secondary_link') }}</a>
        </x-slot:secondary>
    @endif

    <x-slot:sideNote>
        
    </x-slot:sideNote>
</x-layouts.hermes-auth>
