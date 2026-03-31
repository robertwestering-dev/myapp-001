<x-layouts.hermes-admin
    title="Academy-cursus verwijderen"
    eyebrow="Academy"
    heading="Wilt u deze Academy-cursus echt verwijderen?"
    :lead="'U staat op het punt '.e($academyCourse->titleForLocale('nl')).' definitief te verwijderen.'"
    menu-active="academy-courses"
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
        <p>Deze actie kan niet ongedaan worden gemaakt. De gekoppelde web-exportmap wordt niet automatisch verwijderd, alleen de databasevermelding in de Academy-catalogus.</p>

        <div class="confirm-actions">
            <form method="POST" action="{{ route('admin.academy-courses.destroy', $academyCourse) }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="pill">JA, verwijderen</button>
            </form>

            <a href="{{ route('admin.academy-courses.index') }}" class="ghost-pill">NEE, annuleren</a>
        </div>
    </section>
</x-layouts.hermes-admin>
