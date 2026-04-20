<x-layouts.hermes-public
    :title="__('hermes.contact_page.title')"
    :meta-description="__('hermes.contact_page.meta_description')"
    :canonical-url="route('contact.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="true"
    :show-header-contact-link="false"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => __('hermes.contact_page.title'),
        'description' => __('hermes.contact_page.meta_description'),
        'url' => route('contact.show'),
    ]"
>
    @guest
        <x-slot:headerMenu>
            <a class="home-menu-item" href="{{ route('home') }}">{{ __('hermes.nav.home') }}</a>
            <a class="home-menu-item" href="{{ route('blog.index') }}">{{ __('hermes.nav.blog') }}</a>
            <details class="home-menu-dropdown">
                <summary class="home-menu-trigger">
                    {{ __('hermes.nav.about') }}
                    <span aria-hidden="true">▾</span>
                </summary>
                <div class="home-submenu">
                    <a href="{{ route('inspiration-sources.show') }}">{{ __('hermes.nav.inspiration_sources') }}</a>
                    <a href="{{ route('about.show') }}">{{ __('hermes.nav.about_us') }}</a>
                    <a href="{{ route('pricing.show') }}">{{ __('hermes.nav.pricing') }}</a>
                    <a href="{{ route('privacy.show') }}">{{ __('hermes.footer.privacy') }}</a>
                </div>
            </details>
            <a class="home-menu-item" href="{{ route('organizations.landing') }}">{{ __('hermes.nav.organizations') }}</a>
            <a class="home-menu-item" href="{{ route('contact.show') }}" aria-current="page">{{ __('hermes.nav.contact') }}</a>
        </x-slot:headerMenu>
    @endguest

    <x-slot:head>
        <style>
            .contact-page,
            .contact-page .closing__panel,
            .contact-page .contact-form,
            .contact-page .contact-form form,
            .contact-page .contact-form label,
            .contact-page .checkbox-field {
                display: grid;
                gap: 20px;
            }

            .contact-page {
                gap: 24px;
            }

            .contact-page h1,
            .contact-page h2,
            .contact-page p {
                margin: 0;
            }

            .contact-page h1,
            .contact-page h2 {
                font-family: "Georgia", "Times New Roman", serif;
                letter-spacing: 0;
            }

            .contact-page .contact-hero,
            .contact-page .closing__panel {
                padding: 28px;
                border-radius: 30px;
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .contact-page .contact-hero {
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.26), transparent 30%),
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98));
                color: #f8f1e7;
            }

            .contact-page .contact-hero .user-page-heading__eyebrow,
            .contact-page .contact-hero .user-page-heading p {
                color: rgba(248, 241, 231, 0.84);
            }

            .contact-page .contact-hero .user-page-heading h1 {
                color: #f8f1e7;
                font-size: clamp(1.72rem, 3.45vw, 3.15rem);
                line-height: 0.98;
                max-width: none;
            }

            .contact-page .closing__panel {
                grid-template-columns: minmax(0, 1fr);
                align-items: start;
                background: rgba(255, 255, 255, 0.72);
            }

            .contact-page .contact-form label span,
            .contact-page .checkbox-field__text {
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.7;
            }

            .contact-page .nav-actions {
                display: flex;
                gap: 14px;
                flex-wrap: wrap;
                align-items: center;
            }

            .contact-page .contact-form {
                padding: 24px;
                border: 1px solid rgba(255, 255, 255, 0.58);
                border-radius: var(--radius-lg);
                background: var(--panel);
                box-shadow: var(--shadow);
            }

            .contact-page .form-status {
                padding: 14px 16px;
                border-radius: var(--radius-md);
                background: rgba(30, 71, 61, 0.1);
                color: var(--forest-deep);
                font-weight: 600;
            }

            .contact-page .contact-form input,
            .contact-page .contact-form textarea {
                width: 100%;
                border: 1px solid rgba(23, 35, 33, 0.14);
                border-radius: 16px;
                padding: 13px 15px;
                background: rgba(255, 255, 255, 0.82);
                color: var(--ink);
                font: inherit;
            }

            .contact-page .contact-form textarea {
                min-height: 180px;
                resize: vertical;
            }

            .contact-page .checkbox-field label {
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }

            .contact-page .checkbox-field__note {
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.94rem;
                line-height: 1.7;
            }

            .contact-page .checkbox-field__note a {
                color: var(--clay-deep);
                font-weight: 700;
            }

            .contact-page .checkbox-field input[type="checkbox"] {
                width: 18px;
                height: 18px;
                margin-top: 2px;
                padding: 0;
            }

            .contact-page .field-error {
                color: #9f2f1a;
                font-size: 0.9rem;
            }

            @media (max-width: 1040px) {
                .contact-page .closing__panel {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 720px) {
                .contact-page .contact-hero,
                .contact-page .closing__panel,
                .contact-page .contact-form {
                    padding: 20px;
                    border-radius: 24px;
                }

                .contact-page .nav-actions {
                    display: grid;
                    gap: 12px;
                }

                .contact-page .nav-actions .pill {
                    width: 100%;
                    justify-content: center;
                    text-align: center;
                }

                .contact-page .contact-form textarea {
                    min-height: 150px;
                }
            }
        </style>
    </x-slot:head>

    <div class="contact-page">
        <section class="contact-hero">
            <x-user-page-heading
                :eyebrow="__('hermes.contact_page.eyebrow')"
                :title="__('hermes.contact_page.heading')"
            >
                <x-slot:meta>
                    <p>{{ __('hermes.contact_page.intro') }}</p>
                </x-slot:meta>
            </x-user-page-heading>
        </section>

        <section class="closing" id="contact">
            <div class="closing__panel">
                <div class="contact-form">
                    @if (session('status'))
                        <div class="form-status">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <label for="contact-name">
                            <span>{{ __('hermes.home.contact_name') }}</span>
                            <input
                                id="contact-name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                autocomplete="name"
                                required
                            >
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <label for="contact-email">
                            <span>{{ __('hermes.home.contact_email') }}</span>
                            <input
                                id="contact-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                            >
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <label for="contact-message">
                            <span>{{ __('hermes.home.contact_message') }}</span>
                            <textarea id="contact-message" name="message" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <div class="checkbox-field">
                            <label for="contact-consent">
                                <input
                                    id="contact-consent"
                                    type="checkbox"
                                    name="privacy_consent"
                                    value="1"
                                    @checked(old('privacy_consent'))
                                    required
                                >
                                <span class="checkbox-field__text">{{ __('hermes.home.contact_consent') }}</span>
                            </label>
                            <p class="checkbox-field__note">
                                {!! __('hermes.home.contact_privacy_notice', ['url' => route('privacy.show')]) !!}
                            </p>
                            @error('privacy_consent')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="nav-actions">
                            <button type="submit" class="pill pill--strong">{{ __('hermes.home.contact_submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-layouts.hermes-public>
