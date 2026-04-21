<x-layouts.hermes-admin
    title="Beschikbaarheid · {{ $questionnaire->title }}"
    eyebrow="Questionnaires"
    :heading="$questionnaire->title"
    lead="Beheer per organisatie de beschikbaarheid, start- en einddatum van deze questionnaire."
    menu-active="questionnaires"
>
    <style>
        .page-actions,
        .row-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .page-actions {
            justify-content: space-between;
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
            min-width: 680px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
            vertical-align: middle;
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

        .muted {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
        }

        .inline-form {
            margin: 0;
        }

        .empty-state {
            padding: 48px 24px;
            text-align: center;
        }

        .meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 22px;
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

        <div class="page-actions">
            <a href="{{ route('admin.questionnaires.index') }}" class="ghost-pill">← Terug naar overzicht</a>

            <div class="row-actions">
                @if ($canManageLibrary)
                    <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="ghost-pill">Questionnaire bewerken</a>
                @endif
                <a href="{{ route('admin.questionnaires.availability.create', $questionnaire) }}" class="pill">
                    Organisatie koppelen
                </a>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Organisatie</th>
                        <th>Beschikbaar van</th>
                        <th>Beschikbaar tot</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($linkages as $linkage)
                        <tr>
                            <td>
                                <strong>{{ $linkage->organization?->naam ?? '—' }}</strong>
                            </td>
                            <td>
                                <span class="{{ $linkage->available_from ? '' : 'muted' }}">
                                    {{ $linkage->available_from?->format('d-m-Y') ?? 'Geen startdatum' }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $linkage->available_until ? '' : 'muted' }}">
                                    {{ $linkage->available_until?->format('d-m-Y') ?? 'Geen einddatum' }}
                                </span>
                            </td>
                            <td>
                                <x-admin-status-badge
                                    :label="$linkage->is_active ? 'Actief' : 'Inactief'"
                                    :tone="$linkage->is_active ? 'default' : 'warning'"
                                />
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.questionnaires.availability.edit', [$questionnaire, $linkage]) }}" class="ghost-pill">
                                        Bewerken
                                    </a>

                                    <form method="POST" action="{{ route('admin.questionnaires.availability.toggle', [$questionnaire, $linkage->org_id]) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="ghost-pill">
                                            {{ $linkage->is_active ? 'Deactiveren' : 'Activeren' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.questionnaires.availability.destroy', [$questionnaire, $linkage]) }}" class="inline-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="danger-pill">Verwijderen</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="muted">Nog geen organisaties gekoppeld aan deze questionnaire.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (! $questionnaire->is_active)
            <div class="meta">
                <div class="muted">
                    Let op: deze questionnaire staat inactief en is daardoor niet zichtbaar voor gebruikers, ook niet met een actieve koppeling.
                </div>
            </div>
        @endif
    </section>
</x-layouts.hermes-admin>
