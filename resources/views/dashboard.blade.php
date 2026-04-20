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

            .summary-card__stats > .user-stat-tile {
                min-height: 126px;
                align-content: space-between;
            }

            .summary-card--academy .dashboard-section__eyebrow,
            .summary-card--academy .summary-card__header p,
            .summary-card--academy .summary-card__footnote {
                color: rgba(246, 239, 229, 0.82);
            }

            .login-summary-modal {
                position: fixed;
                inset: 50% auto auto 50%;
                transform: translate(-50%, -50%);
                width: min(560px, calc(100% - 32px));
                border: 0;
                border-radius: 8px;
                margin: 0;
                padding: 0;
                background: transparent;
            }

            .login-summary-modal::backdrop {
                background: rgba(22, 33, 29, 0.42);
            }

            .login-summary-modal__body {
                display: grid;
                gap: 18px;
                padding: 28px;
                border-radius: 8px;
                background: #fffaf1;
                color: #16211d;
                box-shadow: 0 24px 70px rgba(24, 34, 30, 0.22);
            }

            .login-summary-modal__details {
                display: grid;
                gap: 10px;
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                line-height: 1.6;
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

    @if ($loginSummary)
        @php
            $previousLoginAt = $loginSummary['previous_login_at']
                ? \Illuminate\Support\Carbon::parse($loginSummary['previous_login_at'])->format('d-m-Y H:i')
                : __('hermes.dashboard.login_summary_unknown');
            $latestQuestionnaireSubmittedAt = $loginSummary['latest_questionnaire_submitted_at']
                ? \Illuminate\Support\Carbon::parse($loginSummary['latest_questionnaire_submitted_at'])->format('d-m-Y H:i')
                : null;
            $latestQuestionnaireLabel = $loginSummary['latest_questionnaire_title'] && $latestQuestionnaireSubmittedAt
                ? __('hermes.dashboard.login_summary_selftest_value', [
                    'title' => $loginSummary['latest_questionnaire_title'],
                    'datetime' => $latestQuestionnaireSubmittedAt,
                ])
                : __('hermes.dashboard.login_summary_no_selftest');
            $latestQuestionnaireSummary = ($loginSummary['latest_questionnaire_is_stale'] ?? false) && $loginSummary['latest_questionnaire_title']
                ? __('hermes.dashboard.login_summary_selftest_stale', [
                    'title' => $loginSummary['latest_questionnaire_title'],
                ])
                : __('hermes.dashboard.login_summary_last_selftest', ['selftest' => $latestQuestionnaireLabel]);
        @endphp

        <dialog class="login-summary-modal" open>
            <form method="dialog" class="login-summary-modal__body">
                <x-user-section-heading
                    :title="__('hermes.dashboard.login_summary_title', [
                        'name' => $loginSummary['name'],
                    ])"
                />

                <p class="login-summary-modal__details">
                    {{ __('hermes.dashboard.login_summary_last_login', ['datetime' => $previousLoginAt]) }}
                </p>

                <p class="login-summary-modal__details">
                    {{ $latestQuestionnaireSummary }}
                </p>

                <x-user-action-row align="end">
                    <button type="submit" class="pill">{{ __('hermes.dashboard.login_summary_close') }}</button>
                </x-user-action-row>
            </form>
        </dialog>
    @endif

    <section class="dashboard-card user-panel">
        <x-user-page-heading
            :eyebrow="__('hermes.dashboard.overview_eyebrow')"
            :title="__('hermes.dashboard.personal_title', ['name' => auth()->user()->first_name ?: auth()->user()->name])"
            :text="__('hermes.dashboard.overview_text')"
        />

        <div class="dashboard-stack">
            <section class="dashboard-section" aria-labelledby="dashboard-overview-title">
                <div id="dashboard-overview-title" class="dashboard-summary">
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
                            <x-user-stat-tile :label="__('hermes.dashboard.questionnaires_in_progress_count')" :value="$draftQuestionnaireCount" />
                            <x-user-stat-tile tone="warning" :label="__('hermes.dashboard.questionnaires_completed_count')" :value="$completedQuestionnaireCount" />
                        </div>

                        <x-user-action-row class="summary-card__actions">
                            <a href="{{ route('questionnaires.index') }}" class="pill">{{ __('hermes.dashboard.questionnaires_action') }}</a>
                        </x-user-action-row>
                    </x-user-surface-card>

                    <x-user-surface-card variant="accent" class="summary-card summary-card--academy">
                        <div class="summary-card__header">
                            <x-user-section-heading
                                :eyebrow="__('hermes.dashboard.academy_eyebrow')"
                                :title="__('hermes.dashboard.academy_title')"
                                :text="__('hermes.dashboard.academy_text')"
                            />
                        </div>

                        <div class="summary-card__stats">
                            <x-user-stat-tile :label="__('hermes.dashboard.academy_available_count')" :value="$academyCourseCount" />
                            <x-user-stat-tile :label="__('hermes.dashboard.academy_in_progress_count')" :value="$inProgressAcademyCourseCount" />
                            <x-user-stat-tile :label="__('hermes.dashboard.academy_completed_count')" :value="$completedAcademyCourseCount" />
                        </div>

                        <p class="summary-card__footnote">{{ __('hermes.dashboard.academy_meta') }}</p>

                        <x-user-action-row class="summary-card__actions">
                            <a href="{{ route('academy.index') }}" class="pill">{{ __('hermes.dashboard.academy_action') }}</a>
                        </x-user-action-row>
                    </x-user-surface-card>
                </div>
            </section>

        </div>
    </section>
</x-layouts.hermes-dashboard>
