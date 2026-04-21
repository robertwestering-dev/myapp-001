<x-layouts.hermes-admin
    title="Admin gebruikers"
    eyebrow=""
    heading="Gebruikersoverzicht"
    lead="Doorzoek en exporteer alle gebruikers. De lijst is gesorteerd op naam en toont telkens 15 records per pagina."
    menu-active="users"
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

        .admin-toolbar--end {
            align-items: end;
        }

        .admin-toolbar__group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search {
            min-width: min(100%, 360px);
            display: grid;
            gap: 10px;
        }

        .admin-filter-field {
            display: grid;
            gap: 8px;
        }

        .admin-filter-field__label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .admin-filter-actions,
        .search__row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .admin-filter-control {
            min-width: 180px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .admin-filter-control--search {
            flex: 1 1 220px;
            min-width: 0;
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

        <x-admin-toolbar>
            <form method="GET" action="{{ route('admin.users.index') }}" class="search">
                <div class="search__row">
                    <x-admin-filter-field label="Zoek op naam of emailadres" for="search">
                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Bijvoorbeeld anna of anna@example.com"
                            class="admin-filter-control admin-filter-control--search"
                        >
                    </x-admin-filter-field>

                    <x-admin-filter-field label="Organisatie" for="organization">
                        <select id="organization" name="organization" class="admin-filter-control">
                            <option value="">Alle organisaties</option>
                            @foreach ($organizations as $organizationId => $organizationName)
                                <option value="{{ $organizationId }}" @selected($selectedOrganization === (string) $organizationId)>
                                    {{ $organizationName }}
                                </option>
                            @endforeach
                        </select>
                    </x-admin-filter-field>

                    <x-admin-filter-field label="Rol" for="role">
                        <select id="role" name="role" class="admin-filter-control">
                            <option value="">Alle rollen</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected($selectedRole === $role)>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </x-admin-filter-field>

                    <x-admin-filter-field label="Land" for="country">
                        <select id="country" name="country" class="admin-filter-control">
                            <option value="">Alle landen</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}" @selected($selectedCountry === $country)>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </x-admin-filter-field>
                </div>

                <x-admin-filter-actions>
                    <button type="submit" class="pill">Zoeken</button>
                    <a href="{{ route('admin.users.index') }}" class="ghost-pill">Reset</a>
                </x-admin-filter-actions>
            </form>

            <x-admin-toolbar-group class="actions">
                <a href="{{ route('admin.users.create') }}" class="pill">Nieuwe gebruiker</a>
                <a
                    href="{{ route('admin.users.export', ['search' => $search, 'organization' => $selectedOrganization, 'role' => $selectedRole, 'country' => $selectedCountry]) }}"
                    class="ghost-pill"
                >
                    Export CSV
                </a>
            </x-admin-toolbar-group>
        </x-admin-toolbar>

        @if ($activeFilters !== [])
            <x-admin-empty-state
                title="Actieve filters"
                :description="'Resultaten gefilterd op: ' . implode(' | ', $activeFilters)"
            />
        @endif

        @if ($users->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Organisatie</th>
                            <th>Rol</th>
                            <th>Email verified</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->organization?->naam ?? 'Geen organisatie' }}</td>
                                <td><x-admin-status-badge :label="$user->role" /></td>
                                <td class="muted">{{ $user->email_verified_at?->format('d-m-Y H:i') ?? 'Niet geverifieerd' }}</td>
                                <td>
                                    <x-admin-row-actions>
                                        <x-admin-icon-link
                                            :href="route('admin.users.edit', $user)"
                                            :label="'Wijzig ' . $user->name"
                                            title="Wijzigen"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                            </svg>
                                        </x-admin-icon-link>

                                        <x-admin-icon-link
                                            :href="route('admin.users.confirm-delete', $user)"
                                            :label="'Verwijder ' . $user->name"
                                            title="Verwijderen"
                                            variant="danger"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4h8v2"/>
                                                <path d="M19 6l-1 14H6L5 6"/>
                                                <path d="M10 11v6"/>
                                                <path d="M14 11v6"/>
                                            </svg>
                                        </x-admin-icon-link>
                                    </x-admin-row-actions>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin-results-meta :paginator="$users" aria-label="Paginatie" />
        @else
            <x-admin-empty-state
                :title="$activeFilters !== [] ? 'Er zijn geen gebruikers gevonden voor deze filtercombinatie.' : 'Geen gebruikers gevonden.'"
                :description="$activeFilters !== [] ? 'Pas een of meer filters aan of reset de lijst om alle gebruikers weer te zien.' : 'Voeg een gebruiker toe of probeer het later opnieuw.'"
            >
                <x-slot:actions>
                    @if ($activeFilters !== [])
                        <a href="{{ route('admin.users.index') }}" class="ghost-pill">Reset</a>
                    @endif
                    <a href="{{ route('admin.users.create') }}" class="pill">Nieuwe gebruiker</a>
                </x-slot:actions>
            </x-admin-empty-state>
        @endif
    </section>
</x-layouts.hermes-admin>
