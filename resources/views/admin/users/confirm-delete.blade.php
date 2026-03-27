<x-layouts.hermes-admin
    title="Gebruiker verwijderen"
    eyebrow="Gebruikers"
    heading="Wilt u deze gebruiker echt verwijderen?"
    :lead="'U staat op het punt '.e($user->name).' ('.e($user->email).') definitief te verwijderen.'"
    menu-active="users"
>
    <style>
        .confirm-card {
            display: grid;
            gap: 18px;
        }

        .confirm-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        .confirm-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
    </style>

    <section class="content-panel confirm-card">
        <p>Deze actie kan niet ongedaan worden gemaakt. Controleer of u de juiste gebruiker geselecteerd heeft voordat u doorgaat.</p>

        <div class="confirm-actions">
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="pill">JA, verwijderen</button>
            </form>

            <a href="{{ route('admin.users.index') }}" class="ghost-pill">NEE, annuleren</a>
        </div>
    </section>
</x-layouts.hermes-admin>
