<x-layouts.hermes-admin
    :title="$title"
    eyebrow="Questionnaires"
    :heading="$title"
    :lead="$intro"
    menu-active="questionnaires"
>
    <style>
        form {
            display: grid;
            gap: 18px;
            margin-top: 28px;
        }

        label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        input,
        select {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkbox input,
        .availability-table input[type='checkbox'] {
            width: auto;
            margin: 0;
        }

        .form-actions,
        .inline-link-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .availability-blocks {
            display: grid;
            gap: 24px;
            margin-top: 28px;
        }

        .availability-card {
            display: grid;
            gap: 16px;
            padding: 22px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.52);
        }

        .availability-card__header {
            display: grid;
            gap: 6px;
        }

        .availability-card__title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            font-weight: 700;
        }

        .help-text,
        .muted {
            font-family: Arial, Helvetica, sans-serif;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .current-link {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.7);
        }

        .availability-table {
            display: grid;
            gap: 12px;
        }

        .availability-table__head,
        .availability-row {
            display: grid;
            grid-template-columns: minmax(220px, 1.5fr) minmax(140px, 1fr) minmax(140px, 1fr) minmax(120px, 0.8fr);
            gap: 12px;
            align-items: center;
        }

        .availability-table__head {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 0 4px;
        }

        .availability-row {
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
        }

        .availability-row__organization {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .availability-row__organization input {
            margin-top: 2px;
        }

        .availability-row__field {
            display: grid;
            gap: 6px;
        }

        .availability-row__field label {
            gap: 6px;
            font-size: 0.85rem;
        }

        @media (max-width: 860px) {
            .availability-table__head {
                display: none;
            }

            .availability-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="content-panel">
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <p><strong>Questionnaire:</strong> {{ $questionnaire->title }}</p>

        @if (! $questionnaire->is_active)
            <div class="errors">
                <div>Deze questionnaire staat momenteel inactief in de bibliotheek. Gebruikers zien deze pas op hun dashboard zodra de questionnaire zelf ook actief is.</div>
                <div>Actieve beschikbaarheid kan daarom nu niet worden opgeslagen zolang de questionnaire zelf inactief is.</div>
                @if (request()->user()?->isAdmin())
                    <div><a href="{{ route('admin.questionnaires.edit', $questionnaire) }}">Open de questionnaire en activeer deze eerst.</a></div>
                @endif
            </div>
        @endif

        <div class="availability-blocks">
            <div class="availability-card">
                <div class="availability-card__header">
                    <div class="availability-card__title">
                        {{ $isEditing ? 'Beschikbaarheid huidige koppeling' : 'Nieuwe organisatiekoppelingen' }}
                    </div>
                    <div class="help-text">
                        {{ $isEditing
                            ? 'Werk alleen de periode en activatie van deze bestaande koppeling bij.'
                            : 'Selecteer per organisatie een koppeling en bepaal direct de eigen periode en activatie.' }}
                    </div>
                </div>

                @if ($isEditing)
                    <div class="current-link">
                        <strong>Gekoppeld aan:</strong> {{ $organizations[$availability->org_id] ?? $availability->organization?->naam ?? 'Onbekende organisatie' }}
                    </div>
                @endif

                <form method="POST" action="{{ $isEditing ? route('admin.questionnaires.availability.update', [$questionnaire, $availability]) : route('admin.questionnaires.availability.store', $questionnaire) }}">
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    @if (! $isEditing && request()->user()?->isAdmin())
                        <div class="availability-table">
                            <div class="availability-table__head">
                                <span>Selectie</span>
                                <span>Beschikbaar vanaf</span>
                                <span>Beschikbaar tot</span>
                                <span>Actief</span>
                            </div>

                            @foreach ($additionalOrganizations as $organizationId => $organizationName)
                                <div class="availability-row">
                                    <div class="availability-row__organization">
                                        <input
                                            type="checkbox"
                                            name="org_ids[]"
                                            value="{{ $organizationId }}"
                                            @checked(in_array((string) $organizationId, collect(old('org_ids', []))->map(fn ($value) => (string) $value)->all(), true))
                                        >
                                        <span>{{ $organizationName }}</span>
                                    </div>

                                    <div class="availability-row__field">
                                        <label>
                                            <span class="muted">Vanaf</span>
                                            <input type="date" name="available_from_by_org[{{ $organizationId }}]" value="{{ old("available_from_by_org.{$organizationId}") }}">
                                        </label>
                                    </div>

                                    <div class="availability-row__field">
                                        <label>
                                            <span class="muted">Tot</span>
                                            <input type="date" name="available_until_by_org[{{ $organizationId }}]" value="{{ old("available_until_by_org.{$organizationId}") }}">
                                        </label>
                                    </div>

                                    <div class="availability-row__field">
                                        <label class="checkbox">
                                            <input type="checkbox" name="is_active_by_org[{{ $organizationId }}]" value="1" @checked(old("is_active_by_org.{$organizationId}", true))>
                                            <span>Actief</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        @if (! request()->user()?->isAdmin())
                            <input type="hidden" name="org_id" value="{{ old('org_id', request()->user()?->org_id) }}">

                            <div class="current-link">
                                <strong>Eigen organisatie:</strong> {{ $organizations[request()->user()?->org_id] ?? 'Eigen organisatie' }}
                            </div>
                        @endif

                        <label>
                            <span>Beschikbaar vanaf</span>
                            <input type="date" name="available_from" value="{{ old('available_from', $availability->available_from?->toDateString()) }}">
                        </label>

                        <label>
                            <span>Beschikbaar tot</span>
                            <input type="date" name="available_until" value="{{ old('available_until', $availability->available_until?->toDateString()) }}">
                        </label>

                        <label class="checkbox">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $availability->is_active))>
                            <span>Questionnaire is actief beschikbaar voor deze organisatie</span>
                        </label>
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="pill">{{ $submitLabel }}</button>
                        <a href="{{ route('admin.questionnaires.index') }}" class="ghost-pill">Terug naar overzicht</a>
                    </div>
                </form>
            </div>

            @if ($isEditing && request()->user()?->isAdmin())
                <div class="availability-card">
                    <div class="availability-card__header">
                        <div class="availability-card__title">Extra organisaties koppelen</div>
                        <div class="help-text">Selecteer extra organisaties en stel per organisatie meteen de eigen beschikbaarheidsperiode en activatie in.</div>
                    </div>

                    @if ($additionalOrganizations === [])
                        <div class="muted">
                            Er zijn geen extra organisaties meer beschikbaar om te koppelen.
                            @if ($linkedOrganizations !== [])
                                Deze questionnaire is al gekoppeld aan: {{ implode(', ', $linkedOrganizations) }}.
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.questionnaires.availability.store', $questionnaire) }}">
                            @csrf

                            <div class="availability-table">
                                <div class="availability-table__head">
                                    <span>Selectie</span>
                                    <span>Beschikbaar vanaf</span>
                                    <span>Beschikbaar tot</span>
                                    <span>Actief</span>
                                </div>

                                @foreach ($additionalOrganizations as $organizationId => $organizationName)
                                    <div class="availability-row">
                                        <div class="availability-row__organization">
                                            <input
                                                type="checkbox"
                                                name="org_ids[]"
                                                value="{{ $organizationId }}"
                                                @checked(in_array((string) $organizationId, collect(old('org_ids', []))->map(fn ($value) => (string) $value)->all(), true))
                                            >
                                            <span>{{ $organizationName }}</span>
                                        </div>

                                        <div class="availability-row__field">
                                            <label>
                                                <span class="muted">Vanaf</span>
                                                <input type="date" name="available_from_by_org[{{ $organizationId }}]" value="{{ old("available_from_by_org.{$organizationId}") }}">
                                            </label>
                                        </div>

                                        <div class="availability-row__field">
                                            <label>
                                                <span class="muted">Tot</span>
                                                <input type="date" name="available_until_by_org[{{ $organizationId }}]" value="{{ old("available_until_by_org.{$organizationId}") }}">
                                            </label>
                                        </div>

                                        <div class="availability-row__field">
                                            <label class="checkbox">
                                                <input type="checkbox" name="is_active_by_org[{{ $organizationId }}]" value="1" @checked(old("is_active_by_org.{$organizationId}", true))>
                                                <span>Actief</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="pill">Extra organisaties opslaan</button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif

            @if ($isEditing)
                <form method="POST" action="{{ route('admin.questionnaires.availability.destroy', [$questionnaire, $availability]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="danger-pill">
                        Beschikbaarheid voor deze organisatie verwijderen
                    </button>
                </form>
            @endif
        </div>
    </section>
</x-layouts.hermes-admin>
