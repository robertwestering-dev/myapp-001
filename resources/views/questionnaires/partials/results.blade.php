<section class="panel panel--results">
    <x-user-page-heading
        :eyebrow="__('hermes.questionnaire.results.eyebrow')"
        :title="$analysisResult->title"
        :text="$analysisResult->summary"
    />

    <div class="results-summary-grid">
        <x-user-stat-tile
            :label="__('hermes.questionnaire.results.total_score')"
            :value="$analysisResult->score !== null && $analysisResult->maxScore !== null
                ? $analysisResult->score.' / '.$analysisResult->maxScore
                : __('hermes.questionnaire.results.not_available')"
        />

        <x-user-stat-tile
            tone="warning"
            :label="__('hermes.questionnaire.results.profile')"
            :value="$analysisResult->profileLabel"
        />

        @if ($analysisResult->recommendedDimensionLabel)
            <x-user-stat-tile
                :label="__('hermes.questionnaire.results.recommended_dimension')"
                :value="$analysisResult->recommendedDimensionLabel"
            />
        @endif
    </div>

    @if ($analysisResult->recommendedActionLabel)
        <x-user-feedback class="questionnaire-feedback questionnaire-feedback--results" :messages="[$analysisResult->recommendedActionLabel]" />
    @endif

    @if ($analysisResult->dimensions !== [])
        <div class="results-dimensions">
            <x-user-section-heading
                :eyebrow="__('hermes.questionnaire.results.dimensions_eyebrow')"
                :title="__('hermes.questionnaire.results.dimensions_title')"
                :text="__('hermes.questionnaire.results.dimensions_text')"
            />

            <div class="results-dimension-grid">
                @foreach ($analysisResult->dimensions as $dimension)
                    @php
                        $badgeClasses = 'results-badge'.($dimension->isRecommended ? ' results-badge--recommended' : '');
                        $cardClasses = 'results-dimension-card'.($dimension->isRecommended ? ' results-dimension-card--recommended' : '');
                        $progress = $dimension->score !== null && $dimension->maxScore
                            ? max(min(($dimension->score / $dimension->maxScore) * 100, 100), 0)
                            : 0;
                    @endphp

                    <x-user-surface-card class="{{ $cardClasses }}">
                        <div class="results-dimension-card__header">
                            <div>
                                <h3>{{ $dimension->label }}</h3>
                                @if ($dimension->statusLabel)
                                    <span class="{{ $badgeClasses }}">
                                        {{ $dimension->isRecommended
                                            ? __('hermes.questionnaire.results.recommended_badge')
                                            : $dimension->statusLabel }}
                                    </span>
                                @endif
                            </div>

                            @if ($dimension->score !== null && $dimension->maxScore !== null)
                                <strong>{{ $dimension->score }} / {{ $dimension->maxScore }}</strong>
                            @endif
                        </div>

                        @if ($dimension->score !== null && $dimension->maxScore !== null)
                            <div class="results-progress" aria-hidden="true">
                                <span style="width: {{ $progress }}%;"></span>
                            </div>
                        @endif

                        <p>{{ $dimension->summary }}</p>

                        @if ($dimension->actionLabel)
                            <div class="results-next-step">{{ $dimension->actionLabel }}</div>
                        @endif
                    </x-user-surface-card>
                @endforeach
            </div>
        </div>
    @endif
</section>
