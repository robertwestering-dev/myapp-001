<x-layouts.hermes-admin
    title="Blogpost verwijderen"
    eyebrow="Blog"
    heading="Blogpost verwijderen"
    :lead="'U staat op het punt '.e($blogPost->titleForLocale('nl')).' te verwijderen.'"
    menu-active="blog-posts"
>
    <x-admin-confirm-delete
        :action="route('admin.blog-posts.destroy', $blogPost)"
        :cancel-href="route('admin.blog-posts.index')"
        confirm-label="Ja, verwijder blogpost"
        confirm-class="danger-pill"
        cancel-label="Nee, annuleren"
    >
        <p>Deze actie verwijdert de blogpost permanent uit de database en haalt hem ook van de publieke blogpagina.</p>
    </x-admin-confirm-delete>
</x-layouts.hermes-admin>
