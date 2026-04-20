<x-layouts.hermes-dashboard :title="$organizationQuestionnaire->questionnaire->title">
    <x-slot:head>
        <style>
            .questionnaire-results-page {
                display: grid;
                gap: 24px;
            }

            .panel {
                display: grid;
                gap: 22px;
                padding: 32px;
                border-radius: 24px;
                border: 1px solid rgba(22, 33, 29, 0.1);
                background: rgba(255, 255, 255, 0.82);
                box-shadow: 0 24px 60px rgba(24, 34, 30, 0.12);
            }

            .panel--results {
                display: grid;
                gap: 24px;
            }

            .results-summary-grid,
            .results-dimension-grid {
                display: grid;
                gap: 18px;
            }

            .results-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .results-dimension-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .results-dimensions,
            .results-dimension-card {
                display: grid;
                gap: 14px;
            }

            .results-dimension-card--recommended {
                border-color: rgba(217, 106, 43, 0.35);
                box-shadow: 0 20px 44px rgba(168, 74, 25, 0.12);
            }

            .results-dimension-card__header {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                align-items: flex-start;
            }

            .results-progress {
                width: 100%;
                height: 10px;
                border-radius: 999px;
                background: rgba(22, 33, 29, 0.1);
                overflow: hidden;
            }

            .results-progress span {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(135deg, #d96a2b 0%, #20453a 100%);
            }

            .results-badge,
            .results-next-step {
                display: inline-flex;
                width: fit-content;
                align-items: center;
                gap: 8px;
                padding: 7px 12px;
                border-radius: 999px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.82rem;
                font-weight: 700;
                background: rgba(32, 69, 58, 0.08);
                color: #20453a;
            }

            .results-badge--recommended,
            .results-next-step {
                background: rgba(217, 106, 43, 0.2);
                color: #16211d;
            }

            .questionnaire-feedback--results {
                background: rgba(217, 106, 43, 0.1);
                border-color: rgba(168, 74, 25, 0.24);
                color: #16211d;
            }

            @media (max-width: 720px) {
                .panel {
                    padding: 22px;
                }

                .results-summary-grid,
                .results-dimension-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <div class="questionnaire-results-page">
        <section class="panel">
            <x-user-page-heading
                :eyebrow="__('hermes.questionnaire.results.result_eyebrow')"
                :title="$organizationQuestionnaire->questionnaire->title"
                :text="__('hermes.questionnaire.results.result_intro', ['datetime' => $response->submitted_at->format('d-m-Y H:i')])"
            />

            @if (session('status'))
                <x-user-feedback :messages="[session('status')]" />
            @endif

            <x-user-action-row>
                <a href="{{ route('questionnaire-responses.show', $organizationQuestionnaire) }}" class="pill">
                    {{ __('hermes.questionnaires.start_questionnaire') }}
                </a>
                <a href="{{ route('questionnaires.index') }}" class="ghost-pill">
                    {{ __('hermes.questionnaires.back_to_library') }}
                </a>
            </x-user-action-row>
        </section>

        @include('questionnaires.partials.results', ['analysisResult' => $analysisResult])
    </div>
</x-layouts.hermes-dashboard>
