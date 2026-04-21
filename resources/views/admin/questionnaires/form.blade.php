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

        .checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkbox input {
            width: auto;
            margin: 0;
        }

        .form-actions,
        .header-actions,
        .category-header,
        .question-row__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-actions,
        .category-header {
            align-items: center;
            justify-content: space-between;
        }

        .category-list {
            display: grid;
            gap: 18px;
            margin-top: 24px;
        }

        .category-card {
            padding: 22px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }

        .question-list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .question-row {
            display: grid;
            gap: 8px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(32, 69, 58, 0.06);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
        }

        .muted {
            color: var(--muted);
        }

        .inline-form {
            margin: 0;
        }
    </style>

    <x-hermes-section-header
        tagline="Samenstellen"
        heading="Bouw standaardquestionnaires op uit categorieen en vraagtypen"
        description="Admins beheren hier de centrale bibliotheek. Beheerders kunnen deze questionnaires daarna alleen nog beschikbaar stellen voor hun eigen organisatie."
    />

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

        <form method="POST" action="{{ $isEditing ? route('admin.questionnaires.update', $questionnaire) : route('admin.questionnaires.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <label>
                <span>Titel</span>
                <input type="text" name="title" value="{{ old('title', $questionnaire->title) }}" required>
            </label>

            <label>
                <span>Beschrijving</span>
                <textarea name="description">{{ old('description', $questionnaire->description) }}</textarea>
            </label>

            <label>
                <span>Basistaal</span>
                <select name="locale" required>
                    @foreach (config('locales.supported', []) as $localeCode => $localeLabel)
                        <option value="{{ $localeCode }}" @selected(old('locale', $questionnaire->locale) === $localeCode)>{{ strtoupper($localeCode) }} · {{ $localeLabel }}</option>
                    @endforeach
                </select>
            </label>

            <label class="checkbox">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $questionnaire->is_active))>
                <span>Questionnaire is actief in de bibliotheek</span>
            </label>

            <label class="checkbox">
                <input type="checkbox" name="pro_only" value="1" @checked(old('pro_only', $questionnaire->pro_only))>
                <span>Alleen toegankelijk voor PRO-gebruikers (rol 'User' ziet de questionnaire, maar kan hem niet starten)</span>
            </label>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.questionnaires.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>

    @if ($isEditing)
        <section class="content-panel">
            <div class="header-actions">
                <div>
                    <h2>Categorieen en vragen</h2>
                    <p class="muted">Voeg eerst categorieen toe en hang daarna de juiste vragen onder elke categorie.</p>
                </div>

                <a href="{{ route('admin.questionnaires.categories.create', $questionnaire) }}" class="pill">Nieuwe categorie</a>
            </div>

            <div class="category-list">
                @forelse ($questionnaire->categories as $category)
                    <article class="category-card">
                        <div class="category-header">
                            <div>
                                <strong>{{ $category->title }}</strong>
                                <div class="muted">
                                    Volgorde {{ $category->sort_order }}
                                    @if ($category->description)
                                        · {{ $category->description }}
                                    @endif
                                </div>
                            </div>

                            <div class="header-actions">
                                <a href="{{ route('admin.questionnaires.questions.create', [$questionnaire, 'category' => $category->id]) }}" class="ghost-pill">
                                    Nieuwe vraag
                                </a>
                                <a href="{{ route('admin.questionnaires.categories.edit', [$questionnaire, $category]) }}" class="ghost-pill">
                                    Categorie wijzigen
                                </a>
                                <form method="POST" action="{{ route('admin.questionnaires.categories.destroy', [$questionnaire, $category]) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger-pill">Categorie verwijderen</button>
                                </form>
                            </div>
                        </div>

                        <div class="question-list">
                            @forelse ($category->questions as $question)
                                <div class="question-row">
                                    <div>
                                        <strong>{{ $question->prompt }}</strong>
                                    </div>
                                    <div class="muted">
                                        <span class="badge">{{ \App\Models\QuestionnaireQuestion::typeLabels()[$question->type] ?? $question->type }}</span>
                                        @if ($question->is_required)
                                            <span class="badge">Verplicht</span>
                                        @endif
                                        <span>Volgorde {{ $question->sort_order }}</span>
                                    </div>
                                    @if ($question->options)
                                        <div class="muted">Opties: {{ implode(', ', $question->options) }}</div>
                                    @endif
                                    @if ($question->help_text)
                                        <div class="muted">{{ $question->help_text }}</div>
                                    @endif
                                    <div class="question-row__actions">
                                        <a href="{{ route('admin.questionnaires.questions.edit', [$questionnaire, $question]) }}" class="ghost-pill">Vraag wijzigen</a>
                                        <form method="POST" action="{{ route('admin.questionnaires.questions.destroy', [$questionnaire, $question]) }}" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-pill">Vraag verwijderen</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="muted">Nog geen vragen in deze categorie.</div>
                            @endforelse
                        </div>
                    </article>
                @empty
                    <div class="muted">Deze questionnaire heeft nog geen categorieen.</div>
                @endforelse
            </div>
        </section>
    @endif
</x-layouts.hermes-admin>
