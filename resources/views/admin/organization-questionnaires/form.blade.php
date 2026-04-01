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
        select {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
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

        <p><strong>Questionnaire:</strong> {{ $questionnaire->title }}</p>

        @if (! $questionnaire->is_active)
            <div class="errors">
                <div>Deze questionnaire staat momenteel inactief in de bibliotheek. Gebruikers zien deze pas op hun dashboard zodra de questionnaire zelf ook actief is.</div>
                @if (request()->user()?->isAdmin())
                    <div><a href="{{ route('admin.questionnaires.edit', $questionnaire) }}">Open de questionnaire en activeer deze eerst.</a></div>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.questionnaires.availability.update', [$questionnaire, $availability]) : route('admin.questionnaires.availability.store', $questionnaire) }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <label>
                <span>Organisatie</span>
                <select name="org_id" required>
                    @foreach ($organizations as $organizationId => $organizationName)
                        <option value="{{ $organizationId }}" @selected((string) old('org_id', $availability->org_id) === (string) $organizationId)>
                            {{ $organizationName }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Beschikbaar vanaf</span>
                <input type="date" name="available_from" value="{{ old('available_from', $availability->available_from?->toDateString()) }}">
            </label>

            <label>
                <span>Beschikbaar tot</span>
                <input type="date" name="available_until" value="{{ old('available_until', $availability->available_until?->toDateString()) }}">
            </label>

            <label class="checkbox">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $availability->is_active))>
                <span>Questionnaire is actief beschikbaar voor deze organisatie</span>
            </label>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.questionnaires.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>

        @if ($isEditing)
            <form method="POST" action="{{ route('admin.questionnaires.availability.destroy', [$questionnaire, $availability]) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-pill">Beschikbaarheid voor deze organisatie verwijderen</button>
            </form>
        @endif
    </section>
</x-layouts.hermes-admin>
