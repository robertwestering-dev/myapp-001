<x-layouts.hermes-dashboard :title="__('hermes.academy.title')">
    <x-slot:head>
        <style>
            .academy-page {
                display: grid;
                gap: 24px;
            }

            .academy-content {
                display: grid;
                gap: 24px;
                grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.78fr);
                align-items: start;
            }

            h1,
            h2,
            h3,
            p {
                margin: 0;
            }

            .academy-hero p,
            .academy-card p,
            .academy-card li,
            .academy-card__meta dd {
                color: #5a6762;
            }

            .academy-grid {
                display: grid;
                gap: 20px;
            }

            .academy-grid__cards {
                display: grid;
                gap: 20px;
            }

            .academy-card--adaptability {
                box-shadow: inset 0 4px 0 #c46836;
            }

            .academy-card--resilience {
                box-shadow: inset 0 4px 0 #2a6a6d;
            }

            .academy-card__title-row {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: start;
            }

            .academy-card__lists {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
            }

            .academy-card__actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: end;
            }

            .academy-card__details {
                display: block;
                align-self: end;
            }

            .academy-card__details[open] .academy-card__toggle::after {
                content: "−";
            }

            .academy-card__details:not([open]) .academy-card__toggle::after {
                content: "+";
            }

            .academy-card__toggle {
                list-style: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                min-height: 44px;
                padding: 0 16px;
                border-radius: 999px;
                border: 1px solid rgba(22, 33, 29, 0.12);
                background: rgba(255, 255, 255, 0.92);
                color: #16211d;
                cursor: pointer;
                font: inherit;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 700;
            }

            .academy-card__actions .pill,
            .academy-card__actions .academy-card__toggle {
                min-height: 44px;
            }

            .academy-card__toggle::-webkit-details-marker {
                display: none;
            }

            .academy-card__details-body {
                display: grid;
                gap: 18px;
                padding-top: 6px;
            }

            .academy-sidebar,
            .academy-sidebar__list {
                display: grid;
                gap: 16px;
            }

            .academy-sidebar {
                position: sticky;
                top: 110px;
            }

            .academy-sidebar__intro {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                line-height: 1.6;
            }

            .academy-sidebar__list a {
                display: block;
                padding: 14px 16px;
                border-radius: 18px;
                border: 1px solid rgba(32, 69, 58, 0.12);
                background: rgba(255, 255, 255, 0.9);
                color: #20453a;
                text-decoration: none;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 700;
                transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            }

            .academy-sidebar__list a:hover,
            .academy-sidebar__list a:focus-visible {
                border-color: rgba(196, 104, 54, 0.38);
                box-shadow: 0 14px 28px rgba(32, 69, 58, 0.08);
                transform: translateY(-1px);
            }

            .academy-card ul {
                margin: 0;
                padding-left: 18px;
                display: grid;
                gap: 8px;
            }

            .academy-card[id] {
                scroll-margin-top: 28px;
            }

            @media (max-width: 920px) {
                .academy-content,
                .academy-grid__cards,
                .academy-card__lists {
                    grid-template-columns: 1fr;
                }

                .academy-sidebar {
                    position: static;
                    top: auto;
                }
            }

            @media (max-width: 720px) {
                .academy-card__title-row {
                    flex-direction: column;
                }
            }

            .academy-card--pro-only {
                opacity: 0.5;
                filter: grayscale(0.4);
            }
        </style>
    </x-slot:head>

    <div class="academy-page">
        <div class="academy-content">
            <section class="academy-grid user-panel user-panel--padded" aria-labelledby="academy-catalog-title">
                <x-user-section-heading
                    id="academy-catalog-title"
                    :eyebrow="__('hermes.academy.catalog_eyebrow')"
                    :title="__('hermes.academy.catalog_title')"
                />

                <div class="academy-grid__cards">
                    @forelse ($courses as $course)
                        @php($canLaunchCourse = $course->canBeLaunchedBy($user))
                        @php($isLockedForUser = $course->isAvailable() && ! $canLaunchCourse)
                        <x-user-surface-card
                            variant="soft"
                            class="academy-card academy-card--{{ $course->theme }} {{ $isLockedForUser ? 'academy-card--pro-only' : '' }}"
                            id="academy-course-{{ $course->slug }}"
                        >
                            <div class="academy-card__title-row">
                                <div>
                                    <x-user-section-heading
                                        :eyebrow="__('hermes.academy.course_label')"
                                        :title="$course->titleForLocale()"
                                    />
                                </div>

                                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                    @if ($course->pro_only)
                                        <x-admin-status-badge label="PRO" tone="warning" uppercase />
                                    @endif
                                    <x-admin-status-badge
                                        :label="$course->isAvailable() ? __('hermes.academy.status_available') : __('hermes.academy.status_pending')"
                                        :tone="$course->isAvailable() ? 'default' : 'warning'"
                                    />
                                </div>
                            </div>

                            <p>{{ $course->summaryForLocale() }}</p>

                            <x-user-action-row class="academy-card__actions">
                                <details class="academy-card__details">
                                    <summary class="academy-card__toggle">{{ __('hermes.academy.more_info') }}</summary>

                                    <div class="academy-card__details-body">
                                        <dl class="academy-card__meta">
                                            <x-user-meta-grid columns="2">
                                                <x-user-meta-item :label="__('hermes.academy.audience')" :value="$course->audienceForLocale()" />
                                                <x-user-meta-item :label="__('hermes.academy.duration')" :value="__('hermes.academy.minutes', ['count' => $course->estimated_minutes])" />
                                                <x-user-meta-item :label="__('hermes.academy.goal')" :value="$course->goalForLocale()" />
                                                <x-user-meta-item :label="__('hermes.academy.format')" :value="__('hermes.academy.web_export_format')" />
                                            </x-user-meta-grid>
                                        </dl>

                                        <div class="academy-card__lists">
                                            <div>
                                                <strong>{{ __('hermes.academy.learning_goals') }}</strong>
                                                <ul>
                                                    @foreach ($course->learningGoalsForLocale() as $learningGoal)
                                                        <li>{{ $learningGoal }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div>
                                                <strong>{{ __('hermes.academy.contents') }}</strong>
                                                <ul>
                                                    @foreach ($course->contentsForLocale() as $contentItem)
                                                        <li>{{ $contentItem }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        @unless ($course->launchUrl())
                                            <span>{{ __('hermes.academy.pending_copy') }}</span>
                                        @endunless
                                    </div>
                                </details>

                                @if ($course->launchUrl() && $canLaunchCourse)
                                    <a href="{{ $course->launchUrl() }}" class="pill" target="_blank" rel="noopener noreferrer">
                                        {{ __('hermes.academy.open_course') }}
                                    </a>
                                @endif
                            </x-user-action-row>
                        </x-user-surface-card>
                    @empty
                        <x-user-guidance-card
                            :eyebrow="__('hermes.academy.catalog_eyebrow')"
                            :title="__('hermes.academy.empty_title')"
                            :text="__('hermes.academy.empty_text')"
                            :action-label="__('hermes.academy.empty_action')"
                            :action-href="route('dashboard')"
                        />
                    @endforelse
                </div>
            </section>

            <aside class="academy-sidebar">
                <section class="user-filter-panel" aria-labelledby="academy-sidebar-title">
                    <x-user-section-heading
                        id="academy-sidebar-title"
                        :eyebrow="__('hermes.academy.eyebrow')"
                        :title="__('hermes.academy.sidebar_title')"
                        :text="__('hermes.academy.sidebar_text')"
                    />

                    @if ($courses->isNotEmpty())
                        <div class="academy-sidebar__list">
                            @foreach ($courses as $course)
                                <a href="#academy-course-{{ $course->slug }}">{{ $course->titleForLocale() }}</a>
                            @endforeach
                        </div>
                    @else
                        <p class="academy-sidebar__intro">{{ __('hermes.academy.sidebar_empty') }}</p>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-layouts.hermes-dashboard>
