<x-layouts.hermes-auth
    title="Forgot password"
    :back-href="route('login')"
    back-label="Terug naar login"
    eyebrow="Password Recovery"
    heading="Wachtwoord vergeten?"
    lead="Voer hiernaast je emailadres in. Als dat bestaat in onze database, dan ontvang je een resetlink voor het instellen van een nieuw wachtwoord."
    form-title="Forgot password"
    helper="Vul je e-mailadres in en we sturen je een link om je wachtwoord opnieuw in te stellen."
    :points="[
        'Voer het e-mailadres van je account in',
        'Ontvang een resetlink',
        'Kies daarna een nieuw wachtwoord en log opnieuw in',
    ]"
>
    <x-slot:heroActions>
        <a class="pill pill--strong" href="{{ route('login') }}">Terug naar login</a>
    </x-slot:heroActions>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label>
            <span>Email address</span>
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
            Email password reset link
        </button>
    </form>

    <x-slot:secondary>
        Of ga terug naar <a href="{{ route('login') }}">inloggen</a>
    </x-slot:secondary>

    <x-slot:sideNote>
        
    </x-slot:sideNote>
</x-layouts.hermes-auth>
