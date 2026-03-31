<x-layouts.hermes-admin
    title="Vertaling wijzigen"
    eyebrow="Vertalingen"
    heading="Wijzig vertaaltekst"
    lead="Werk de geselecteerde tekst bij en keer daarna terug naar het gefilterde overzicht."
    menu-active="translations"
>
    <style>
        form {
            display: grid;
            gap: 18px;
        }

        .meta-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .meta-card {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        .meta-card span {
            display: block;
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        textarea {
            width: 100%;
            min-height: 220px;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
            resize: vertical;
        }

        .helper {
            color: var(--muted);
            font-size: 0.92rem;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 860px) {
            .meta-grid {
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

        <div class="meta-grid">
            <div class="meta-card">
                <span>Taal</span>
                <strong>{{ $translation['locale_label'] }}</strong>
            </div>

            <div class="meta-card">
                <span>Pagina</span>
                <strong>{{ $translation['page'] }}</strong>
            </div>

            <div class="meta-card">
                <span>Element</span>
                <strong>{{ $translation['element'] }}</strong>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.translations.update') }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="locale" value="{{ $translation['locale'] }}">
            <input type="hidden" name="key" value="{{ $translation['key'] }}">
            <input type="hidden" name="filter_locale" value="{{ $returnFilters['locale'] }}">
            <input type="hidden" name="filter_page" value="{{ $returnFilters['page'] }}">
            <input type="hidden" name="filter_element" value="{{ $returnFilters['element'] }}">
            <input type="hidden" name="filter_search" value="{{ $returnFilters['search'] }}">
            <input type="hidden" name="page_number" value="{{ $returnFilters['page_number'] }}">

            <label>
                <span>Content</span>
                <textarea name="content" required>{{ old('content', $translation['content']) }}</textarea>
                <span class="helper">{{ $translation['key'] }}</span>
            </label>

            <div class="form-actions">
                <button type="submit" class="pill">Wijzigingen opslaan</button>
                <a href="{{ route('admin.translations.index', [
                    'locale' => $returnFilters['locale'] ?: null,
                    'page' => $returnFilters['page'] ?: null,
                    'element' => $returnFilters['element'] ?: null,
                    'search' => $returnFilters['search'] ?: null,
                    'page_number' => $returnFilters['page_number'] ?: null,
                ]) }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
