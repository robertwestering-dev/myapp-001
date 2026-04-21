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
        .spotlight-grid,
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

        .spotlight-grid {
            margin: 0 0 24px;
        }

        .spotlight-card {
            flex: 1 1 320px;
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
            display: grid;
            gap: 14px;
        }

        .spotlight-card__actions {
            display: flex;
            gap: 12px;
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
            min-width: 580px;
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

        <div class="spotlight-grid">
            @foreach ($spotlightQuestionnaires as $spotlightQuestionnaire)
                @php($spotlightAvailability = $spotlightQuestionnaire->organizationQuestionnaires->first())
                <article class="spotlight-card">
                    <div>
                        <span class="eyebrow">Uitgelicht</span>
                    </div>
                    <div>
                        <strong>{{ $spotlightQuestionnaire->title }}</strong>
                        <div class="muted">{{ $spotlightQuestionnaire->description }}</div>
                    </div>
                    <div class="muted">
                        {{ $spotlightQuestionnaire->categories_count }} categorieen · {{ $spotlightQuestionnaire->questions_count }} vragen ·
                        {{ $spotlightQuestionnaire->organization_questionnaires_count }} organisatiekoppelingen
                    </div>
                    @if (! $spotlightQuestionnaire->is_active)
                        <div class="muted">Let op: deze questionnaire staat inactief in de bibliotheek en is daardoor niet zichtbaar voor gebruikers, ook niet met een organisatiekoppeling.</div>
                    @endif
                    <div class="spotlight-card__actions">
                        @if ($spotlightAvailability)
                            <a href="{{ route('admin.questionnaires.availability.edit', [$spotlightQuestionnaire, $spotlightAvailability]) }}" class="ghost-pill">
                                Beschikbaarheid
                            </a>
                        @endif

                        <a href="{{ route('admin.questionnaires.availability.create', $spotlightQuestionnaire) }}" class="ghost-pill">
                            {{ $spotlightAvailability ? 'Extra organisaties koppelen' : 'Beschikbaar stellen' }}
                        </a>

                        <a href="{{ route('admin.questionnaire-responses.index', ['questionnaire_id' => $spotlightQuestionnaire->id]) }}" class="ghost-pill">
                            Bekijk responses
                        </a>

                        @if ($canManageLibrary)
                            <a href="{{ route('admin.questionnaires.edit', $spotlightQuestionnaire) }}" class="pill">Open questionnaire</a>
                        @else
                            <a href="{{ route('admin.questionnaires.index') }}" class="pill">In bibliotheek</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Questionnaire</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questionnaires as $questionnaire)
                        <tr>
                            <td>
                                <strong>{{ $questionnaire->title }}</strong>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                    <x-admin-status-badge
                                        :label="$questionnaire->is_active ? 'Actief' : 'Inactief'"
                                        :tone="$questionnaire->is_active ? 'default' : 'warning'"
                                    />
                                    @if ($questionnaire->pro_only)
                                        <x-admin-status-badge label="PRO" tone="warning" uppercase />
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="row-actions">
                                    @if ($canManageLibrary)
                                        <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="pill">
                                            Open questionnaire
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.questionnaires.availability.index', $questionnaire) }}" class="ghost-pill">
                                        Beschikbaarheid
                                    </a>

                                    @if ($canManageLibrary)
                                        <form method="POST" action="{{ route('admin.questionnaires.toggle', $questionnaire) }}" class="inline-form">
                                            @csrf
                                            <button type="submit" class="ghost-pill">
                                                {{ $questionnaire->is_active ? 'Deactiveren' : 'Activeren' }}
                                            </button>
                                        </form>

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
                            <td colspan="3">Er zijn nog geen questionnaires toegevoegd.</td>
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
