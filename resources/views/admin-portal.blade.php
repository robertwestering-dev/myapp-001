<x-layouts.hermes-admin
    title="Admin-portal"
    eyebrow="Admin-portal"
    heading="Welkom terug, beheerder."
    :lead="'U bent ingelogd als admin met het account '.auth()->user()->email.'. Deze omgeving is uitsluitend beschikbaar voor gebruikers met de rol Admin.'"
    menu-active="portal"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="auth()->user()->role"
            description="Actieve rol in deze sessie"
        />
        <x-hermes-fact
            title="Beveiligd"
            description="Toegang uitsluitend voor admins"
        />
        <x-hermes-fact
            title="Gereed"
            description="Startpunt voor verdere modules"
        />
    </x-slot:heroFacts>

</x-layouts.hermes-admin>
