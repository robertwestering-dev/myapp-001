<x-layouts.hermes-admin
    title="Blogpost verwijderen"
    eyebrow="Blog"
    heading="Blogpost verwijderen"
    :lead="'U staat op het punt '.e($blogPost->titleForLocale('nl')).' te verwijderen.'"
    menu-active="blog-posts"
>
    <section class="content-panel">
        <p>Deze actie verwijdert de blogpost permanent uit de database en haalt hem ook van de publieke blogpagina.</p>

        <div class="form-actions">
            <form method="POST" action="{{ route('admin.blog-posts.destroy', $blogPost) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-pill">Ja, verwijder blogpost</button>
            </form>

            <a href="{{ route('admin.blog-posts.index') }}" class="ghost-pill">Nee, annuleren</a>
        </div>
    </section>
</x-layouts.hermes-admin>
