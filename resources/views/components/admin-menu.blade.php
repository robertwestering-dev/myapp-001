@props([
    'active' => 'portal',
    'showSecondaryItems' => true,
])

<nav class="admin-menu" aria-label="Admin menu">
    <a href="{{ route('admin.users.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'users'])>
        Gebruikers
    </a>

    @if ($showSecondaryItems && $active !== 'portal')
        <a href="{{ route('admin.portal') }}" class="admin-menu__item">
            Admin-portal
        </a>

        <span class="admin-menu__item">Instellingen</span>
    @endif
</nav>
