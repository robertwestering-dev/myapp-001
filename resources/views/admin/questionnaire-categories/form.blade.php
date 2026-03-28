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
            min-height: 110px;
            resize: vertical;
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

        <form method="POST" action="{{ $isEditing ? route('admin.questionnaires.categories.update', [$questionnaire, $category]) : route('admin.questionnaires.categories.store', $questionnaire) }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <label>
                <span>Categorietitel</span>
                <input type="text" name="title" value="{{ old('title', $category->title) }}" required>
            </label>

            <label>
                <span>Beschrijving</span>
                <textarea name="description">{{ old('description', $category->description) }}</textarea>
            </label>

            <label>
                <span>Volgorde</span>
                <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" required>
            </label>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="ghost-pill">Terug naar questionnaire</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
