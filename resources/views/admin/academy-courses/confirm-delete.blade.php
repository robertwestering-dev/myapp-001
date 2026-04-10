<x-layouts.hermes-admin
    title="Academy-cursus verwijderen"
    eyebrow="Academy"
    heading="Wilt u deze Academy-cursus echt verwijderen?"
    :lead="'U staat op het punt '.e($academyCourse->titleForLocale('nl')).' definitief te verwijderen.'"
    menu-active="academy-courses"
>
    <x-admin-confirm-delete
        :action="route('admin.academy-courses.destroy', $academyCourse)"
        :cancel-href="route('admin.academy-courses.index')"
    >
        <p>Deze actie kan niet ongedaan worden gemaakt. De gekoppelde web-exportmap wordt niet automatisch verwijderd, alleen de databasevermelding in de Academy-catalogus.</p>
    </x-admin-confirm-delete>
</x-layouts.hermes-admin>
