<x-layouts.hermes-admin
    title="Questionnaire statistieken"
    eyebrow="Responses"
    heading="Statistieken voor {{ $questionnaire->title }}"
    lead="Bekijk direct in de app hoeveel responses zijn ingezonden en hoe keuzevragen zijn verdeeld."
    menu-active="questionnaire-responses"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$responseCount"
            description="Responses in de huidige selectie"
        />
        <x-hermes-fact
            :title="$statistics->sum(fn ($category) => $category['questions']->count())"
            description="Vragen in deze questionnaire"
        />
        <x-hermes-fact
            :title="$statistics->count()"
            description="Categorieen in beeld"
        />
    </x-slot:heroFacts>

    <style>
        .filters,
        .stats-grid,
        .samples {
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

        .category-section {
            display: grid;
            gap: 18px;
            margin-top: 28px;
        }

        .category-card,
        .question-card {
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        .category-card {
            padding: 24px;
        }

        .question-card {
            padding: 22px;
        }

        .stats-grid {
            align-items: stretch;
        }

        .question-card {
            flex: 1 1 320px;
            display: grid;
            align-content: start;
            gap: 16px;
        }

        .question-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .stat-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(22, 33, 29, 0.08);
            line-height: 1.1;
            white-space: nowrap;
            box-sizing: border-box;
        }

        .bar-list {
            display: grid;
            gap: 12px;
        }

        .bar-row {
            display: grid;
            gap: 8px;
        }

        .bar-label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .bar-track {
            overflow: hidden;
            height: 12px;
            border-radius: 999px;
            background: rgba(22, 33, 29, 0.08);
        }

        .bar-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-soft) 100%);
        }

        .samples {
            display: grid;
            align-content: start;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .samples-panel {
            display: grid;
            gap: 12px;
            padding: 18px;
            border-radius: 20px;
            background: rgba(32, 69, 58, 0.06);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }

        .sample-pill {
            padding: 10px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(22, 33, 29, 0.08);
            color: var(--ink);
        }

        .muted {
            color: var(--muted);
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

        <form method="GET" action="{{ route('admin.questionnaire-responses.stats') }}" class="filters">
            <label>
                <span>Questionnaire</span>
                <select name="questionnaire_id">
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

            <button type="submit" class="pill">Ververs statistieken</button>
            <a href="{{ route('admin.questionnaire-responses.index', request()->only(['questionnaire_id', 'org_id', 'user_id'])) }}" class="ghost-pill">
                Terug naar responses
            </a>
            <a href="{{ route('admin.questionnaire-responses.export-stats', request()->only(['questionnaire_id', 'org_id', 'user_id'])) }}" class="ghost-pill">
                Export statistiek CSV
            </a>
        </form>

        @forelse ($statistics as $categoryStats)
            <section class="category-section">
                <div class="category-card">
                    <h2>{{ $categoryStats['category']->title }}</h2>
                    <p class="muted">{{ $categoryStats['questions']->count() }} vragen in deze categorie.</p>
                </div>

                <div class="stats-grid">
                    @foreach ($categoryStats['questions'] as $questionStats)
                        <article class="question-card">
                            <div>
                                <div class="muted">{{ $questionStats['type_label'] }}</div>
                                <h3>{{ $questionStats['question']->prompt }}</h3>
                            </div>

                            <div class="question-meta">
                                <span class="stat-chip">{{ $questionStats['answered_count'] }} van {{ $responseCount }} ingevuld</span>
                                <span class="stat-chip">{{ $questionStats['answered_percentage'] }}% respons</span>
                            </div>

                            @if ($questionStats['options']->isNotEmpty())
                                <div class="bar-list">
                                    @foreach ($questionStats['options'] as $optionStats)
                                        <div class="bar-row">
                                            <div class="bar-label">
                                                <span>{{ $optionStats['label'] }}</span>
                                                <span>{{ $optionStats['count'] }} · {{ $optionStats['percentage'] }}%</span>
                                            </div>
                                            <div class="bar-track" aria-hidden="true">
                                                <div class="bar-fill" style="width: {{ $optionStats['percentage'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($questionStats['latest_answers']->isNotEmpty())
                                <div class="samples-panel">
                                    <div class="muted">Recente antwoorden</div>
                                    <ul class="samples">
                                        @foreach ($questionStats['latest_answers'] as $answer)
                                            <li class="sample-pill">{{ $answer }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="muted">Nog geen antwoorden binnen voor deze vraag.</div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="category-card">
                <strong>Geen statistieken beschikbaar</strong>
                <p class="muted">Deze questionnaire bevat nog geen vragen of responses voor de huidige selectie.</p>
            </div>
        @endforelse
    </section>
</x-layouts.hermes-admin>
