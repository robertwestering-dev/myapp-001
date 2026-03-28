<x-layouts.hermes-admin
    :title="$title"
    eyebrow="Gebruikers"
    :heading="$title"
    :lead="$intro"
    menu-active="users"
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

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
    </style>

    <x-hermes-section-header
        tagline="Bewerken"
        heading="Werk veilig en consistent met gebruikersgegevens"
        description="Dit formulier gebruikt nu dezelfde bladstructuur als de homepage en de admin-overzichtspagina’s, zodat alle beheerschermen herkenbaar aanvoelen."
    />

    <section class="content-panel">
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <label>
                <span>Naam</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </label>

            <label>
                <span>Emailadres</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>

            <label>
                <span>Rol</span>
                <select name="role" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Organisatie</span>
                <select name="org_id" required>
                    @foreach ($organizations as $organizationId => $organizationName)
                        <option value="{{ $organizationId }}" @selected((string) old('org_id', $user->org_id) === (string) $organizationId)>{{ $organizationName }}</option>
                    @endforeach
                </select>
            </label>

            @unless ($isEditing)
                <label>
                    <span>Wachtwoord</span>
                    <input type="password" name="password" required>
                </label>

                <label>
                    <span>Bevestig wachtwoord</span>
                    <input type="password" name="password_confirmation" required>
                </label>
            @endunless

            <div class="form-actions">
                <button type="submit" class="pill">{{ $submitLabel }}</button>
                <a href="{{ route('admin.users.index') }}" class="ghost-pill">Terug naar overzicht</a>
            </div>
        </form>
    </section>
</x-layouts.hermes-admin>
