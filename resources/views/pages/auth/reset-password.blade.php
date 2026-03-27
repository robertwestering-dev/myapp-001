<x-layouts.hermes-auth
    title="Reset password"
    :back-href="route('login')"
    back-label="Terug naar login"
    eyebrow="New Password"
    heading="Kies een nieuw wachtwoord in dezelfde vertrouwde stijl."
    lead="Ook de reset-password pagina sluit nu visueel aan op de andere auth-schermen, met dezelfde rustige premium uitstraling en duidelijke formulieropbouw."
    form-title="Reset password"
    helper="Voer hieronder je e-mailadres en nieuwe wachtwoord in om je accounttoegang te herstellen."
    :points="[
        'Vul hetzelfde e-mailadres in als bij het account',
        'Kies een nieuw veilig wachtwoord',
        'Na het resetten kun je direct opnieuw inloggen',
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('login') }}">Terug naar login</a>
    </x-slot:heroActions>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <label>
            <span>Email address</span>
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
            <span>Password</span>
            <input
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="Nieuw wachtwoord"
                required
            >
        </label>

        <label>
            <span>Confirm password</span>
            <input
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="Bevestig wachtwoord"
                required
            >
        </label>

        <button type="submit" class="pill pill--strong submit" data-test="reset-password-button">
            Reset password
        </button>
    </form>

    <x-slot:secondary>
        Terug naar je account?
        <a href="{{ route('login') }}">Log dan hier in</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        De reset-flow blijft gebruikmaken van dezelfde Laravel Fortify-validatie en beveiliging als voorheen.
    </x-slot:sideNote>
</x-layouts.hermes-auth>
