<x-layouts.hermes-admin
    :title="__('hermes.admin_portal.title')"
    :eyebrow="__('hermes.admin_portal.eyebrow')"
    :heading="__('hermes.admin_portal.heading')"
    :lead="$lead"
    menu-active="portal"
>
    <x-slot:heroFacts>
        <x-hermes-fact
            :title="$actor->role"
            :description="__('hermes.admin_portal.role')"
        />
        <x-hermes-fact
            :title="$questionnaireCount"
            :description="__('hermes.admin_portal.library_count')"
        />
        <x-hermes-fact
            :title="$scopedAvailabilityCount"
            :description="__('hermes.admin_portal.availability_count')"
        />
    </x-slot:heroFacts>

    <style>
        .portal-grid,
        .spotlight-grid,
        .spotlight-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .portal-grid {
            margin-top: 28px;
        }

        .portal-card,
        .spotlight-card {
            flex: 1 1 280px;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.56);
        }

        .spotlight-section {
            margin-top: 28px;
        }

        .spotlight-grid {
            margin-top: 16px;
        }

        .spotlight-card {
            display: grid;
            gap: 16px;
        }

        .spotlight-meta {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.94rem;
        }
    </style>

    <section class="content-panel">
        <div class="portal-grid">
            <article class="portal-card">
                <h2>{{ __('hermes.admin_portal.library_title') }}</h2>
                <p>{{ __('hermes.admin_portal.library_text') }}</p>
                <a href="{{ route('admin.questionnaires.index') }}" class="pill">{{ __('hermes.admin_portal.library_action') }}</a>
            </article>

            @if ($canManageLibrary)
                <article class="portal-card">
                    <h2>{{ __('hermes.admin_portal.academy_title') }}</h2>
                    <p>{{ __('hermes.admin_portal.academy_text') }}</p>
                    <div class="spotlight-meta">{{ __('hermes.admin_portal.academy_count', ['count' => $academyCourseCount]) }}</div>
                    <a href="{{ route('admin.academy-courses.index') }}" class="pill">{{ __('hermes.admin_portal.academy_action') }}</a>
                </article>

                <article class="portal-card">
                    <h2>{{ __('hermes.admin_portal.translations_title') }}</h2>
                    <p>{{ __('hermes.admin_portal.translations_text') }}</p>
                    <div class="spotlight-meta">{{ __('hermes.admin_portal.translations_count', ['count' => $translationCount]) }}</div>
                    <a href="{{ route('admin.translations.index') }}" class="pill">{{ __('hermes.admin_portal.translations_action') }}</a>
                </article>
            @endif

            <article class="portal-card">
                <h2>{{ __('hermes.admin_portal.reports_title') }}</h2>
                <p>{{ __('hermes.admin_portal.reports_text') }}</p>
                <a href="{{ route('admin.questionnaire-responses.index') }}" class="pill">{{ __('hermes.admin_portal.reports_action') }}</a>
            </article>
        </div>

        <section class="spotlight-section">
            <span class="eyebrow">{{ __('hermes.admin_portal.spotlight_eyebrow') }}</span>
            <h2>{{ __('hermes.admin_portal.spotlight_title') }}</h2>
            <p>{{ __('hermes.admin_portal.spotlight_text') }}</p>

            <div class="spotlight-grid">
                @foreach ($spotlightQuestionnaires as $questionnaire)
                    <article class="spotlight-card">
                        <div>
                            <h3>{{ $questionnaire->title }}</h3>
                            <p>{{ $questionnaire->description }}</p>
                        </div>

                        <div class="spotlight-meta">
                            {{ __('hermes.admin_portal.scope_meta', [
                                'categories' => $questionnaire->categories_count,
                                'questions' => $questionnaire->questions_count,
                                'links' => $questionnaire->scoped_organization_questionnaires_count,
                            ]) }}
                        </div>

                        <div class="spotlight-actions">
                            <a href="{{ route('admin.questionnaire-responses.index', ['questionnaire_id' => $questionnaire->id]) }}" class="ghost-pill">
                                {{ __('hermes.admin_portal.view_responses') }}
                            </a>
                            <a href="{{ route('admin.questionnaire-responses.stats', ['questionnaire_id' => $questionnaire->id]) }}" class="ghost-pill">
                                {{ __('hermes.admin_portal.view_stats') }}
                            </a>
                            @if ($canManageLibrary)
                                <a href="{{ route('admin.questionnaires.edit', $questionnaire) }}" class="pill">{{ __('hermes.admin_portal.open_library') }}</a>
                            @else
                                <a href="{{ route('admin.questionnaires.index') }}" class="pill">{{ __('hermes.admin_portal.open_library') }}</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </section>
</x-layouts.hermes-admin>
