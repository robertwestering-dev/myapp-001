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

        </style>
    </x-slot:head>

    <div class="questionnaire-page">
        <section class="questionnaire-list user-panel user-panel--padded">
            <x-user-section-heading :eyebrow="__('hermes.questionnaires.library_eyebrow')" />

            @if (session('status'))
                <x-user-feedback :messages="[session('status')]" />
            @endif

            @forelse ($availableQuestionnaires as $availableQuestionnaire)
                @php($currentResponse = $availableQuestionnaire->currentResponse)
                <x-user-surface-card variant="soft" class="questionnaire-card">
                    <div class="questionnaire-card__header">
                        <strong>{{ $availableQuestionnaire->questionnaire->title }}</strong>
                        @if ($currentResponse?->isDraft())
                            <x-admin-status-badge :label="__('hermes.dashboard.draft_badge')" tone="warning" uppercase />
                        @endif
                    </div>
                    <div class="questionnaire-card__description">{{ $availableQuestionnaire->questionnaire->description ?: __('hermes.dashboard.description_fallback') }}</div>
                    <x-user-action-row class="questionnaire-card__actions">
                        <a href="{{ route('questionnaire-responses.show', $availableQuestionnaire) }}" class="pill">
                            {{ $currentResponse?->isDraft() ? __('hermes.dashboard.resume_draft') : __('hermes.dashboard.open_questionnaire') }}
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
