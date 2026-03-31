<x-layouts.hermes-admin
    title="Admin Academy"
    eyebrow="Academy"
    heading="Academy-catalogus"
    lead="Beheer hier alle e-learnings in de Academy. Alleen globale admins kunnen deze bibliotheek zien, publiceren en onderhouden."
    menu-active="academy-courses"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$academyCourses->total()"
            description="Academy-cursussen in de database"
        />
        <x-hermes-fact
            :title="$academyCourses->where('is_active', true)->count()"
            description="Actieve cursussen op deze pagina"
        />
        <x-hermes-fact
            title="JSON"
            description="Meertalige content per cursus"
        />
    </x-slot:heroFacts>

    <style>
        .toolbar,
        .actions,
        .meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar,
        .meta {
            align-items: center;
            justify-content: space-between;
        }

        .toolbar {
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
            min-width: 980px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
            vertical-align: top;
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

        .badge--inactive {
            background: rgba(168, 74, 25, 0.12);
            color: var(--accent-deep);
        }

        .muted {
            color: var(--muted);
        }

        .meta {
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
    </style>

    <section class="content-panel">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <div class="toolbar">
            <div class="muted">
                Elke cursus bevat metadata, een map-pad naar de web-export en content in vier talen.
            </div>

            <div class="actions">
                <a href="{{ route('admin.academy-courses.create') }}" class="pill">Nieuwe Academy-cursus</a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cursus</th>
                        <th>Status</th>
                        <th>Pad</th>
                        <th>Duur</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($academyCourses as $academyCourse)
                        <tr>
                            <td>
                                <strong>{{ $academyCourse->titleForLocale('nl') }}</strong>
                                <div class="muted">{{ $academyCourse->summaryForLocale('nl') }}</div>
                            </td>
                            <td>
                                <span @class(['badge', 'badge--inactive' => ! $academyCourse->is_active])>
                                    {{ $academyCourse->is_active ? 'Actief' : 'Inactief' }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $academyCourse->path }}</div>
                                <div class="muted">{{ $academyCourse->isAvailable() ? 'Web-export gevonden' : 'Nog geen exportbestand gevonden' }}</div>
                            </td>
                            <td>{{ $academyCourse->estimated_minutes }} minuten</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.academy-courses.edit', $academyCourse) }}" class="ghost-pill">Wijzigen</a>
                                    <a href="{{ route('admin.academy-courses.confirm-delete', $academyCourse) }}" class="danger-pill">Verwijderen</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">Er zijn nog geen Academy-cursussen opgeslagen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="meta">
            <span class="muted">Resultaten {{ $academyCourses->firstItem() ?? 0 }} t/m {{ $academyCourses->lastItem() ?? 0 }} van {{ $academyCourses->total() }}</span>

            @if ($academyCourses->hasPages())
                <div class="pagination">
                    @if ($academyCourses->onFirstPage())
                        <span class="pagination__link">Vorige</span>
                    @else
                        <a href="{{ $academyCourses->previousPageUrl() }}" class="pagination__link">Vorige</a>
                    @endif

                    <span class="pagination__current">{{ $academyCourses->currentPage() }}</span>

                    @if ($academyCourses->hasMorePages())
                        <a href="{{ $academyCourses->nextPageUrl() }}" class="pagination__link">Volgende</a>
                    @else
                        <span class="pagination__link">Volgende</span>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts.hermes-admin>
