<x-layouts.hermes-auth
    title="Login"
    :back-href="route('home')"
    back-label="Terug naar home"
    eyebrow="Secure Access"
    heading="Log in binnen dezelfde sfeer als de homepage."
    lead="De loginpagina gebruikt nu exact dezelfde kleurwereld, typografie en premium panelstijl als de homepage, zodat de overgang van bezoeker naar gebruiker visueel logisch voelt."
    form-title="Log in"
    helper="Gebruik je e-mailadres en wachtwoord om verder te gaan."
    :points="[
        'Hetzelfde warme kleurenpalet als de landing page',
        'Rustige premium compositie met glass panels',
        'Een direct en veilig loginformulier',
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('home') }}">Terug naar homepage</a>
        @if (Route::has('register'))
            <a class="pill" href="{{ route('register') }}">Nieuw account</a>
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
        Na inloggen word je doorgestuurd naar je persoonlijke welkomstpagina met je e-mailadres en de logout-knop.
    </x-slot:sideNote>
</x-layouts.hermes-auth>
