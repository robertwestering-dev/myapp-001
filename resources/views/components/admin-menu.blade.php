@props([
    'active' => 'portal',
    'showSecondaryItems' => true,
])

<nav class="admin-menu" aria-label="{{ __('hermes.admin_menu.label') }}">
    <a href="{{ route('admin.users.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'users'])>
        {{ __('hermes.admin_menu.users') }}
    </a>

    <a href="{{ route('admin.organizations.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'organizations'])>
        {{ __('hermes.admin_menu.organizations') }}
    </a>

    <a href="{{ route('admin.questionnaires.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'questionnaires'])>
        {{ __('hermes.admin_menu.questionnaires') }}
    </a>

    <a href="{{ route('admin.questionnaire-responses.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'questionnaire-responses'])>
        {{ __('hermes.admin_menu.responses') }}
    </a>

    @if (request()->user()?->isAdmin())
        <a href="{{ route('admin.academy-courses.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'academy-courses'])>
            {{ __('hermes.admin_menu.academy') }}
        </a>

        <a href="{{ route('admin.translations.index') }}" @class(['admin-menu__item', 'admin-menu__item--active' => $active === 'translations'])>
            {{ __('hermes.admin_menu.translations') }}
        </a>
    @endif

    @if ($showSecondaryItems && $active !== 'portal')
        <a href="{{ route('admin.portal') }}" class="admin-menu__item">
            {{ __('hermes.admin_menu.portal') }}
        </a>

        <span class="admin-menu__item">{{ __('hermes.admin_menu.settings') }}</span>
    @endif
</nav>
