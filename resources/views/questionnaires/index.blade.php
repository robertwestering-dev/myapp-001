<x-layouts.hermes-dashboard :title="__('hermes.questionnaires.title')">
    <x-slot:head>
        <style>
            .questionnaire-page {
                display: grid;
                gap: 24px;
            }

            h1,
            h2,
            h3,
            p {
                margin: 0;
            }

            .hero p,
            .questionnaire-card__meta,
            .questionnaire-card__description {
                color: #5a6762;
            }

            .questionnaire-list {
                display: grid;
                gap: 18px;
            }

            .questionnaire-card__header,
            .questionnaire-card__actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
            }

            .questionnaire-card__meta {
                display: grid;
                gap: 6px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.95rem;
            }

            .questionnaire-card__history {
                display: grid;
                gap: 10px;
                padding-top: 4px;
                font-family: Arial, Helvetica, sans-serif;
            }

            .questionnaire-card__history-title {
                font-weight: 700;
                color: #16211d;
            }

            .questionnaire-card__history-list {
                display: grid;
                gap: 8px;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .questionnaire-card__history-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
                padding: 10px 0;
                border-top: 1px solid rgba(22, 33, 29, 0.12);
            }

            .questionnaire-modal {
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

            .questionnaire-modal::backdrop {
                background: rgba(22, 33, 29, 0.42);
            }

            .questionnaire-modal__body {
                display: grid;
                gap: 18px;
                padding: 28px;
                border-radius: 8px;
                background: #fffaf1;
                color: #16211d;
                box-shadow: 0 24px 70px rgba(24, 34, 30, 0.22);
            }

            .questionnaire-modal__body p {
                color: #5a6762;
                line-height: 1.65;
            }

        </style>
    </x-slot:head>

    <div class="questionnaire-page">
        <section class="questionnaire-list user-panel user-panel--padded">
            <x-user-section-heading :eyebrow="__('hermes.questionnaires.library_eyebrow')" />

            @if (session('pro_required_modal'))
                <dialog class="questionnaire-modal" open>
                    <form method="dialog" class="questionnaire-modal__body">
                        <x-user-section-heading
                            :title="__('hermes.questionnaires.pro_required_title')"
                            :text="__('hermes.questionnaires.pro_required_message')"
                        />
                        <x-user-action-row align="end">
                            <button type="submit" class="pill">{{ __('hermes.questionnaires.pro_required_close') }}</button>
                        </x-user-action-row>
                    </form>
                </dialog>
            @endif

            @if (session('status'))
                <x-user-feedback :messages="[session('status')]" />
            @endif

            @forelse ($availableQuestionnaires as $availableQuestionnaire)
                @php($currentResponse = $availableQuestionnaire->currentResponse)
                <x-user-surface-card variant="soft" class="questionnaire-card">
                    <div class="questionnaire-card__header">
                        <strong>{{ $availableQuestionnaire->questionnaire->localized_title ?? $availableQuestionnaire->questionnaire->title }}</strong>
                        @if ($currentResponse?->isDraft())
                            <x-admin-status-badge :label="__('hermes.dashboard.draft_badge')" tone="warning" uppercase />
                        @endif
                    </div>
                    <div class="questionnaire-card__description">{{ ($availableQuestionnaire->questionnaire->localized_description ?? $availableQuestionnaire->questionnaire->description) ?: __('hermes.dashboard.description_fallback') }}</div>
                    <x-user-action-row class="questionnaire-card__actions">
                        <a href="{{ route('questionnaire-responses.show', $availableQuestionnaire) }}" class="pill">
                            {{ $currentResponse?->isDraft() ? __('hermes.dashboard.resume_draft') : __('hermes.questionnaires.start_questionnaire') }}
                        </a>
                    </x-user-action-row>
                    <div class="questionnaire-card__meta">
                        @if ($currentResponse?->submitted_at)
                            <span>{{ __('hermes.dashboard.last_completed', ['datetime' => $currentResponse->submitted_at->format('d-m-Y H:i')]) }}</span>
                        @elseif ($currentResponse?->last_saved_at)
                            <span>{{ __('hermes.dashboard.draft_saved', ['datetime' => $currentResponse->last_saved_at->format('d-m-Y H:i')]) }}</span>
                            <span>{{ __('hermes.dashboard.resume_ready') }}</span>
                        @else
                            <span>{{ __('hermes.dashboard.not_completed') }}</span>
                        @endif
                    </div>
                    @if ($availableQuestionnaire->completedResponses->isNotEmpty())
                        <div class="questionnaire-card__history">
                            <div class="questionnaire-card__history-title">{{ __('hermes.questionnaires.completed_history_title') }}</div>
                            <ul class="questionnaire-card__history-list">
                                @foreach ($availableQuestionnaire->completedResponses as $completedResponse)
                                    <li class="questionnaire-card__history-item">
                                        <span>{{ __('hermes.questionnaires.completed_history_item', ['datetime' => $completedResponse->submitted_at->format('d-m-Y H:i')]) }}</span>
                                        <a href="{{ route('questionnaire-responses.results', $completedResponse) }}" class="ghost-pill">
                                            {{ __('hermes.questionnaires.view_results') }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </x-user-surface-card>
            @empty
                <x-user-guidance-card
                    :eyebrow="__('hermes.questionnaires.library_eyebrow')"
                    :title="__('hermes.dashboard.empty_title')"
                    :text="__('hermes.dashboard.empty_text')"
                    :action-label="__('hermes.questionnaires.empty_action')"
                    :action-href="route('dashboard')"
                />
            @endforelse
        </section>
    </div>
</x-layouts.hermes-dashboard>
