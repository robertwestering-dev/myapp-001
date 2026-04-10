<x-layouts.hermes-admin
    title="Strategiepagina's"
    eyebrow="Strategie"
    heading="Interne strategiepagina's"
    lead="Deze admin-only pagina's werken de voorgestelde vervolgstappen uit in concrete Nederlandse copy voor homepage, B2B, pricing en vertrouwen."
    menu-active="strategy-pages"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="count($pages)"
            description="Uitgewerkte pagina's"
        />
        <x-hermes-fact
            title="Admin"
            description="Alleen zichtbaar voor globale admins"
        />
        <x-hermes-fact
            title="NL"
            description="Copy uitgewerkt in het Nederlands"
        />
    </x-slot:heroFacts>

    <style>
        .strategy-grid,
        .strategy-links {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .strategy-grid {
            margin-top: 28px;
        }

        .strategy-card,
        .strategy-summary {
            flex: 1 1 300px;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
            display: grid;
            gap: 16px;
        }

        .strategy-summary {
            margin-top: 24px;
        }

        .strategy-meta {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.94rem;
        }
    </style>

    <section class="content-panel">
        <div class="strategy-grid">
            @foreach ($pages as $page)
                <article class="strategy-card">
                    <div>
                        <span class="eyebrow">{{ $page['eyebrow'] }}</span>
                        <h2>{{ $page['heading'] }}</h2>
                        <p>{{ $page['lead'] }}</p>
                    </div>

                    <div class="strategy-meta">{{ count($page['sections']) }} inhoudsblokken uitgewerkt</div>

                    <div class="strategy-links">
                        <a href="{{ route('admin.strategy-pages.show', $page['slug']) }}" class="pill">Open pagina</a>
                        <a href="{{ route('admin.strategy-pages.preview', $page['slug']) }}" class="ghost-pill">Open preview</a>
                    </div>
                </article>
            @endforeach
        </div>

        <article class="strategy-summary">
            <h2>Strategische lijn</h2>
            <p>De rode draad in deze set is: gratis platform voor individuen, betaald inzichtsplatform voor organisaties. Daarmee sluiten bereik, data, community en zakelijke waarde logisch op elkaar aan.</p>
        </article>
    </section>
</x-layouts.hermes-admin>
