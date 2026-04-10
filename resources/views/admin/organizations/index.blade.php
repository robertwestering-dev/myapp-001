<x-layouts.hermes-admin
    title="Admin organisaties"
    eyebrow="Organisaties"
    heading="Organisatieoverzicht"
    lead="Bekijk alle organisaties en beheer per record direct de gekoppelde contactpersoon."
    menu-active="organizations"
    :show-secondary-menu-items="false"
    :show-hero="false"
>
    <style>
        .admin-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 28px 0 24px;
        }

        .admin-toolbar--center {
            align-items: center;
        }

        .admin-toolbar__group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 720px;
        }

        th,
        td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        th {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .icon-button {
            width: 42px;
            height: 42px;
            padding: 0;
            border-radius: 14px;
            box-shadow: none;
        }

        .icon-button--danger {
            color: var(--accent-deep);
            background: rgba(168, 74, 25, 0.12);
            border-color: rgba(168, 74, 25, 0.2);
        }

        .icon-button svg {
            width: 18px;
            height: 18px;
        }

        .muted {
            color: var(--muted);
        }

        .meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination__link,
        .pagination__current {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0 14px;
            border-radius: 14px;
            font-family: Arial, Helvetica, sans-serif;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
        }

        .pagination__current {
            color: #fff;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-soft) 100%);
            border-color: transparent;
        }

        .empty {
            display: grid;
            gap: 12px;
            padding: 26px;
            border-radius: 24px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }
    </style>

    <section class="content-panel">
        <x-admin-feedback :messages="session('status')" />
        <x-admin-feedback variant="errors" :messages="$errors->all()" />

        <x-admin-toolbar align="center">
            <div>
                <strong>Alle organisaties</strong>
                <div class="muted">Per organisatie ziet u direct de gekoppelde contactpersoon.</div>
            </div>

            @if ($canCreateOrganizations)
                <x-admin-toolbar-group class="actions">
                    <a href="{{ route('admin.organizations.create') }}" class="pill">Nieuwe organisatie</a>
                </x-admin-toolbar-group>
            @endif
        </x-admin-toolbar>

        @if ($organizations->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Organisatie</th>
                            <th>Contactpersoon</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($organizations as $organization)
                            <tr>
                                <td>{{ $organization->naam }}</td>
                                <td>{{ $organization->contact?->name ?? 'Geen contactpersoon' }}</td>
                                <td>
                                    <x-admin-row-actions>
                                        <x-admin-icon-link
                                            :href="route('admin.organizations.edit', $organization)"
                                            :label="'Wijzig ' . $organization->naam"
                                            title="Wijzigen"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                            </svg>
                                        </x-admin-icon-link>

                                        @if ($canDeleteOrganizations)
                                            <x-admin-icon-link
                                                :href="route('admin.organizations.confirm-delete', $organization)"
                                                :label="'Verwijder ' . $organization->naam"
                                                title="Verwijderen"
                                                variant="danger"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18"/>
                                                    <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
                                                    <path d="M19 6l-1 14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                </svg>
                                            </x-admin-icon-link>
                                        @endif
                                    </x-admin-row-actions>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin-results-meta :paginator="$organizations" aria-label="Paginering organisaties" />
        @else
            <x-admin-empty-state
                title="Er zijn nog geen organisaties beschikbaar."
                description="Voeg de eerste organisatie toe om te starten met beheer en koppelingen."
            >
                @if ($canCreateOrganizations)
                    <x-slot:actions>
                        <a href="{{ route('admin.organizations.create') }}" class="pill">Nieuwe organisatie</a>
                    </x-slot:actions>
                @endif
            </x-admin-empty-state>
        @endif
    </section>
</x-layouts.hermes-admin>
