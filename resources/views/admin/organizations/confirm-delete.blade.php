<x-layouts.hermes-admin
    title="Organisatie verwijderen"
    eyebrow="Organisaties"
    heading="Wilt u deze organisatie echt verwijderen?"
    :lead="'U staat op het punt '.e($organization->naam).' te verwijderen. Contactpersoon: '.e($organization->contact?->name ?? 'Onbekend').'.'"
    menu-active="organizations"
>
    <style>
        .confirm-card {
            display: grid;
            gap: 18px;
        }

        .confirm-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        .confirm-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
    </style>

    <section class="content-panel confirm-card">
        <p>Deze actie kan niet ongedaan worden gemaakt. Organisaties met gekoppelde gebruikers, of de standaardorganisatie Hermes Results, kunnen niet worden verwijderd.</p>

        <div class="confirm-actions">
            <form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="pill">JA, verwijderen</button>
            </form>

            <a href="{{ route('admin.organizations.index') }}" class="ghost-pill">NEE, annuleren</a>
        </div>
    </section>
</x-layouts.hermes-admin>
