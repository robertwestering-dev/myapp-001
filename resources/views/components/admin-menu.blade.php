@props([
    'active' => 'portal',
    'showSecondaryItems' => true,
])

<nav class="admin-menu" aria-label="Admin menu">
    <a href="{{ route('admin.users.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'users'])>
        Gebruikers
    </a>

    <a href="{{ route('admin.organizations.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'organizations'])>
        Organisaties
    </a>

    <a href="{{ route('admin.questionnaires.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'questionnaires'])>
        Questionnaires
    </a>

    <a href="{{ route('admin.questionnaire-responses.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'questionnaire-responses'])>
        Responses
    </a>

    @if ($showSecondaryItems && $active !== 'portal')
        <a href="{{ route('admin.portal') }}" class="admin-menu__item">
            Admin-portal
        </a>

        <span class="admin-menu__item">Instellingen</span>
    @endif
</nav>
