<x-layouts.hermes-admin
    title="Questionnaire responses"
    eyebrow="Responses"
    heading="Ingevulde questionnaires"
    lead="Bekijk per questionnaire, organisatie en gebruiker welke antwoorden zijn ingezonden."
    menu-active="questionnaire-responses"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$responses->total()"
            description="Responses in de huidige selectie"
        />
        <x-hermes-fact
            :title="count($questionnaires)"
            description="Beschikbare questionnaires als filter"
        />
        <x-hermes-fact
            title="Scope"
            description="Admins zien alles, beheerders alleen hun organisatie"
        />
    </x-slot:heroFacts>

    <style>
        .filters,
        .meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filters {
            align-items: end;
            margin: 28px 0 24px;
        }

        .filters label {
            min-width: min(100%, 220px);
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .filters select {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        table {
            width: 100%;
            min-width: 920px;
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
            align-items: center;
            justify-content: space-between;
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

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('admin.questionnaire-responses.index') }}" class="filters">
            <label>
                <span>Questionnaire</span>
                <select name="questionnaire_id">
                    <option value="">Alle questionnaires</option>
                    @foreach ($questionnaires as $id => $title)
                        <option value="{{ $id }}" @selected($questionnaireId === (int) $id)>{{ $title }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Organisatie</span>
                <select name="org_id">
                    <option value="">Alle organisaties</option>
                    @foreach ($organizations as $id => $name)
                        <option value="{{ $id }}" @selected($orgId === (int) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Gebruiker</span>
                <select name="user_id">
                    <option value="">Alle gebruikers</option>
                    @foreach ($users as $id => $label)
                        <option value="{{ $id }}" @selected($selectedUserId === (int) $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="pill">Filter</button>
            <a href="{{ route('admin.questionnaire-responses.index') }}" class="ghost-pill">Reset</a>
            <a
                href="{{ route('admin.questionnaire-responses.stats', request()->only(['questionnaire_id', 'org_id', 'user_id'])) }}"
                class="ghost-pill"
            >
                Bekijk statistieken
            </a>
            <a
                href="{{ route('admin.questionnaire-responses.export', request()->only(['questionnaire_id', 'org_id', 'user_id'])) }}"
                class="ghost-pill"
            >
                Export detail CSV
            </a>
            <a
                href="{{ route('admin.questionnaire-responses.export-summary', request()->only(['questionnaire_id', 'org_id', 'user_id'])) }}"
                class="ghost-pill"
            >
                Export samenvatting CSV
            </a>
            <a
                href="{{ route('admin.questionnaire-responses.export-stats', request()->only(['questionnaire_id', 'org_id', 'user_id'])) }}"
                class="ghost-pill"
            >
                Export statistiek CSV
            </a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Questionnaire</th>
                        <th>Organisatie</th>
                        <th>Gebruiker</th>
                        <th>Ingezonden op</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($responses as $response)
                        <tr>
                            <td>{{ $response->organizationQuestionnaire->questionnaire->title }}</td>
                            <td>{{ $response->organizationQuestionnaire->organization->naam }}</td>
                            <td>
                                <div>{{ $response->user->name }}</div>
                                <div class="muted">{{ $response->user->email }}</div>
                            </td>
                            <td>{{ $response->submitted_at?->format('d-m-Y H:i') ?? 'Niet ingezonden' }}</td>
                            <td>
                                <a href="{{ route('admin.questionnaire-responses.show', $response) }}" class="ghost-pill">Bekijk response</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Er zijn geen ingevulde questionnaires gevonden voor deze selectie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="meta">
            <div class="muted">
                Resultaten {{ $responses->firstItem() ?? 0 }} t/m {{ $responses->lastItem() ?? 0 }} van {{ $responses->total() }}
            </div>

            @if ($responses->hasPages())
                <nav class="pagination" aria-label="Paginering">
                    @foreach ($responses->linkCollection() as $link)
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
