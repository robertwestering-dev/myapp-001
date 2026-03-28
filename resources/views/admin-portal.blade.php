<x-layouts.hermes-admin
    title="Admin-portal"
    eyebrow="Admin-portal"
    heading="Welkom terug, beheerder."
    :lead="$lead"
    menu-active="portal"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$actor->role"
            description="Actieve rol in deze sessie"
        />
        <x-hermes-fact
            :title="$questionnaireCount"
            description="Questionnaires in de bibliotheek"
        />
        <x-hermes-fact
            :title="$scopedAvailabilityCount"
            description="Beschikbaarheid binnen uw scope"
        />
    </x-slot:heroFacts>

    <style>
        .portal-grid,
        .spotlight-grid,
        .spotlight-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .portal-grid {
            margin-top: 28px;
        }

        .portal-card,
        .spotlight-card {
            flex: 1 1 280px;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
        }

        .spotlight-section {
            margin-top: 28px;
        }

        .spotlight-grid {
            margin-top: 16px;
        }

        .spotlight-card {
            display: grid;
            gap: 16px;
        }

        .spotlight-meta {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.94rem;
        }
    </style>

    <section class="content-panel">
        <div class="portal-grid">
            <article class="portal-card">
                <h2>Questionnairebibliotheek</h2>
                <p>Beheer de centrale bibliotheek en stel assessments beschikbaar voor organisaties binnen de juiste scope.</p>
                <a href="{{ route('admin.questionnaires.index') }}" class="pill">Open questionnaires</a>
            </article>

            <article class="portal-card">
                <h2>Responses en rapportage</h2>
                <p>Bekijk ingevulde assessments, filter op questionnaire of organisatie en open direct de statistiek- en exportroutes.</p>
                <a href="{{ route('admin.questionnaire-responses.index') }}" class="pill">Open responses</a>
            </article>
        </div>

        <section class="spotlight-section">
            <span class="eyebrow">Baseline assessments</span>
            <h2>Twee quick scans staan nu centraal in de portal</h2>
            <p>Gebruik deze shortcuts om de A.C.E.-scan en de verdieping op digitale weerbaarheid direct te openen, beschikbaar te stellen of te analyseren.</p>

            <div class="spotlight-grid">
                @foreach ($spotlightQuestionnaires as $questionnaire)
                    <article class="spotlight-card">
                        <div>
                            <h3>{{ $questionnaire->title }}</h3>
                            <p>{{ $questionnaire->description }}</p>
                        </div>

                        <div class="spotlight-meta">
                            {{ $questionnaire->categories_count }} categorieen · {{ $questionnaire->questions_count }} vragen ·
                            {{ $questionnaire->scoped_organization_questionnaires_count }} koppelingen in uw scope
                        </div>

                        <div class="spotlight-actions">
                            <a href="{{ route('admin.questionnaire-responses.index', ['questionnaire_id' => $questionnaire->id]) }}" class="ghost-pill">
                                Bekijk responses
                            </a>
                            <a href="{{ route('admin.questionnaire-responses.stats', ['questionnaire_id' => $questionnaire->id]) }}" class="ghost-pill">
                                Bekijk statistieken
                            </a>
                            @if ($canManageLibrary)
                                <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="pill">Open in bibliotheek</a>
                            @else
                                <a href="{{ route('admin.questionnaires.index') }}" class="pill">Open in bibliotheek</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </section>
</x-layouts.hermes-admin>
