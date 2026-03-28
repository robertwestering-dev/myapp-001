<x-layouts.hermes-admin
    :title="$title"
    eyebrow="Organisaties"
    :heading="$title"
    :lead="$intro"
    menu-active="organizations"
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

        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        @media (max-width: 720px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <x-hermes-section-header
        tagline="Bewerken"
        heading="Houd organisatiegegevens centraal en overzichtelijk"
        description="Gebruik dit formulier om kerngegevens en de vaste contactpersoon van een organisatie op een consistente manier te beheren."
    />

    <section class="content-panel">
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.organizations.update', $organization) : route('admin.organizations.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <label>
                <span>Naam</span>
                <input type="text" name="naam" value="{{ old('naam', $organization->naam) }}" required>
            </label>

            <label>
                <span>Adres</span>
                <input type="text" name="adres" value="{{ old('adres', $organization->adres) }}" required>
            </label>

            <div class="grid">
                <label>
                    <span>Postcode</span>
                    <input type="text" name="postcode" value="{{ old('postcode', $organization->postcode) }}" required>
                </label>

                <label>
                    <span>Plaats</span>
                    <input type="text" name="plaats" value="{{ old('plaats', $organization->plaats) }}" required>
                </label>
            </div>

            <div class="grid">
                <label>
                    <span>Land</span>
                    <input type="text" name="land" value="{{ old('land', $organization->land) }}" required>
                </label>

                <label>
                    <span>Telefoon</span>
                    <input type="text" name="telefoon" value="{{ old('telefoon', $organization->telefoon) }}" required>
                </label>
            </div>

            <label>
                <span>Contactpersoon</span>
                <select name="contact_id" required>
                    <option value="">Kies een contactpersoon</option>
                    @foreach ($contacts as $contactId => $contactName)
                        <option value="{{ $contactId }}" @selected((string) old('contact_id', $organization->contact_id) === (string) $contactId)>
                            {{ $contactName }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.organizations.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
