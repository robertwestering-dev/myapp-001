@props([
    'active' => 'portal',
    'showSecondaryItems' => true,
    'variant' => 'panel',
])

@php
    $itemClass = $variant === 'dropdown' ? 'admin-menu__dropdown-item' : 'admin-menu__item';
    $activeClass = $variant === 'dropdown' ? 'admin-menu__dropdown-item--active' : 'admin-menu__item--active';
@endphp

<nav @class([
    'admin-menu' => $variant === 'panel',
    'admin-menu__dropdown' => $variant === 'dropdown',
]) aria-label="{{ __('hermes.admin_menu.label') }}">
    <a href="{{ route('admin.users.index') }}" @class([$itemClass, $activeClass => $active === 'users'])>
        {{ __('hermes.admin_menu.users') }}
    </a>

    <a href="{{ route('admin.organizations.index') }}" @class([$itemClass, $activeClass => $active === 'organizations'])>
        {{ __('hermes.admin_menu.organizations') }}
    </a>

    <a href="{{ route('admin.questionnaires.index') }}" @class([$itemClass, $activeClass => $active === 'questionnaires'])>
        {{ __('hermes.admin_menu.questionnaires') }}
    </a>

    <a href="{{ route('admin.questionnaire-responses.index') }}" @class([$itemClass, $activeClass => $active === 'questionnaire-responses'])>
        {{ __('hermes.admin_menu.responses') }}
    </a>

    <a href="{{ route('forum.index') }}" @class([$itemClass, $activeClass => $active === 'forum'])>
        {{ __('hermes.admin_menu.forum') }}
    </a>

    @if (request()->user()?->isAdmin())
        <a href="{{ route('admin.strategy-pages.index') }}" @class([$itemClass, $activeClass => $active === 'strategy-pages'])>
            {{ __('hermes.admin_menu.strategy') }}
        </a>

        <a href="{{ route('admin.media-assets.index') }}" @class([$itemClass, $activeClass => $active === 'media-assets'])>
            {{ __('hermes.admin_menu.assets') }}
        </a>

        <a href="{{ route('admin.academy-courses.index') }}" @class([$itemClass, $activeClass => $active === 'academy-courses'])>
            {{ __('hermes.admin_menu.academy') }}
        </a>

        <a href="{{ route('admin.blog-posts.index') }}" @class([$itemClass, $activeClass => $active === 'blog-posts'])>
            {{ __('hermes.admin_menu.blog') }}
        </a>

        <a href="{{ route('admin.translations.index') }}" @class([$itemClass, $activeClass => $active === 'translations'])>
            {{ __('hermes.admin_menu.translations') }}
        </a>

        <a href="{{ route('admin.audit-logs.index') }}" @class([$itemClass, $activeClass => $active === 'audit-logs'])>
            {{ __('hermes.admin_menu.audit_logs') }}
        </a>
    @endif

    @if ($showSecondaryItems && $active !== 'portal')
        <div @class([
            'admin-menu__divider' => $variant === 'dropdown',
        ])></div>

        <a href="{{ route('admin.portal') }}" @class([$itemClass])>
            {{ __('hermes.admin_menu.portal') }}
        </a>

        <span @class([
            $itemClass,
            'admin-menu__dropdown-item--muted' => $variant === 'dropdown',
        ])>{{ __('hermes.admin_menu.settings') }}</span>
    @endif
</nav>
