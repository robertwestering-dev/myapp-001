<x-layouts.hermes-admin
    title="Admin-portal"
    eyebrow="Admin-portal"
    heading="Welkom terug, beheerder."
    :lead="'U bent ingelogd als admin met het account '.auth()->user()->email.'. Deze omgeving is uitsluitend beschikbaar voor gebruikers met de rol Admin.'"
    menu-active="portal"
>
    <x-slot:heroFacts>
        <div class="fact">
            <strong>{{ auth()->user()->role }}</strong>
            <span>Actieve rol in deze sessie</span>
        </div>
        <div class="fact">
            <strong>Beveiligd</strong>
            <span>Toegang uitsluitend voor admins</span>
        </div>
        <div class="fact">
            <strong>Gereed</strong>
            <span>Startpunt voor verdere modules</span>
        </div>
    </x-slot:heroFacts>

</x-layouts.hermes-admin>
