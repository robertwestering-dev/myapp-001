<x-layouts.hermes-admin
    title="Admin gebruikers"
    eyebrow="Gebruikers"
    heading="Gebruikersoverzicht"
    lead="Doorzoek en exporteer alle gebruikers. De lijst is gesorteerd op naam en toont telkens 15 records per pagina."
    menu-active="users"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <div class="fact">
            <strong>{{ $users->total() }}</strong>
            <span>Totaal aantal gebruikers</span>
        </div>
        <div class="fact">
            <strong>{{ $users->count() }}</strong>
            <span>Resultaten op deze pagina</span>
        </div>
        <div class="fact">
            <strong>CSV</strong>
            <span>Export direct beschikbaar voor de huidige selectie</span>
        </div>
    </x-slot:heroFacts>

    <style>
        .toolbar {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 28px 0 24px;
        }

        .search {
            min-width: min(100%, 360px);
            display: grid;
            gap: 10px;
        }

        .search label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .search__row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search input {
            flex: 1 1 220px;
            min-width: 0;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .actions {
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
            min-width: 760px;
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

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
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

        .actions form {
            margin: 0;
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
            <form method="GET" action="{{ route('admin.users.index') }}" class="search">
                <label for="search">Zoek op naam of emailadres</label>
                <div class="search__row">
                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Bijvoorbeeld anna of anna@example.com"
                    >
                    <button type="submit" class="pill">Zoeken</button>
                    <a href="{{ route('admin.users.index') }}" class="ghost-pill">Reset</a>
                </div>
            </form>

            <div class="actions">
                <a href="{{ route('admin.users.create') }}" class="pill">Nieuwe gebruiker</a>
                <a href="{{ route('admin.users.export', ['search' => $search]) }}" class="ghost-pill">Export CSV</a>
            </div>
        </div>

        @if ($users->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Emailadres</th>
                            <th>Rol</th>
                            <th>Email verified</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge">{{ $user->role }}</span></td>
                                <td class="muted">{{ $user->email_verified_at?->format('d-m-Y H:i') ?? 'Niet geverifieerd' }}</td>
                                <td>
                                    <div class="actions">
                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="ghost-pill icon-button"
                                            aria-label="Wijzig {{ $user->name }}"
                                            title="Wijzigen"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                            </svg>
                                        </a>

                                        <a
                                            href="{{ route('admin.users.confirm-delete', $user) }}"
                                            class="danger-pill icon-button icon-button--danger"
                                            aria-label="Verwijder {{ $user->name }}"
                                            title="Verwijderen"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4h8v2"/>
                                                <path d="M19 6l-1 14H6L5 6"/>
                                                <path d="M10 11v6"/>
                                                <path d="M14 11v6"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="meta">
                <p class="muted">
                    Resultaten {{ $users->firstItem() }} t/m {{ $users->lastItem() }} van {{ $users->total() }}
                </p>

                <nav class="pagination" aria-label="Paginatie">
                    @if ($users->onFirstPage())
                        <span class="pagination__link muted">Vorige</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="pagination__link">Vorige</a>
                    @endif

                    @foreach (range(max(1, $users->currentPage() - 1), min($users->lastPage(), $users->currentPage() + 1)) as $page)
                        @if ($page === $users->currentPage())
                            <span class="pagination__current">{{ $page }}</span>
                        @else
                            <a href="{{ $users->url($page) }}" class="pagination__link">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="pagination__link">Volgende</a>
                    @else
                        <span class="pagination__link muted">Volgende</span>
                    @endif
                </nav>
            </div>
        @else
            <div class="empty">
                <strong>Geen gebruikers gevonden.</strong>
                <p class="muted">Pas de zoekterm aan of reset het zoekveld om de volledige lijst te zien.</p>
            </div>
        @endif
    </section>
</x-layouts.hermes-admin>
