<x-layouts.hermes-admin
    title="Vertaalbeheer"
    eyebrow="Vertalingen"
    heading="Beheer alle Hermes-teksten"
    lead="Gebruik dit overzicht om teksten uit de `hermes.php` taalbestanden per taal, pagina en element te filteren en te wijzigen."
    menu-active="translations"
    :show-secondary-menu-items="false"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$translations->total()"
            description="Vertaalregels in de huidige selectie"
        />
        <x-hermes-fact
            :title="count($supportedLocales)"
            description="Beschikbare talen"
        />
        <x-hermes-fact
            :title="count($pages)"
            description="Pagina's in `hermes.php`"
        />
    </x-slot:heroFacts>

    <style>
        .filters,
        .actions,
        .meta,
        .pagination {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filters,
        .meta {
            align-items: end;
            justify-content: space-between;
        }

        .filters {
            margin: 28px 0 24px;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-form label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .filter-form select,
        .filter-form input {
            min-width: 180px;
            padding: 14px 15px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .search-row {
            margin-bottom: 24px;
        }

        .search-row label {
            display: grid;
            gap: 8px;
            max-width: 420px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        table {
            width: 100%;
            min-width: 1120px;
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

        .content-cell {
            max-width: 520px;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .muted {
            color: var(--muted);
        }

        .meta {
            margin-top: 22px;
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

        <div class="search-row">
            <form method="GET" action="{{ route('admin.translations.index') }}" id="translation-search-form">
                <input type="hidden" name="locale" value="{{ $filters['locale'] }}">
                <input type="hidden" name="page" value="{{ $filters['page'] }}">
                <input type="hidden" name="element" value="{{ $filters['element'] }}">

                <label>
                    <span>Zoek in content</span>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Zoek op tekst in de content"
                        autocomplete="off"
                        data-realtime-search
                    >
                </label>
            </form>
        </div>

        <div class="filters">
            <form method="GET" action="{{ route('admin.translations.index') }}" class="filter-form" id="translation-filter-form">
                <label>
                    <span>Taal</span>
                    <select name="locale">
                        <option value="">Alle talen</option>
                        @foreach ($supportedLocales as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" @selected($filters['locale'] === $localeCode)>{{ $localeLabel }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Pagina</span>
                    <select name="page">
                        <option value="">Alle pagina's</option>
                        @foreach ($pages as $page)
                            <option value="{{ $page }}" @selected($filters['page'] === $page)>{{ $page }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Element</span>
                    <select name="element">
                        <option value="">Alle elementen</option>
                        @foreach ($elements as $element)
                            <option value="{{ $element }}" @selected($filters['element'] === $element)>{{ $element }}</option>
                        @endforeach
                    </select>
                </label>

                <input type="hidden" name="search" value="{{ $filters['search'] }}">

                <div class="actions">
                    <button type="submit" class="pill">Filter</button>
                    <a href="{{ route('admin.translations.index') }}" class="ghost-pill">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Taal</th>
                        <th>Pagina</th>
                        <th>Element</th>
                        <th>Content</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($translations as $translation)
                        <tr>
                            <td>{{ $translation['locale_label'] }}</td>
                            <td>{{ $translation['page'] }}</td>
                            <td>
                                <strong>{{ $translation['element'] }}</strong>
                                <div class="muted">{{ $translation['key'] }}</div>
                            </td>
                            <td class="content-cell">{{ $translation['content'] }}</td>
                            <td>
                                <a
                                    href="{{ route('admin.translations.edit', [
                                        'locale' => $translation['locale'],
                                        'key' => $translation['key'],
                                        'filter_locale' => $filters['locale'],
                                        'filter_page' => $filters['page'],
                                        'filter_element' => $filters['element'],
                                        'filter_search' => $filters['search'],
                                        'page_number' => $translations->currentPage(),
                                    ]) }}"
                                    class="ghost-pill"
                                >
                                    Wijzigen
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="muted">Er zijn geen vertaalregels gevonden voor de huidige filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="meta">
            <span class="muted">Resultaten {{ $translations->firstItem() ?? 0 }} t/m {{ $translations->lastItem() ?? 0 }} van {{ $translations->total() }}</span>

            @if ($translations->hasPages())
                <div class="pagination">
                    @if ($translations->onFirstPage())
                        <span class="pagination__link">Vorige</span>
                    @else
                        <a href="{{ $translations->previousPageUrl() }}" class="pagination__link">Vorige</a>
                    @endif

                    <span class="pagination__current">{{ $translations->currentPage() }}</span>

                    @if ($translations->hasMorePages())
                        <a href="{{ $translations->nextPageUrl() }}" class="pagination__link">Volgende</a>
                    @else
                        <span class="pagination__link">Volgende</span>
                    @endif
                </div>
            @endif
        </div>

        <script>
            (() => {
                const searchInput = document.querySelector('[data-realtime-search]');

                if (! searchInput) {
                    return;
                }

                const searchForm = document.getElementById('translation-search-form');
                let timeoutId = null;

                searchInput.addEventListener('input', () => {
                    window.clearTimeout(timeoutId);

                    timeoutId = window.setTimeout(() => {
                        searchForm.requestSubmit();
                    }, 250);
                });
            })();
        </script>
    </section>
</x-layouts.hermes-admin>
