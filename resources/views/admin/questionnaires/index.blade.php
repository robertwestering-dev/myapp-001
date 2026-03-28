<x-layouts.hermes-admin
    title="Admin questionnaires"
    eyebrow="Questionnaires"
    heading="Questionnaire-overzicht"
    lead="Beheer de standaardquestionnaires die door admins worden samengesteld en per organisatie beschikbaar kunnen worden gesteld."
    menu-active="questionnaires"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$questionnaires->total()"
            description="Questionnaires in de bibliotheek"
        />
        <x-hermes-fact
            :title="$questionnaires->sum('questions_count')"
            description="Vragen in de huidige selectie"
        />
        <x-hermes-fact
            title="Scoped"
            description="Beschikbaarheid blijft per organisatie afgebakend"
        />
    </x-slot:heroFacts>

    <style>
        .toolbar,
        .actions,
        .meta,
        .row-actions {
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
            min-width: 860px;
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

        .inline-form {
            margin: 0;
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
            <div class="muted">
                Elke questionnaire bestaat uit categorieen en vragen. Alleen admins wijzigen de bibliotheek.
            </div>

            @if ($canManageLibrary)
                <div class="actions">
                    <a href="{{ route('admin.questionnaires.create') }}" class="pill">Nieuwe questionnaire</a>
                </div>
            @endif
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Questionnaire</th>
                        <th>Status</th>
                        <th>Opbouw</th>
                        <th>Beschikbaarheid</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questionnaires as $questionnaire)
                        @php($ownAvailability = $questionnaire->organizationQuestionnaires->first())
                        <tr>
                            <td>
                                <strong>{{ $questionnaire->title }}</strong>
                                <div class="muted">{{ $questionnaire->description ?: 'Geen beschrijving toegevoegd.' }}</div>
                            </td>
                            <td>
                                <span @class(['badge', 'badge--inactive' => ! $questionnaire->is_active])>
                                    {{ $questionnaire->is_active ? 'Actief' : 'Inactief' }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $questionnaire->categories_count }} categorieen</div>
                                <div class="muted">{{ $questionnaire->questions_count }} vragen</div>
                            </td>
                            <td>
                                @if ($ownAvailability)
                                    <div>Actief voor {{ $ownAvailability->organization?->naam ?? 'organisatie' }}</div>
                                    <div class="muted">
                                        {{ $ownAvailability->is_active ? 'Beschikbaar' : 'Niet beschikbaar' }}
                                        @if ($ownAvailability->available_from)
                                            vanaf {{ $ownAvailability->available_from->format('d-m-Y') }}
                                        @endif
                                    </div>
                                @else
                                    <div>{{ $questionnaire->organization_questionnaires_count }} organisatiekoppelingen</div>
                                    <div class="muted">Nog geen eigen koppeling in deze scope</div>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    @if ($ownAvailability)
                                        <a href="{{ route('admin.questionnaires.availability.edit', [$questionnaire, $ownAvailability]) }}" class="ghost-pill">
                                            Beschikbaarheid
                                        </a>
                                    @else
                                        <a href="{{ route('admin.questionnaires.availability.create', $questionnaire) }}" class="ghost-pill">
                                            Beschikbaar stellen
                                        </a>
                                    @endif

                                    @if ($canManageLibrary)
                                        <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="ghost-pill">Bewerken</a>

                                        <form method="POST" action="{{ route('admin.questionnaires.destroy', $questionnaire) }}" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-pill">Verwijderen</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Er zijn nog geen questionnaires toegevoegd.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="meta">
            <div class="muted">
                Resultaten {{ $questionnaires->firstItem() ?? 0 }} t/m {{ $questionnaires->lastItem() ?? 0 }} van {{ $questionnaires->total() }}
            </div>

            @if ($questionnaires->hasPages())
                <nav class="pagination" aria-label="Paginering">
                    @foreach ($questionnaires->linkCollection() as $link)
                        @if ($link['url'] === null)
                            <span class="pagination__current">{{ $link['label'] }}</span>
                        @elseif ($link['active'])
                            <span class="pagination__current">{{ $link['label'] }}</span>
                        @else
                            <a href="{{ $link['url'] }}" class="pagination__link">{!! $link['label'] !!}</a>
                        @endif
                    @endforeach
                </nav>
            @endif
        </div>
    </section>
</x-layouts.hermes-admin>
