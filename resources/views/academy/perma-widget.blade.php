<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('hermes.academy.perma_widget.title') }}</title>
    <style>
        :root {
            color-scheme: light;
            --forest-deep: #173c34;
            --forest: #245346;
            --sand: #f6f1e8;
            --paper: #fffdf8;
            --ink: #18211d;
            --muted: #5c6a64;
            --line: rgba(24, 33, 29, 0.1);
            --accent: #be5b27;
            --success: #2f7d4a;
            --success-soft: rgba(47, 125, 74, 0.14);
            --neutral: #66746d;
            --neutral-soft: rgba(102, 116, 109, 0.14);
            --warning: #b36a18;
            --warning-soft: rgba(179, 106, 24, 0.14);
            --start: #2b6f8a;
            --start-soft: rgba(43, 111, 138, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(36, 83, 70, 0.12), transparent 34%),
                linear-gradient(180deg, #fcf9f3 0%, #f4ede2 100%);
            color: var(--ink);
            font-family: Georgia, "Times New Roman", serif;
        }

        .widget {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            padding: 18px;
        }

        .widget-card {
            display: grid;
            gap: 14px;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: rgba(255, 253, 248, 0.94);
            box-shadow: 0 22px 48px rgba(24, 33, 29, 0.08);
        }

        .widget-card--empty {
            max-width: 520px;
        }

        .widget-eyebrow {
            margin: 0;
            color: var(--forest);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-heading,
        .widget-empty {
            display: grid;
            gap: 6px;
        }

        .widget-heading h1,
        .widget-heading p,
        .widget-empty h1,
        .widget-empty p {
            margin: 0;
        }

        .widget-heading h1,
        .widget-empty h1 {
            font-size: clamp(1.35rem, 2vw, 1.8rem);
            line-height: 1.08;
            color: var(--forest-deep);
        }

        .widget-heading p,
        .widget-empty p,
        .widget-note {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.45;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-summary {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .widget-metric {
            display: grid;
            gap: 4px;
            padding: 12px 14px;
            border-radius: 18px;
            background: var(--sand);
            border: 1px solid rgba(24, 33, 29, 0.06);
        }

        .widget-metric span {
            color: var(--muted);
            font-size: 0.72rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-metric strong {
            color: var(--forest-deep);
            font-size: 1.08rem;
            line-height: 1.15;
        }

        .widget-dimensions {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .widget-dimension {
            display: grid;
            gap: 10px;
            padding: 12px;
            border-radius: 18px;
            border: 1px solid rgba(24, 33, 29, 0.08);
            background: #fff;
        }

        .widget-dimension--recommended {
            border-color: rgba(43, 111, 138, 0.34);
            box-shadow: inset 0 0 0 1px rgba(43, 111, 138, 0.08);
        }

        .widget-dimension__top {
            display: grid;
            gap: 4px;
        }

        .widget-dimension__name {
            font-size: 0.84rem;
            line-height: 1.15;
            color: var(--ink);
            font-weight: 700;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-dimension__score {
            color: var(--forest-deep);
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1;
        }

        .widget-dimension__status {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            line-height: 1;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-dimension__status--strong {
            color: var(--success);
            background: var(--success-soft);
        }

        .widget-dimension__status--partial {
            color: var(--neutral);
            background: var(--neutral-soft);
        }

        .widget-dimension__status--fragile {
            color: var(--warning);
            background: var(--warning-soft);
        }

        .widget-dimension__recommended {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            color: var(--start);
            background: var(--start-soft);
            font-size: 0.7rem;
            font-weight: 700;
            line-height: 1;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-dimension__recommended svg {
            width: 12px;
            height: 12px;
            flex: 0 0 12px;
        }

        .widget-progress {
            position: relative;
            overflow: hidden;
            height: 7px;
            border-radius: 999px;
            background: rgba(24, 33, 29, 0.1);
        }

        .widget-progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--accent), #e08941);
        }

        .widget-progress--strong span {
            background: linear-gradient(90deg, var(--success), #57a36f);
        }

        .widget-progress--partial span {
            background: linear-gradient(90deg, #7e8a84, #b2bbb7);
        }

        .widget-progress--fragile span {
            background: linear-gradient(90deg, var(--accent), #e08941);
        }

        .widget-note {
            padding: 12px 14px;
            border-radius: 18px;
            background: rgba(190, 91, 39, 0.1);
        }

        .widget-note strong {
            color: var(--forest-deep);
        }

        @media (max-width: 760px) {
            .widget {
                padding: 12px;
            }

            .widget-card {
                padding: 16px;
                border-radius: 20px;
            }

            .widget-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .widget-summary .widget-metric:first-child {
                grid-column: 1 / -1;
            }

            .widget-dimensions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <main class="widget">
        @if ($analysisResult === null || $response === null)
            <section class="widget-card widget-card--empty">
                <p class="widget-eyebrow">{{ __('hermes.academy.perma_widget.eyebrow') }}</p>
                <div class="widget-empty">
                    <h1>{{ __('hermes.academy.perma_widget.empty_title') }}</h1>
                    <p>{{ __('hermes.academy.perma_widget.empty_text') }}</p>
                </div>
            </section>
        @else
            <section class="widget-card compact-perma-widget">
                <div class="widget-heading">
                    <p class="widget-eyebrow">{{ __('hermes.academy.perma_widget.eyebrow') }}</p>
                    <h1>{{ __('hermes.academy.perma_widget.title') }}</h1>
                    <p>{{ __('hermes.academy.perma_widget.latest_result', ['datetime' => $response->submitted_at->format('d-m-Y H:i')]) }}</p>
                </div>

                <div class="widget-summary">
                    <div class="widget-metric">
                        <span>{{ __('hermes.questionnaire.results.total_score') }}</span>
                        <strong>{{ $analysisResult->score }} / {{ $analysisResult->maxScore }}</strong>
                    </div>
                    <div class="widget-metric">
                        <span>{{ __('hermes.questionnaire.results.profile') }}</span>
                        <strong>{{ $analysisResult->profileLabel }}</strong>
                    </div>
                    <div class="widget-metric">
                        <span>{{ __('hermes.questionnaire.results.recommended_dimension') }}</span>
                        <strong>{{ $analysisResult->recommendedDimensionLabel ?? __('hermes.academy.perma_widget.not_available') }}</strong>
                    </div>
                </div>

                @if ($analysisResult->dimensions !== [])
                    <div class="widget-dimensions">
                        @foreach ($analysisResult->dimensions as $dimension)
                            @php($progress = $dimension->score !== null && $dimension->maxScore ? max(min(($dimension->score / $dimension->maxScore) * 100, 100), 0) : 0)
                            @php($statusClass = match ($dimension->statusKey) {
                                'strong' => 'widget-dimension__status--strong',
                                'partial' => 'widget-dimension__status--partial',
                                'fragile' => 'widget-dimension__status--fragile',
                                default => 'widget-dimension__status--partial',
                            })
                            @php($progressClass = match ($dimension->statusKey) {
                                'strong' => 'widget-progress--strong',
                                'partial' => 'widget-progress--partial',
                                'fragile' => 'widget-progress--fragile',
                                default => 'widget-progress--partial',
                            })
                            <article class="widget-dimension {{ $dimension->isRecommended ? 'widget-dimension--recommended' : '' }}">
                                <div class="widget-dimension__top">
                                    <div class="widget-dimension__name">{{ $dimension->label }}</div>
                                    <div class="widget-dimension__score">{{ $dimension->score }} / {{ $dimension->maxScore }}</div>
                                </div>
                                <div class="widget-progress {{ $progressClass }}" aria-hidden="true">
                                    <span style="width: {{ $progress }}%;"></span>
                                </div>
                                @if ($dimension->isRecommended)
                                    <div class="widget-dimension__recommended">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 5v14"></path>
                                            <path d="m19 12-7 7-7-7"></path>
                                        </svg>
                                        Start here
                                    </div>
                                @endif
                                @if ($dimension->statusLabel)
                                    <div class="widget-dimension__status {{ $statusClass }}">
                                        {{ $dimension->statusLabel }}
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="widget-note">
                        <strong>{{ __('hermes.academy.perma_widget.dimensions_unavailable_title') }}</strong>
                        {{ __('hermes.academy.perma_widget.dimensions_unavailable_text') }}
                    </div>
                @endif
            </section>
        @endif
    </main>
</body>
</html>
