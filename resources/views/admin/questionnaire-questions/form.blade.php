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

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
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

        <form method="POST" action="{{ $isEditing ? route('admin.questionnaires.questions.update', [$questionnaire, $question]) : route('admin.questionnaires.questions.store', $questionnaire) }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <label>
                <span>Categorie</span>
                <select name="questionnaire_category_id" required>
                    <option value="">Kies een categorie</option>
                    @foreach ($questionnaire->categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('questionnaire_category_id', $question->questionnaire_category_id) === (string) $category->id)>
                            {{ $category->sort_order }} · {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Taal</span>
                <select name="locale" required>
                    @foreach (config('locales.supported', []) as $localeCode => $localeLabel)
                        <option value="{{ $localeCode }}" @selected(old('locale', $question->locale) === $localeCode)>{{ strtoupper($localeCode) }} · {{ $localeLabel }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Vraag</span>
                <textarea name="prompt" required>{{ old('prompt', $question->prompt) }}</textarea>
            </label>

            <label>
                <span>Vraagtype</span>
                <select name="type" required>
                    @foreach ($questionTypes as $type => $label)
                        <option value="{{ $type }}" @selected(old('type', $question->type) === $type)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Extra toelichting</span>
                <textarea name="help_text">{{ old('help_text', $question->help_text) }}</textarea>
            </label>

            <label>
                <span>Antwoordopties</span>
                <textarea name="options" placeholder="Voor enkele keuze, meerdere keuzes of likert-schaal. Zet elke optie op een nieuwe regel.">{{ old('options', is_array($question->options) ? implode("\n", $question->options) : '') }}</textarea>
            </label>

            <label>
                <span>Alleen tonen wanneer</span>
                <select name="display_condition_question_id">
                    <option value="">Altijd tonen</option>
                    @foreach ($conditionQuestionOptions as $questionId => $questionLabel)
                        <option value="{{ $questionId }}" @selected((string) old('display_condition_question_id', $question->display_condition_question_id) === (string) $questionId)>
                            {{ $questionLabel }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Conditie</span>
                <select name="display_condition_operator">
                    <option value="">Kies een conditie</option>
                    @foreach ($conditionOperators as $operator => $operatorLabel)
                        <option value="{{ $operator }}" @selected(old('display_condition_operator', $question->display_condition_operator) === $operator)>
                            {{ $operatorLabel }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Verwachte waarde(n)</span>
                <textarea name="display_condition_answer" placeholder="Gebruik bij meerdere mogelijke waarden telkens een nieuwe regel. Laat leeg bij 'is ingevuld' of 'is niet ingevuld'.">{{ old('display_condition_answer', is_array($question->display_condition_answer) ? implode("\n", $question->display_condition_answer) : '') }}</textarea>
            </label>

            <label>
                <span>Volgorde</span>
                <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $question->sort_order ?? 0) }}" required>
            </label>

            <label class="checkbox">
                <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $question->is_required))>
                <span>Vraag is verplicht</span>
            </label>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="ghost-pill">Terug naar questionnaire</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
