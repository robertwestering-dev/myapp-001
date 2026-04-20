<x-layouts.hermes-admin
    title="Admin auditlog"
    eyebrow=""
    heading="Auditlog"
    lead="Overzicht van alle beheerderactiviteiten in de applicatie. Gesorteerd op meest recent."
    menu-active="audit-logs"
    :show-secondary-menu-items="false"
    :show-hero="false"
>
    <section>
        <form method="GET" action="{{ route('admin.audit-logs.index') }}">
            <x-admin-toolbar>
                <x-admin-toolbar-group>
                    <div class="search">
                        <div class="admin-filter-field">
                            <label class="admin-filter-field__label" for="search">Zoekterm</label>
                            <input
                                id="search"
                                name="search"
                                type="search"
                                value="{{ $search }}"
                                placeholder="Zoek op omschrijving"
                                class="admin-filter-control"
                            />
                        </div>
                    </div>

                    <div class="admin-filter-field">
                        <label class="admin-filter-field__label" for="action">Actie</label>
                        <select id="action" name="action" class="admin-filter-control">
                            <option value="">Alle acties</option>
                            @foreach ($actions as $actionOption)
                                <option value="{{ $actionOption }}" @selected($selectedAction === $actionOption)>
                                    {{ $actionOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </x-admin-toolbar-group>

                <x-admin-toolbar-group>
                    <div class="admin-filter-actions">
                        <button type="submit" class="pill">Filteren</button>
                        @if ($search !== '' || $selectedAction !== '')
                            <a href="{{ route('admin.audit-logs.index') }}" class="ghost-pill">Reset</a>
                        @endif
                    </div>
                </x-admin-toolbar-group>
            </x-admin-toolbar>
        </form>

        @if ($logs->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Beheerder</th>
                            <th>Actie</th>
                            <th>Omschrijving</th>
                            <th>IP-adres</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="muted">{{ $log->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $log->user?->name ?? '–' }}</td>
                                <td><x-admin-status-badge :label="$log->action" /></td>
                                <td>{{ $log->description }}</td>
                                <td class="muted">{{ $log->ip_address ?? '–' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin-results-meta :paginator="$logs" aria-label="Paginatie auditlog" />
        @else
            <x-admin-empty-state
                title="Geen activiteit gevonden."
                description="Er zijn nog geen beheerderactiviteiten geregistreerd of de filters geven geen resultaten."
            >
                @if ($search !== '' || $selectedAction !== '')
                    <x-slot:actions>
                        <a href="{{ route('admin.audit-logs.index') }}" class="ghost-pill">Reset filters</a>
                    </x-slot:actions>
                @endif
            </x-admin-empty-state>
        @endif
    </section>
</x-layouts.hermes-admin>
