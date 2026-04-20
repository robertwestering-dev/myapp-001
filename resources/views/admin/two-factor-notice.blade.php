<x-layouts.hermes-admin
    title="2FA vereist"
    eyebrow="Beveiliging"
    heading="Tweefactorauthenticatie vereist"
    lead="Als beheerder moet je twee-factor-authenticatie (2FA) inschakelen voordat je toegang krijgt tot het beheerderspaneel."
    :show-secondary-menu-items="false"
    :show-hero="false"
>
    <section style="max-width: 560px; margin: 40px 0;">
        <div class="user-panel" style="padding: 40px;">
            <p style="margin: 0 0 24px; font-family: Arial, Helvetica, sans-serif; font-size: 1rem; line-height: 1.6; color: #5a6762;">
                Twee-factor-authenticatie voegt een extra beveiligingslaag toe aan jouw beheerdersaccount. Na het inloggen heb je naast je wachtwoord ook een tijdgebonden code nodig vanuit een authenticator-app.
            </p>
            <a href="{{ route('profile.edit') }}" class="pill">
                2FA instellen in profielbeheer
            </a>
        </div>
    </section>
</x-layouts.hermes-admin>
