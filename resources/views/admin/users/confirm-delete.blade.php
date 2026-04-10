<x-layouts.hermes-admin
    title="Gebruiker verwijderen"
    eyebrow="Gebruikers"
    heading="Wilt u deze gebruiker echt verwijderen?"
    :lead="'U staat op het punt '.e($user->name).' ('.e($user->email).') definitief te verwijderen.'"
    menu-active="users"
>
    <x-admin-confirm-delete
        :action="route('admin.users.destroy', $user)"
        :cancel-href="route('admin.users.index')"
    >
        <p>Deze actie kan niet ongedaan worden gemaakt. Controleer of u de juiste gebruiker geselecteerd heeft voordat u doorgaat.</p>
    </x-admin-confirm-delete>
</x-layouts.hermes-admin>
