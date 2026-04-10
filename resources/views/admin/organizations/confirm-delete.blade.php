<x-layouts.hermes-admin
    title="Organisatie verwijderen"
    eyebrow="Organisaties"
    heading="Wilt u deze organisatie echt verwijderen?"
    :lead="'U staat op het punt '.e($organization->naam).' te verwijderen. Contactpersoon: '.e($organization->contact?->name ?? 'Onbekend').'.'"
    menu-active="organizations"
>
    <x-admin-confirm-delete
        :action="route('admin.organizations.destroy', $organization)"
        :cancel-href="route('admin.organizations.index')"
    >
        <p>Deze actie kan niet ongedaan worden gemaakt. Organisaties met gekoppelde gebruikers, of de standaardorganisatie Hermes Results, kunnen niet worden verwijderd.</p>
    </x-admin-confirm-delete>
</x-layouts.hermes-admin>
