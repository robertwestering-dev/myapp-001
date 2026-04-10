<x-layouts.hermes-admin
    :title="$page['title']"
    :eyebrow="$page['eyebrow']"
    :heading="$page['heading']"
    :lead="$page['lead']"
    menu-active="strategy-pages"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="count($page['sections'])"
            description="Inhoudsblokken"
        />
        <x-hermes-fact
            title="Admin-only"
            description="Niet zichtbaar op de publieke homepage"
        />
        <x-hermes-fact
            title="Nederlands"
            description="Conceptcopy voor verdere uitwerking"
        />
    </x-slot:heroFacts>

    <style>
        .strategy-layout,
        .strategy-nav {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .strategy-layout {
            margin-top: 28px;
            align-items: flex-start;
        }

        .strategy-main {
            flex: 1 1 760px;
            display: grid;
            gap: 18px;
        }

        .strategy-side {
            flex: 0 1 280px;
            display: grid;
            gap: 16px;
        }

        .strategy-block,
        .strategy-nav-card {
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
        }

        .strategy-block ul {
            margin: 14px 0 0;
            padding-left: 20px;
            display: grid;
            gap: 10px;
        }

        .strategy-nav {
            margin-top: 16px;
        }
    </style>

    <section class="content-panel">
        <div class="strategy-layout">
            <div class="strategy-main">
                @foreach ($page['sections'] as $section)
                    <article class="strategy-block">
                        <h2>{{ $section['title'] }}</h2>
                        <ul>
                            @foreach ($section['body'] as $paragraph)
                                <li>{{ $paragraph }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>

            <aside class="strategy-side">
                <article class="strategy-nav-card">
                    <h2>Meer strategiepagina's</h2>
                    <div class="strategy-nav">
                        <a href="{{ route('admin.strategy-pages.index') }}" class="ghost-pill">Overzicht</a>
                        <a href="{{ route('admin.strategy-pages.preview', $page['slug']) }}" class="pill">Open live preview</a>

                        @foreach ($pages as $linkedPage)
                            <a
                                href="{{ route('admin.strategy-pages.show', $linkedPage['slug']) }}"
                                class="{{ $linkedPage['slug'] === $page['slug'] ? 'pill' : 'ghost-pill' }}"
                            >
                                {{ $linkedPage['title'] }}
                            </a>
                        @endforeach
                    </div>
                </article>
            </aside>
        </div>
    </section>
</x-layouts.hermes-admin>
