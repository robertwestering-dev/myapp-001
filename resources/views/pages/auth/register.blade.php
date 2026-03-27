<x-layouts.hermes-auth
    title="Register"
    :back-href="route('login')"
    back-label="Terug naar login"
    eyebrow="Create Access"
    heading="Maak een account aan in dezelfde visuele wereld."
    lead="De registerpagina volgt nu exact dezelfde stijl als de loginpagina: hetzelfde warme kleurenpalet, dezelfde premium panelen en dezelfde rustige, zakelijke compositie."
    form-title="Create account"
    helper="Vul hieronder je gegevens in om een nieuw account aan te maken."
    :points="[
        'Registreer met een geldig e-mailadres',
        'Kies een veilig wachtwoord voor je account',
        'Na registratie ga je direct naar je welkomstpagina',
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('login') }}">Ik heb al een account</a>
    </x-slot:heroActions>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label>
            <span>Naam</span>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                autocomplete="name"
                placeholder="Uw naam"
                required
                autofocus
            >
        </label>

        <label>
            <span>Email address</span>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email"
                placeholder="email@example.com"
                required
            >
        </label>

        <label>
            <span>Password</span>
            <input
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="Password"
                required
            >
        </label>

        <label>
            <span>Confirm password</span>
            <input
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Confirm password"
                required
            >
        </label>

        <button type="submit" class="pill pill--strong" data-test="register-user-button">
            Create account
        </button>
    </form>

    <x-slot:secondary>
        Heb je al een account?
        <a href="{{ route('login') }}">Log dan hier in</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        De registratie blijft gebruikmaken van dezelfde Laravel Fortify-flow en validatie als voorheen.
    </x-slot:sideNote>
</x-layouts.hermes-auth>
