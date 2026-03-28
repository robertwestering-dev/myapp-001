<x-layouts.hermes-admin
    title="Admin organisaties"
    eyebrow="Organisaties"
    heading="Organisatieoverzicht"
    lead="Bekijk alle organisaties en beheer per record direct de gekoppelde contactpersoon."
    menu-active="organizations"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$organizations->total()"
            description="Totaal aantal organisaties"
        />
        <x-hermes-fact
            :title="$organizations->count()"
            description="Resultaten op deze pagina"
        />
        <x-hermes-fact
            title="CRUD"
            description="Toevoegen, wijzigen en verwijderen vanuit hetzelfde overzicht"
        />
    </x-slot:heroFacts>

    <style>
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 28px 0 24px;
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
            padding: 26px;
            border-radius: 24px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }
    </style>

    <section class="content-panel">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="toolbar">
            <div>
                <strong>Alle organisaties</strong>
                <div class="muted">Per organisatie ziet u direct de gekoppelde contactpersoon.</div>
            </div>

            @if ($canCreateOrganizations)
                <div class="actions">
                    <a href="{{ route('admin.organizations.create') }}" class="pill">Nieuwe organisatie</a>
                </div>
            @endif
        </div>

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
                                    <div class="actions">
                                        <a
                                            href="{{ route('admin.organizations.edit', $organization) }}"
                                            class="ghost-pill icon-button"
                                            aria-label="Wijzig {{ $organization->naam }}"
                                            title="Wijzigen"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                            </svg>
                                        </a>

                                        @if ($canDeleteOrganizations)
                                            <a
                                                href="{{ route('admin.organizations.confirm-delete', $organization) }}"
                                                class="danger-pill icon-button icon-button--danger"
                                                aria-label="Verwijder {{ $organization->naam }}"
                                                title="Verwijderen"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18"/>
                                                    <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
                                                    <path d="M19 6l-1 14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="meta">
                <div class="muted">
                    Resultaten {{ $organizations->firstItem() }} t/m {{ $organizations->lastItem() }} van {{ $organizations->total() }}
                </div>

                <div class="pagination" role="navigation" aria-label="Paginering organisaties">
                    @if ($organizations->onFirstPage())
                        <span class="pagination__link muted">Vorige</span>
                    @else
                        <a href="{{ $organizations->previousPageUrl() }}" class="pagination__link">Vorige</a>
                    @endif

                    @foreach (range(max(1, $organizations->currentPage() - 1), min($organizations->lastPage(), $organizations->currentPage() + 1)) as $page)
                        @if ($page === $organizations->currentPage())
                            <span class="pagination__current">{{ $page }}</span>
                        @else
                            <a href="{{ $organizations->url($page) }}" class="pagination__link">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($organizations->hasMorePages())
                        <a href="{{ $organizations->nextPageUrl() }}" class="pagination__link">Volgende</a>
                    @else
                        <span class="pagination__link muted">Volgende</span>
                    @endif
                </div>
            </div>
        @else
            <div class="empty">
                Er zijn nog geen organisaties beschikbaar. Voeg de eerste organisatie toe om te starten.
            </div>
        @endif
    </section>
</x-layouts.hermes-admin>
