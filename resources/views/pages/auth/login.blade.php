<x-layouts.hermes-auth
    title="Login"
    :back-href="route('home')"
    back-label="Terug naar home"
    eyebrow="Secure Access"
    heading="Toegang tot jouw portal"
    lead="Na inloggen heb je gratis toegang tot de Quick scans, de Academy en meer. Organisaties kunnen een zakelijk account aanmaken (plan daarvoor een afspraak)."
    form-title="Log in"
    helper=""
    :points="['Een nieuw account aanmaken is gratis','Je ontvangt een bericht om je emailadres te verifiëren','Via je account heb je toegang tot de portal']"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('home') }}">Terug naar homepage</a>
        @if (Route::has('register'))
            <a class="pill" href="{{ route('register') }}">Nieuw account aanmaken</a>
        @endif
    </x-slot:heroActions>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label>
            <span>Email address</span>
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
            <span>Password</span>
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
                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="helper" href="{{ route('password.request') }}">Forgot your password?</a>
            @endif
        </div>

        <button type="submit" class="pill pill--strong submit">Inloggen</button>
    </form>

    @if (Route::has('register'))
        <x-slot:secondary>
            Nog geen account?
            <a href="{{ route('register') }}">Maak er hier een aan</a>
        </x-slot:secondary>
    @endif

    <x-slot:sideNote>
        
    </x-slot:sideNote>
</x-layouts.hermes-auth>
