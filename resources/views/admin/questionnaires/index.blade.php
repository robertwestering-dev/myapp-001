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
        .row-actions,
        .availability-actions {
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

        .availability-list {
            display: grid;
            gap: 8px;
        }

        .availability-list__head,
        .availability-list__row {
            display: grid;
            grid-template-columns: minmax(160px, 1.5fr) minmax(108px, 0.8fr) minmax(108px, 0.8fr) minmax(88px, 0.7fr) auto;
            gap: 10px;
            align-items: center;
        }

        .availability-list__head {
            padding: 0 4px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .availability-list__row {
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.68);
        }

        .availability-actions {
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
        }

        .icon-button--danger {
            color: #8a2c2c;
            border-color: rgba(138, 44, 44, 0.18);
            background: rgba(255, 244, 244, 0.92);
        }

        .icon-button svg {
            width: 16px;
            height: 16px;
        }

        .availability-state {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
        }

        .availability-state--empty {
            color: var(--muted);
        }

        .icon-button[aria-disabled='true'] {
            pointer-events: none;
            opacity: 0.45;
        }

        @media (max-width: 1120px) {
            .availability-list__head {
                display: none;
            }

            .availability-list__row {
                grid-template-columns: 1fr;
                justify-items: start;
            }

            .availability-actions {
                justify-content: flex-start;
            }
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
                        <th>Opbouw</th>
                        <th>Beschikbaarheid</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questionnaires as $questionnaire)
                        @php($availabilityRows = $questionnaire->organizationQuestionnaires->sortBy(fn ($availability) => $availability->organization?->naam ?? ''))
                        @php($ownAvailability = $availabilityRows->first())
                        @php($availabilitiesByOrganization = $availabilityRows->keyBy('org_id'))
                        <tr>
                            <td>
                                <strong>{{ $questionnaire->title }}</strong>
                                <div class="muted">{{ $questionnaire->description ?: 'Geen beschrijving toegevoegd.' }}</div>
                                <div class="muted">Taal {{ strtoupper($questionnaire->locale ?? 'NL') }}</div>
                            </td>
                            <td>
                                <x-admin-status-badge
                                    :label="$questionnaire->is_active ? 'Actief' : 'Inactief'"
                                    :tone="$questionnaire->is_active ? 'default' : 'warning'"
                                />
                            </td>
                            <td>
                                <div>{{ $questionnaire->categories_count }} categorieen</div>
                                <div class="muted">{{ $questionnaire->questions_count }} vragen</div>
                            </td>
                            <td>
                                @if ($organizationOptions !== [])
                                    <div class="availability-list">
                                        <div class="availability-list__head">
                                            <span>Organisatie</span>
                                            <span>Van</span>
                                            <span>Tot</span>
                                            <span>Status</span>
                                            <span>Acties</span>
                                        </div>

                                        @foreach ($organizationOptions as $organizationId => $organizationName)
                                            @php($availability = $availabilitiesByOrganization->get($organizationId))
                                            <div class="availability-list__row">
                                                <div>{{ $organizationName }}</div>
                                                <div class="muted">{{ $availability?->available_from?->format('d-m-Y') ?? '-' }}</div>
                                                <div class="muted">{{ $availability?->available_until?->format('d-m-Y') ?? '-' }}</div>
                                                <div class="availability-state {{ $availability === null ? 'availability-state--empty' : ($availability->is_active ? '' : 'muted') }}">
                                                    @if ($availability === null)
                                                        Niet gekoppeld
                                                    @elseif ($availability->is_active)
                                                        Actief
                                                    @else
                                                        Inactief
                                                    @endif
                                                </div>
                                                <div class="availability-actions">
                                                    @if ($availability)
                                                        <a
                                                            href="{{ route('admin.questionnaires.availability.edit', [$questionnaire, $availability]) }}"
                                                            class="icon-button"
                                                            title="Wijzigen"
                                                            aria-label="Beschikbaarheid wijzigen voor {{ $organizationName }}"
                                                        >
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M12 20h9" />
                                                                <path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                            </svg>
                                                        </a>

                                                        <form method="POST" action="{{ route('admin.questionnaires.availability.destroy', [$questionnaire, $availability]) }}" class="inline-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="icon-button icon-button--danger"
                                                                title="Verwijderen"
                                                                aria-label="Beschikbaarheid verwijderen voor {{ $organizationName }}"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                    <path d="M3 6h18" />
                                                                    <path d="M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6" />
                                                                    <path d="M6.5 6 7.3 19A2 2 0 0 0 9.3 21h5.4a2 2 0 0 0 2-2L17.5 6" />
                                                                    <path d="M10 10.5v6" />
                                                                    <path d="M14 10.5v6" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="icon-button" aria-disabled="true" title="Wijzigen niet beschikbaar">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M12 20h9" />
                                                                <path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                            </svg>
                                                        </span>

                                                        <span class="icon-button icon-button--danger" aria-disabled="true" title="Verwijderen niet beschikbaar">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M3 6h18" />
                                                                <path d="M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6" />
                                                                <path d="M6.5 6 7.3 19A2 2 0 0 0 9.3 21h5.4a2 2 0 0 0 2-2L17.5 6" />
                                                                <path d="M10 10.5v6" />
                                                                <path d="M14 10.5v6" />
                                                            </svg>
                                                        </span>
                                                    @endif

                                                    <form method="POST" action="{{ route('admin.questionnaires.availability.toggle', [$questionnaire, $organizationId]) }}" class="inline-form">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="icon-button {{ $availability?->is_active ? 'icon-button--danger' : '' }}"
                                                            title="{{ $availability?->is_active ? 'Deactiveren' : 'Activeren' }}"
                                                            aria-label="Beschikbaarheid {{ $availability?->is_active ? 'deactiveren' : 'activeren' }} voor {{ $organizationName }}"
                                                        >
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M12 3v9" />
                                                                <path d="M7.05 5.05a9 9 0 1 0 9.9 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if (! $questionnaire->is_active)
                                        <div class="muted">Niet zichtbaar voor gebruikers zolang de questionnaire zelf op inactief staat.</div>
                                    @endif
                                @else
                                    <div>{{ $questionnaire->organization_questionnaires_count }} organisatiekoppelingen</div>
                                    <div class="muted">Nog geen organisaties beschikbaar in deze scope</div>
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    @if ($ownAvailability)
                                        <a href="{{ route('admin.questionnaires.availability.edit', [$questionnaire, $ownAvailability]) }}" class="ghost-pill">
                                            Beschikbaarheid
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.questionnaires.availability.create', $questionnaire) }}" class="ghost-pill">
                                        {{ $ownAvailability ? 'Extra organisaties koppelen' : 'Beschikbaar stellen' }}
                                    </a>

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
