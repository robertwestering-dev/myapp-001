<x-layouts.hermes-dashboard :title="__('hermes.dashboard.title')">
    <x-slot:head>
        <style>
            .dashboard-card {
                padding: 40px;
            }

            .dashboard-stack {
                display: grid;
                gap: 24px;
                margin-top: 28px;
            }

            .dashboard-section {
                display: grid;
                gap: 18px;
            }

            .dashboard-guidance {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 24px;
            }

            .dashboard-summary {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 24px;
            }

            .summary-card__header,
            .summary-card__stats {
                display: grid;
                gap: 10px;
            }

            .summary-card__header p,
            .summary-card__footnote {
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                line-height: 1.6;
            }

            .summary-card__stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
            }

            .summary-card--academy .dashboard-section__eyebrow,
            .summary-card--academy .summary-card__header p,
            .summary-card--academy .summary-card__footnote {
                color: rgba(246, 239, 229, 0.82);
            }

            @media (max-width: 720px) {
                .dashboard-card {
                    padding: 28px;
                }

                .dashboard-summary,
                .dashboard-guidance,
                .summary-card__stats {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <section class="dashboard-card user-panel">
        <x-user-page-heading
            :eyebrow="__('hermes.dashboard.overview_eyebrow')"
            :title="__('hermes.dashboard.personal_title', ['name' => auth()->user()->first_name ?: auth()->user()->name])"
            :text="__('hermes.dashboard.overview_text')"
        />

        <div class="dashboard-stack">
            <section class="dashboard-section" aria-labelledby="dashboard-overview-title">
                <div id="dashboard-overview-title" class="dashboard-summary">
                    <x-user-surface-card variant="accent" class="summary-card summary-card--academy">
                        <div class="summary-card__header">
                            <x-user-section-heading
                                :eyebrow="__('hermes.dashboard.academy_eyebrow')"
                                :title="__('hermes.dashboard.academy_title')"
                                :text="__('hermes.dashboard.academy_text')"
                            />
                        </div>

                        <div class="summary-card__stats">
                            <x-user-stat-tile :label="__('hermes.dashboard.academy_catalog_count')" :value="$academyCourseCount" />
                            <x-user-stat-tile :label="__('hermes.dashboard.academy_completed_count')" :value="$completedAcademyCourseCount" />
                        </div>

                        <p class="summary-card__footnote">{{ __('hermes.dashboard.academy_meta') }}</p>

                        <x-user-action-row class="summary-card__actions">
                            <a href="{{ route('academy.index') }}" class="pill">{{ __('hermes.dashboard.academy_action') }}</a>
                        </x-user-action-row>
                    </x-user-surface-card>

                    <x-user-surface-card class="summary-card summary-card--questionnaires">
                        <div class="summary-card__header">
                            <x-user-section-heading
                                :eyebrow="__('hermes.dashboard.questionnaires_eyebrow')"
                                :title="__('hermes.dashboard.questionnaires_title')"
                                :text="__('hermes.dashboard.questionnaires_text')"
                            />
                        </div>

                        <div class="summary-card__stats">
                            <x-user-stat-tile :label="__('hermes.dashboard.questionnaires_available_count')" :value="$availableQuestionnaireCount" />
                            <x-user-stat-tile tone="warning" :label="__('hermes.dashboard.questionnaires_completed_count')" :value="$completedQuestionnaireCount" />
                            <x-user-stat-tile :label="__('hermes.dashboard.questionnaires_draft_count')" :value="$draftQuestionnaireCount" />
                        </div>

                        <p class="summary-card__footnote">
                            {{ $draftQuestionnaireCount > 0 ? __('hermes.dashboard.drafts_ready', ['count' => $draftQuestionnaireCount]) : __('hermes.dashboard.questionnaires_ready') }}
                        </p>

                        <x-user-inline-meta
                            class="summary-card__footnote"
                            :items="[
                                __('hermes.questionnaire.active_language').': '.(__('hermes.questionnaire.active_language_value', ['locale' => strtoupper($activeQuestionnaireLocale), 'label' => $activeQuestionnaireLocaleLabel])),
                                $activeQuestionnaireLocaleSource === 'profile'
                                    ? __('hermes.questionnaire.active_language_profile')
                                    : __('hermes.questionnaire.active_language_session'),
                            ]"
                        />

                        <x-user-action-row class="summary-card__actions">
                            <a href="{{ route('questionnaires.index') }}" class="pill">{{ __('hermes.dashboard.questionnaires_action') }}</a>
                        </x-user-action-row>
                    </x-user-surface-card>
                </div>
            </section>

            <section class="dashboard-section" aria-labelledby="dashboard-next-step-title">
                <x-user-section-heading :eyebrow="__('hermes.dashboard.next_step_eyebrow')" />

                <div id="dashboard-next-step-title" class="dashboard-guidance">
                    <x-user-guidance-card
                        variant="accent"
                        :title="__('hermes.dashboard.next_step_questionnaires_title')"
                        :text="$draftQuestionnaireCount > 0
                            ? __('hermes.dashboard.drafts_ready', ['count' => $draftQuestionnaireCount])
                            : ($availableQuestionnaireCount > 0
                                ? __('hermes.dashboard.questionnaires_ready')
                                : __('hermes.dashboard.next_step_questionnaires_text'))"
                        :action-label="__('hermes.dashboard.questionnaires_action')"
                        :action-href="route('questionnaires.index')"
                    />

                    <x-user-guidance-card
                        :title="$academyCourseCount > 0
                            ? __('hermes.dashboard.next_step_academy_title')
                            : __('hermes.academy.empty_title')"
                        :text="$academyCourseCount > 0
                            ? __('hermes.dashboard.next_step_academy_text')
                            : __('hermes.academy.empty_text')"
                        :action-label="__('hermes.dashboard.academy_action')"
                        :action-href="route('academy.index')"
                    />
                </div>
            </section>
        </div>
    </section>
</x-layouts.hermes-dashboard>
