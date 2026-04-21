<x-layouts.hermes-admin
    :title="$title"
    eyebrow="Academy"
    :heading="$title"
    :lead="$intro"
    menu-active="academy-courses"
>
    <style>
        form,
        .locale-grid {
            display: grid;
            gap: 18px;
        }

        .locale-grid {
            margin-top: 28px;
        }

        .locale-panel {
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
            display: grid;
            gap: 16px;
        }

        .field-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .checkbox-row input {
            width: auto;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .helper {
            color: var(--muted);
            font-size: 0.92rem;
        }

        @media (max-width: 860px) {
            .field-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <x-hermes-section-header
        tagline="Academy beheer"
        heading="Beheer metadata, vertalingen en publicatie vanuit een centrale database"
        description="Elke cursus bewaart de catalogusinformatie nu in MySQL. Gebruik per taal een eigen blok en zet per regel een leerdoel of inhoudspunt in de daarvoor bedoelde tekstvakken."
    />

    <section class="content-panel">
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.academy-courses.update', $academyCourse) : route('admin.academy-courses.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div class="field-grid">
                <label>
                    <span>Slug</span>
                    <input type="text" name="slug" value="{{ old('slug', $academyCourse->slug) }}" required>
                </label>

                <label>
                    <span>Thema</span>
                    <select name="theme" required>
                        @foreach ($themes as $themeValue => $themeLabel)
                            <option value="{{ $themeValue }}" @selected(old('theme', $academyCourse->theme) === $themeValue)>{{ $themeLabel }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Pad naar web-export</span>
                    <input type="text" name="path" value="{{ old('path', $academyCourse->path) }}" required>
                    <span class="helper">Bijvoorbeeld `academy-courses/adaptability-foundations`.</span>
                </label>

                <label>
                    <span>Gemiddelde duur in minuten</span>
                    <input type="number" name="estimated_minutes" min="1" value="{{ old('estimated_minutes', $academyCourse->estimated_minutes) }}" required>
                </label>

                <label>
                    <span>Sortering</span>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $academyCourse->sort_order) }}">
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $academyCourse->is_active))>
                    <span>Actief tonen in de Academy</span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="pro_only" value="1" @checked(old('pro_only', $academyCourse->pro_only))>
                    <span>Alleen toegankelijk voor PRO-gebruikers</span>
                </label>
            </div>

            <div class="locale-grid">
                @foreach ($supportedLocales as $localeCode => $localeLabel)
                    <section class="locale-panel">
                        <strong>Taal {{ $localeLabel }}</strong>

                        <label>
                            <span>Titel</span>
                            <input type="text" name="title[{{ $localeCode }}]" value="{{ old("title.$localeCode", $academyCourse->translation('title', $localeCode)) }}" required>
                        </label>

                        <label>
                            <span>Doelgroep</span>
                            <textarea name="audience[{{ $localeCode }}]" required>{{ old("audience.$localeCode", $academyCourse->translation('audience', $localeCode)) }}</textarea>
                        </label>

                        <label>
                            <span>Doel van de training</span>
                            <textarea name="goal[{{ $localeCode }}]" required>{{ old("goal.$localeCode", $academyCourse->translation('goal', $localeCode)) }}</textarea>
                        </label>

                        <label>
                            <span>Korte samenvatting</span>
                            <textarea name="summary[{{ $localeCode }}]" required>{{ old("summary.$localeCode", $academyCourse->translation('summary', $localeCode)) }}</textarea>
                        </label>

                        <label>
                            <span>Leerdoelen</span>
                            <textarea name="learning_goals[{{ $localeCode }}]" required>{{ old("learning_goals.$localeCode", implode(PHP_EOL, $academyCourse->translation('learning_goals', $localeCode) ?? [])) }}</textarea>
                            <span class="helper">Een leerdoel per regel.</span>
                        </label>

                        <label>
                            <span>Inhoud</span>
                            <textarea name="contents[{{ $localeCode }}]" required>{{ old("contents.$localeCode", implode(PHP_EOL, $academyCourse->translation('contents', $localeCode) ?? [])) }}</textarea>
                            <span class="helper">Een inhoudspunt per regel.</span>
                        </label>
                    </section>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.academy-courses.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
