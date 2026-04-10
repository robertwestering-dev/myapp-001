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
        .actions,
        .meta,
        .pagination {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .meta {
            align-items: end;
            justify-content: space-between;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .admin-filter-field {
            display: grid;
            gap: 8px;
        }

        .admin-filter-field__label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .admin-filter-control {
            min-width: 180px;
            padding: 14px 15px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .admin-filter-control--search {
            width: min(100%, 420px);
        }

        .admin-filter-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-row {
            margin-bottom: 24px;
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

        .empty {
            display: grid;
            gap: 12px;
            padding: 26px;
            border-radius: 24px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
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

                <x-admin-filter-field label="Zoek in content">
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Zoek op tekst in de content"
                        autocomplete="off"
                        data-realtime-search
                        class="admin-filter-control admin-filter-control--search"
                    >
                </x-admin-filter-field>
            </form>
        </div>

        <x-admin-toolbar>
            <form method="GET" action="{{ route('admin.translations.index') }}" class="filter-form" id="translation-filter-form">
                <x-admin-filter-field label="Taal">
                    <select name="locale" class="admin-filter-control">
                        <option value="">Alle talen</option>
                        @foreach ($supportedLocales as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" @selected($filters['locale'] === $localeCode)>{{ $localeLabel }}</option>
                        @endforeach
                    </select>
                </x-admin-filter-field>

                <x-admin-filter-field label="Pagina">
                    <select name="page" class="admin-filter-control">
                        <option value="">Alle pagina's</option>
                        @foreach ($pages as $page)
                            <option value="{{ $page }}" @selected($filters['page'] === $page)>{{ $page }}</option>
                        @endforeach
                    </select>
                </x-admin-filter-field>

                <x-admin-filter-field label="Element">
                    <select name="element" class="admin-filter-control">
                        <option value="">Alle elementen</option>
                        @foreach ($elements as $element)
                            <option value="{{ $element }}" @selected($filters['element'] === $element)>{{ $element }}</option>
                        @endforeach
                    </select>
                </x-admin-filter-field>

                <input type="hidden" name="search" value="{{ $filters['search'] }}">

                <x-admin-filter-actions class="actions">
                    <button type="submit" class="pill">Filter</button>
                    <a href="{{ route('admin.translations.index') }}" class="ghost-pill">Reset</a>
                </x-admin-filter-actions>
            </form>
        </x-admin-toolbar>

        @if ($filters['locale'] !== '' || $filters['page'] !== '' || $filters['element'] !== '' || $filters['search'] !== '')
            <x-admin-empty-state title="Actieve filters">
                <x-slot:content>
                    Taal: {{ $filters['locale'] !== '' ? $supportedLocales[$filters['locale']] : 'alle' }} ·
                    Pagina: {{ $filters['page'] !== '' ? $filters['page'] : 'alle' }} ·
                    Element: {{ $filters['element'] !== '' ? $filters['element'] : 'alle' }} ·
                    Zoekterm: {{ $filters['search'] !== '' ? $filters['search'] : 'geen' }}
                </x-slot:content>
            </x-admin-empty-state>
        @endif

        @if ($translations->count() > 0)
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
                        @foreach ($translations as $translation)
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-admin-empty-state
                title="Er zijn geen vertaalregels gevonden voor de huidige filters."
                description="Pas de filters aan of reset het overzicht om weer alle vertaalregels te zien."
            >
                <x-slot:actions>
                    <a href="{{ route('admin.translations.index') }}" class="ghost-pill">Reset</a>
                </x-slot:actions>
            </x-admin-empty-state>
        @endif

        <x-admin-results-meta :paginator="$translations" aria-label="Paginatie vertalingen" link-mode="simple" />

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
