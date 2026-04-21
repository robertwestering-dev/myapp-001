<x-layouts.hermes-dashboard :title="$organizationQuestionnaire->questionnaire->localized_title ?? $organizationQuestionnaire->questionnaire->title">
    <x-slot:head>
        <style>
        :root {
            --bg: #f4efe6;
            --paper: rgba(255, 255, 255, 0.78);
            --paper-strong: rgba(255, 255, 255, 0.9);
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --forest: #20453a;
            --shadow: 0 24px 60px rgba(24, 34, 30, 0.14);
            --radius-xl: 32px;
            --radius-lg: 24px;
            --radius-md: 18px;
            --content: 1180px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Georgia", "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(217, 106, 43, 0.18), transparent 32%),
                radial-gradient(circle at 85% 20%, rgba(32, 69, 58, 0.14), transparent 28%),
                linear-gradient(180deg, #f8f2e8 0%, #f2ece2 48%, #ebe3d8 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(244, 237, 227, 0.78);
            border-bottom: 1px solid rgba(23, 35, 33, 0.08);
        }

        .topbar__inner {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar__left,
        .topbar__actions,
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .topbar__left {
            min-width: 0;
            flex: 1;
        }

        .topbar__menu {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-left: 16px;
            white-space: nowrap;
        }

        .topbar__menu a {
            font-size: 0.98rem;
            font-weight: 600;
            color: var(--ink);
        }

        .topbar__menu a:hover {
            color: var(--accent-deep);
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 54px;
            max-width: 100%;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.72);
            color: var(--ink);
            font-family: "Avenir Next", "Segoe UI", sans-serif;
            font-size: 0.94rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .pill--booking {
            border-color: transparent;
            color: #f8f3eb;
            background: linear-gradient(180deg, rgba(30, 71, 61, 0.96), rgba(16, 42, 35, 0.98));
        }

        .pill--neutral {
            background: linear-gradient(135deg, #8a8f97 0%, #666c74 100%);
            color: #fff;
            border-color: transparent;
        }

        .locale-switcher {
            display: inline-flex;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        .locale-switcher__label {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .locale-switcher__select {
            min-width: 72px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.82);
            color: var(--ink);
            font: inherit;
            font-size: 0.82rem;
            font-weight: 700;
        }

        main {
            flex: 1;
            padding: 34px 0 60px;
        }

        .page,
        .site-footer__inner {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .page {
            display: grid;
            gap: 24px;
        }

        .panel,
        .step {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
        }

        .panel {
            border-radius: var(--radius-xl);
            padding: 32px;
        }

        .panel--intro {
            display: grid;
            gap: 22px;
        }

        .panel--results {
            display: grid;
            gap: 24px;
        }

        .panel--instructions {
            background:
                linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                var(--forest);
            color: #f6f2eb;
        }

        .panel--instructions .eyebrow {
            margin-bottom: 14px;
        }

        .panel--intro h1,
        .panel--intro .lead,
        .panel--instructions .lead {
            max-width: none;
            width: 100%;
        }

        .panel--intro .user-page-heading,
        .panel--intro .user-page-heading__body,
        .panel--intro .user-page-heading p {
            max-width: none;
            width: 100%;
        }

        .panel--instructions .lead {
            margin-bottom: 14px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .panel--instructions .eyebrow {
            background: rgba(255, 255, 255, 0.12);
            color: #f6f2eb;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1 {
            font-size: clamp(1.65rem, 3vw, 2.9rem);
            line-height: 1.08;
            margin: 0;
        }

        h2 {
            margin-bottom: 10px;
            font-size: clamp(1.3rem, 2.2vw, 2rem);
        }

        .lead {
            font-size: 1.08rem;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 0;
        }

        .panel--instructions .lead,
        .panel--instructions .muted,
        .panel--instructions .user-section-heading p,
        .panel--instructions .user-section-heading h2,
        .panel--instructions .user-section-heading__eyebrow {
            color: #fff !important;
        }

        .panel--instructions .user-section-heading,
        .panel--instructions .user-section-heading p {
            max-width: none;
            width: 100%;
        }

        .meta-list,
        .instruction-grid,
        .results-dimensions {
            display: grid;
            gap: 14px;
        }

        .questionnaire-feedback__instruction {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            line-height: 1.6;
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

        .results-dimension-card__header h3 {
            margin-bottom: 8px;
            font-size: 1.08rem;
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
            color: var(--forest);
        }

        .results-badge--recommended,
        .results-next-step {
            background: rgba(217, 106, 43, 0.2);
            color: #16211d;
            border: 1px solid rgba(168, 74, 25, 0.2);
        }

        .questionnaire-feedback--results {
            background: rgba(217, 106, 43, 0.1);
            border-color: rgba(168, 74, 25, 0.24);
            color: var(--ink);
        }

        .questionnaire-feedback {
            border-radius: var(--radius-md);
        }

        .questionnaire-feedback--inline {
            display: block;
        }

        .questionnaire-feedback.user-feedback--status {
            background: rgba(32, 69, 58, 0.14);
            border-color: rgba(32, 69, 58, 0.24);
            color: #16211d;
        }

        .questionnaire-feedback.user-feedback--errors {
            background: rgba(217, 106, 43, 0.1);
            border-color: rgba(168, 74, 25, 0.26);
            color: #ffd9c8;
        }

        .meta-link {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .progress-row {
            display: grid;
            gap: 18px;
            margin-bottom: 22px;
        }

        .progress-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .progress-label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.94rem;
            font-weight: 600;
            color: var(--muted);
        }

        .progress-copy {
            display: grid;
            gap: 4px;
        }

        .progress-title {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--ink);
        }

        .progress-meta {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .progress-pills {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .progress-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 40px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.66);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--muted);
        }

        .progress-pill.is-active {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            border-color: transparent;
            color: #fff;
        }

        .progress-pill.is-complete {
            border-color: rgba(32, 69, 58, 0.2);
            color: var(--forest);
        }

        form {
            display: grid;
            gap: 22px;
        }

        .step {
            border-radius: var(--radius-lg);
            padding: 28px;
            background: var(--paper-strong);
        }

        .step[hidden] {
            display: none;
        }

        .question[hidden] {
            display: none;
        }

        .step__header {
            display: grid;
            gap: 8px;
            margin-bottom: 8px;
        }

        .step__counter {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--accent-deep);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .question-list {
            display: grid;
            gap: 18px;
        }

        .question {
            display: grid;
            gap: 10px;
            padding-top: 18px;
        }

        .question + .question {
            border-top: 1px solid rgba(22, 33, 29, 0.12);
        }

        .question--invalid {
            border-top-color: rgba(168, 74, 25, 0.34);
        }

        .question--invalid label,
        .question--invalid legend {
            color: var(--accent-deep);
        }

        label,
        .question legend {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1.08rem;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.94);
            color: var(--ink);
            font: inherit;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        fieldset {
            margin: 0;
            padding: 0;
            border: 0;
            display: grid;
            gap: 10px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 400;
        }

        .option input[type="radio"] {
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            accent-color: var(--forest);
        }

        .likert-scale {
            display: grid;
            gap: 14px;
        }

        .likert-scale__track {
            display: grid;
            grid-template-columns: repeat(var(--likert-count, 5), minmax(0, 1fr));
            gap: 12px;
            width: 100%;
            padding: 16px;
            border-radius: 22px;
            border: 1px solid rgba(22, 33, 29, 0.12);
            background:
                linear-gradient(90deg, rgba(168, 74, 25, 0.08), rgba(255, 255, 255, 0.92) 50%, rgba(32, 69, 58, 0.08)),
                rgba(255, 255, 255, 0.82);
        }

        .likert-scale__option {
            min-width: 0;
        }

        .likert-scale__option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .likert-scale__card {
            height: 100%;
            min-height: 52px;
            display: grid;
            align-content: center;
            justify-items: center;
            gap: 0;
            padding: 10px 12px;
            border-radius: 18px;
            border: 1px solid rgba(22, 33, 29, 0.12);
            background: rgba(255, 255, 255, 0.96);
            text-align: center;
            transition: border-color 160ms ease, background 160ms ease, transform 160ms ease, box-shadow 160ms ease;
        }

        .likert-scale__option input:checked + .likert-scale__card {
            border-color: rgba(47, 122, 87, 0.45);
            background: rgba(47, 122, 87, 0.25);
            box-shadow: 0 12px 24px rgba(24, 34, 30, 0.1);
            transform: translateY(-2px);
        }

        .likert-scale__label {
            font-size: 0.88rem;
            line-height: 1.35;
            color: var(--ink);
        }

        .likert-scale__option input:checked + .likert-scale__card .likert-scale__label {
            color: #1f5b40;
        }

        .muted {
            color: var(--muted);
        }

        .error,
        .question__client-error {
            color: var(--accent-deep);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
        }

        .question__client-error[hidden] {
            display: none;
        }

        .actions {
            display: grid;
            gap: 12px;
            margin-top: 8px;
        }

        .questionnaire-pill,
        .ghost-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 18px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .questionnaire-pill {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            border-color: transparent;
        }

        .questionnaire-pill--submit {
            background: linear-gradient(135deg, #2f7a57 0%, #1f5b40 100%);
            color: #fff;
            border-color: transparent;
        }

        .ghost-pill--draft {
            background: linear-gradient(135deg, #8e949c 0%, #676d75 100%);
            color: #fff;
            border-color: transparent;
        }

        .questionnaire-pill[hidden],
        .ghost-pill[hidden] {
            display: none;
        }

        .site-footer__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .site-footer {
            background: rgba(244, 239, 230, 0.78);
            border-top: 1px solid rgba(22, 33, 29, 0.08);
        }

        @media (max-width: 780px) {
            .topbar__inner {
                align-items: flex-start;
                flex-direction: column;
                height: auto;
                min-height: 80px;
                padding: 12px 0;
            }

            .topbar__left {
                width: 100%;
                flex-wrap: wrap;
                gap: 10px 14px;
            }

            .topbar__menu {
                margin-left: 0;
            }

            .topbar__actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 720px) {
            .panel,
            .step {
                padding: 22px;
            }

            .likert-scale__track {
                grid-template-columns: 1fr;
            }

            .results-summary-grid,
            .results-dimension-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                align-items: stretch;
            }

            .user-action-row,
            .questionnaire-pill,
            .ghost-pill {
                width: 100%;
            }
        }
        </style>
    </x-slot:head>

    @php
        $categories = $organizationQuestionnaire->questionnaire->categories->values();
        $firstErrorCategoryIndex = $categories->search(function ($category) use ($errors): bool {
            return $category->questions->contains(function ($question) use ($errors): bool {
                return $errors->has("answers.{$question->id}");
            });
        });
        $savedCategoryIndex = $initialCategoryId
            ? $categories->search(fn ($category): bool => $category->id === $initialCategoryId)
            : false;
        $initialStepIndex = $firstErrorCategoryIndex === false
            ? ($savedCategoryIndex === false ? 0 : (int) $savedCategoryIndex)
            : (int) $firstErrorCategoryIndex;
        $initialCategory = $categories->get($initialStepIndex);
        $initialVisibleQuestionCount = $initialCategory?->questions->whereIn('id', $visibleQuestionIds)->count() ?? 0;
        $initialAnsweredQuestionCount = $initialCategory?->questions
            ->whereIn('id', $visibleQuestionIds)
            ->filter(fn ($question) => filled($existingAnswers[$question->id] ?? null))
            ->count() ?? 0;
        $initialTotalAnsweredQuestionCount = collect($visibleQuestionIds)
            ->filter(fn ($questionId): bool => filled($existingAnswers[$questionId] ?? null))
            ->count();
        $isCompletedResponse = $response?->submitted_at !== null;
    @endphp

        <div class="page">
            <section class="panel panel--intro">
                <x-user-page-heading
                    :eyebrow="__('hermes.questionnaire.eyebrow')"
                    :title="$organizationQuestionnaire->questionnaire->localized_title ?? $organizationQuestionnaire->questionnaire->title"
                    :text="$organizationQuestionnaire->questionnaire->localized_description ?? $organizationQuestionnaire->questionnaire->description"
                />

                <div class="meta-list">
                    @if (session('status'))
                        <x-user-feedback class="questionnaire-feedback" :messages="[session('status')]" />
                    @endif

                    @unless ($isCompletedResponse)
                        <x-user-feedback class="questionnaire-feedback questionnaire-feedback--inline" always-render>
                            <span class="questionnaire-feedback__instruction">{{ __('hermes.questionnaire.instructions_text') }}</span>
                            <span data-autosave-status hidden></span>
                        </x-user-feedback>
                    @endunless

                    @if ($response?->last_saved_at)
                        <x-user-inline-meta :items="[__('hermes.questionnaire.last_saved', ['datetime' => $response->last_saved_at->format('d-m-Y H:i')])]" />
                    @endif

                    @if ($response?->submitted_at)
                        <x-user-inline-meta :items="[__('hermes.questionnaire.last_completed', ['datetime' => $response->submitted_at->format('d-m-Y H:i')])]" />
                        <x-user-inline-meta :items="[__('hermes.questionnaire.completed_locked_draft')]" />
                    @endif

                    @if ($resumeUrl)
                        <x-user-inline-meta>
                            <span>{{ __('hermes.questionnaire.resume_hint') }}</span>
                            <a href="{{ $resumeUrl }}" class="meta-link">{{ __('hermes.questionnaire.resume_link') }}</a>
                        </x-user-inline-meta>
                    @endif

                    @if ($errors->any())
                        <x-user-feedback variant="errors" class="questionnaire-feedback" :messages="$errors->all()" />
                    @endif
                </div>
            </section>

            @if ($analysisResult)
                @include('questionnaires.partials.results', ['analysisResult' => $analysisResult])
            @endif

            <section class="panel">
                <div class="progress-row">
                    <div class="progress-summary">
                        <div class="progress-copy">
                            <div class="progress-label" data-questionnaire-progress-label>
                                {{ __('hermes.questionnaire.step_of', ['current' => $initialStepIndex + 1, 'total' => max($categories->count(), 1)]) }}
                            </div>
                            <div class="progress-title" data-questionnaire-progress-title>{{ $initialCategory?->localized_title ?? $initialCategory?->title }}</div>
                            <div class="progress-meta" data-questionnaire-progress-meta>
                                {{ __('hermes.questionnaire.section_progress', ['answered' => $initialAnsweredQuestionCount, 'total' => max($initialVisibleQuestionCount, 1)]) }}
                            </div>
                        </div>

                        <div class="progress-meta" data-questionnaire-total-progress>
                            {{ __('hermes.questionnaire.total_progress', ['answered' => $initialTotalAnsweredQuestionCount, 'total' => max(count($visibleQuestionIds), 1)]) }}
                        </div>
                    </div>

                    <div class="progress-pills" aria-label="Voortgang door de questionnaire">
                        @foreach ($categories as $categoryIndex => $category)
                            <span
                                class="progress-pill @if ($categoryIndex === $initialStepIndex) is-active @endif"
                                data-progress-pill
                            >
                                {{ $categoryIndex + 1 }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('questionnaire-responses.store', $organizationQuestionnaire) }}"
                    novalidate
                    data-questionnaire-form
                    data-step-total="{{ max($categories->count(), 1) }}"
                    data-initial-step="{{ $initialStepIndex }}"
                    data-is-completed="{{ $isCompletedResponse ? 'true' : 'false' }}"
                >
                    @csrf
                    <input type="hidden" name="current_category_id" value="{{ $initialCategory?->id }}" data-current-category-input>

                    @foreach ($categories as $categoryIndex => $category)
                        <section
                            class="step"
                            data-questionnaire-step
                            data-step-index="{{ $categoryIndex }}"
                            data-step-category-id="{{ $category->id }}"
                            data-step-title="{{ $category->localized_title ?? $category->title }}"
                            @if ($categoryIndex !== $initialStepIndex) hidden @endif
                        >
                            <div class="step__header">
                                <span class="step__counter">{{ __('hermes.questionnaire.step_of', ['current' => $categoryIndex + 1, 'total' => $categories->count()]) }}</span>
                                <x-user-section-heading
                                    :title="$category->localized_title ?? $category->title"
                                    :text="$category->localized_description ?? $category->description"
                                />
                            </div>

                            <div class="question-list">
                                @foreach ($category->questions as $question)
                                    @php($answer = old("answers.{$question->id}", $existingAnswers[$question->id] ?? null))
                                    <div
                                        class="question"
                                        data-question
                                        data-question-id="{{ $question->id }}"
                                        data-visible-default="{{ in_array($question->id, $visibleQuestionIds, true) ? 'true' : 'false' }}"
                                        data-condition-question-id="{{ $question->display_condition_question_id }}"
                                        data-condition-operator="{{ $question->display_condition_operator }}"
                                        data-condition-answer='@json($question->display_condition_answer ?? [])'
                                        data-required="{{ $question->is_required ? 'true' : 'false' }}"
                                        @if (! in_array($question->id, $visibleQuestionIds, true)) hidden @endif
                                    >
                                        <label for="question-{{ $question->id }}">
                                            {{ $question->prompt }}@if ($question->is_required) * @endif
                                        </label>

                                        @if ($question->help_text)
                                            <div class="muted">{{ $question->help_text }}</div>
                                        @endif

                                        @if ($question->type === \App\Models\QuestionnaireQuestion::TYPE_SHORT_TEXT)
                                            <input
                                                id="question-{{ $question->id }}"
                                                type="text"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ is_array($answer) ? '' : $answer }}"
                                                @required($question->is_required)
                                            >
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_LONG_TEXT)
                                            <textarea
                                                id="question-{{ $question->id }}"
                                                name="answers[{{ $question->id }}]"
                                                @required($question->is_required)
                                            >{{ is_array($answer) ? '' : $answer }}</textarea>
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_NUMBER)
                                            <input
                                                id="question-{{ $question->id }}"
                                                type="number"
                                                step="any"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ is_array($answer) ? '' : $answer }}"
                                                @required($question->is_required)
                                            >
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_DATE)
                                            <input
                                                id="question-{{ $question->id }}"
                                                type="date"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ is_array($answer) ? '' : $answer }}"
                                                @required($question->is_required)
                                            >
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_BOOLEAN)
                                            <select id="question-{{ $question->id }}" name="answers[{{ $question->id }}]" @required($question->is_required)>
                                                <option value="">Maak een keuze</option>
                                                <option value="1" @selected((string) $answer === '1')>Ja</option>
                                                <option value="0" @selected((string) $answer === '0')>Nee</option>
                                            </select>
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_SINGLE_CHOICE)
                                            <fieldset>
                                                @foreach ($question->options ?? [] as $optionIndex => $option)
                                                    <label class="option">
                                                        <input
                                                            id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                            type="radio"
                                                            name="answers[{{ $question->id }}]"
                                                            value="{{ $option }}"
                                                            @checked($answer === $option)
                                                            @required($question->is_required && $optionIndex === 0)
                                                        >
                                                        <span>{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </fieldset>
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_LIKERT_SCALE)
                                            <fieldset class="likert-scale">
                                                <div class="likert-scale__track" style="--likert-count: {{ max(count($question->options ?? []), 1) }};">
                                                    @foreach ($question->options ?? [] as $optionIndex => $option)
                                                        <label class="likert-scale__option">
                                                            <input
                                                                id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                type="radio"
                                                                name="answers[{{ $question->id }}]"
                                                            value="{{ $option }}"
                                                            @checked($answer === $option)
                                                            @required($question->is_required && $optionIndex === 0)
                                                        >
                                                            <span class="likert-scale__card">
                                                                <span class="likert-scale__label">{{ $option }}</span>
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </fieldset>
                                        @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE)
                                            <fieldset>
                                                @foreach ($question->options ?? [] as $option)
                                                    <label class="option">
                                                        <input
                                                            type="checkbox"
                                                            name="answers[{{ $question->id }}][]"
                                                            value="{{ $option }}"
                                                            @checked(is_array($answer) && in_array($option, $answer, true))
                                                        >
                                                        <span>{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </fieldset>
                                        @endif

                                        @error("answers.{$question->id}")
                                            <div class="error">{{ $message }}</div>
                                        @enderror

                                        <div class="question__client-error" data-question-client-error hidden></div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <div class="actions">
                        <x-user-action-row>
                            <button type="button" class="ghost-pill" data-previous-step>{{ __('hermes.questionnaire.previous_step') }}</button>
                            <button type="button" class="questionnaire-pill" data-next-step>{{ __('hermes.questionnaire.next_step') }}</button>
                        </x-user-action-row>

                        <x-user-action-row align="end">
                            @unless ($isCompletedResponse)
                                <button type="submit" class="ghost-pill ghost-pill--draft" name="intent" value="draft" data-save-draft formnovalidate>{{ __('hermes.questionnaire.save_draft') }}</button>
                            @endunless

                            <button type="submit" class="questionnaire-pill questionnaire-pill--submit" name="intent" value="submit" data-submit-step>{{ __('hermes.questionnaire.submit') }}</button>
                        </x-user-action-row>
                    </div>
                </form>
            </section>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-questionnaire-form]');
            const progressPanel = form?.closest('.panel');

            if (! form) {
                return;
            }

            const steps = Array.from(form.querySelectorAll('[data-questionnaire-step]'));
            const progressLabel = document.querySelector('[data-questionnaire-progress-label]');
            const progressTitle = document.querySelector('[data-questionnaire-progress-title]');
            const progressMeta = document.querySelector('[data-questionnaire-progress-meta]');
            const totalProgress = document.querySelector('[data-questionnaire-total-progress]');
            const progressPills = Array.from(document.querySelectorAll('[data-progress-pill]'));
            const previousButton = form.querySelector('[data-previous-step]');
            const nextButton = form.querySelector('[data-next-step]');
            const submitButton = form.querySelector('[data-submit-step]');
            const draftButton = form.querySelector('[data-save-draft]');
            const autosaveStatus = document.querySelector('[data-autosave-status]');
            const currentCategoryInput = form.querySelector('[data-current-category-input]');
            const isCompletedResponse = form.dataset.isCompleted === 'true';
            let currentStepIndex = Number(form.dataset.initialStep || 0);
            let autosaveTimeout = null;
            let autosaveRequest = null;
            let isDirty = false;

            const normalizeValue = (value) => String(value || '').trim();

            const setAutosaveStatus = (message, isVisible = true) => {
                if (! autosaveStatus) {
                    return;
                }

                autosaveStatus.hidden = ! isVisible || message === '';
                autosaveStatus.textContent = message;
            };

            const answerValuesForQuestion = (questionId) => {
                const controls = Array.from(form.querySelectorAll(`[name="answers[${questionId}]"], [name="answers[${questionId}][]"]`));

                if (controls.length === 0) {
                    return [];
                }

                const firstControl = controls[0];

                if (firstControl.type === 'radio' || firstControl.type === 'checkbox') {
                    return controls
                        .filter((control) => control.checked)
                        .map((control) => normalizeValue(control.value))
                        .filter(Boolean);
                }

                const value = normalizeValue(firstControl.value);

                return value === '' ? [] : [value];
            };

            const isQuestionVisible = (question, questionMap, visibilityCache, inProgress = new Set()) => {
                const questionId = question.dataset.questionId;

                if (visibilityCache.has(questionId)) {
                    return visibilityCache.get(questionId);
                }

                const dependencyId = question.dataset.conditionQuestionId;
                const operator = question.dataset.conditionOperator;

                if (! dependencyId || ! operator) {
                    visibilityCache.set(questionId, true);

                    return true;
                }

                if (inProgress.has(questionId)) {
                    visibilityCache.set(questionId, false);

                    return false;
                }

                const dependencyQuestion = questionMap.get(String(dependencyId));

                if (! dependencyQuestion) {
                    visibilityCache.set(questionId, false);

                    return false;
                }

                inProgress.add(questionId);

                if (! isQuestionVisible(dependencyQuestion, questionMap, visibilityCache, inProgress)) {
                    inProgress.delete(questionId);
                    visibilityCache.set(questionId, false);

                    return false;
                }

                const expected = JSON.parse(question.dataset.conditionAnswer || '[]');
                const answerValues = answerValuesForQuestion(dependencyId);
                let visible = true;

                if (operator === 'answered') {
                    visible = answerValues.length > 0;
                } else if (operator === 'not_answered') {
                    visible = answerValues.length === 0;
                } else if (operator === 'equals') {
                    visible = answerValues.length === 1 && expected.length === 1 && answerValues[0] === expected[0];
                } else if (operator === 'not_equals') {
                    visible = ! (answerValues.length === 1 && expected.length === 1 && answerValues[0] === expected[0]);
                } else if (operator === 'contains') {
                    visible = expected.some((value) => answerValues.includes(value));
                } else if (operator === 'not_contains') {
                    visible = expected.every((value) => ! answerValues.includes(value));
                }

                inProgress.delete(questionId);
                visibilityCache.set(questionId, visible);

                return visible;
            };

            const clearClientError = (question) => {
                question.classList.remove('question--invalid');

                const errorElement = question.querySelector('[data-question-client-error]');

                if (errorElement) {
                    errorElement.hidden = true;
                    errorElement.textContent = '';
                }
            };

            const setClientError = (question, message) => {
                question.classList.add('question--invalid');

                const errorElement = question.querySelector('[data-question-client-error]');

                if (errorElement) {
                    errorElement.hidden = false;
                    errorElement.textContent = message;
                }
            };

            const validateQuestion = (question) => {
                clearClientError(question);

                if (question.hidden) {
                    return true;
                }

                if (question.dataset.required !== 'true') {
                    return true;
                }

                const controls = Array.from(question.querySelectorAll('input, textarea, select'));

                if (controls.length === 0) {
                    return true;
                }

                const firstControl = controls[0];

                if (firstControl.type === 'radio') {
                    const isChecked = controls.some((control) => control.checked);

                    if (! isChecked) {
                        setClientError(question, @json(__('hermes.questionnaire.validation.required')));

                        return false;
                    }

                    return true;
                }

                if (firstControl.type === 'checkbox') {
                    const isChecked = controls.some((control) => control.checked);

                    if (! isChecked) {
                        setClientError(question, @json(__('hermes.questionnaire.validation.required')));

                        return false;
                    }

                    return true;
                }

                const value = (firstControl.value || '').trim();

                if (value === '') {
                    firstControl.reportValidity();
                    setClientError(question, @json(__('hermes.questionnaire.validation.required')));

                    return false;
                }

                if (! firstControl.checkValidity()) {
                    firstControl.reportValidity();

                    return false;
                }

                return true;
            };

            const syncConditionalQuestions = () => {
                const questions = Array.from(form.querySelectorAll('[data-question]'));
                const questionMap = new Map(questions.map((question) => [question.dataset.questionId, question]));
                const visibilityCache = new Map();

                questions.forEach((question) => {
                    const shouldShow = isQuestionVisible(question, questionMap, visibilityCache);
                    question.hidden = ! shouldShow;

                    const controls = Array.from(question.querySelectorAll('input, textarea, select'));

                    controls.forEach((control) => {
                        control.disabled = ! shouldShow;
                    });

                    if (! shouldShow) {
                        clearClientError(question);

                        return;
                    }
                });
            };

            const visibleSteps = () => steps.filter((step) => Array.from(step.querySelectorAll('[data-question]')).some((question) => ! question.hidden));
            const hasAnswer = (question) => answerValuesForQuestion(question.dataset.questionId).length > 0;

            const scrollToFormTop = () => {
                const target = progressPanel || form;

                if (! target) {
                    return;
                }

                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            };

            const validateStep = (step) => {
                const questions = Array.from(step.querySelectorAll('[data-question]')).filter((question) => ! question.hidden);
                let firstInvalidControl = null;

                const isValid = questions.every((question) => {
                    const valid = validateQuestion(question);

                    if (! valid && firstInvalidControl === null) {
                        firstInvalidControl = question.querySelector('input, textarea, select');
                    }

                    return valid;
                });

                if (! isValid && firstInvalidControl) {
                    firstInvalidControl.focus();
                }

                return isValid;
            };

            const renderStep = () => {
                syncConditionalQuestions();

                const activeSteps = visibleSteps();
                const totalSteps = Math.max(activeSteps.length, 1);

                if (activeSteps.length > 0) {
                    currentStepIndex = Math.min(currentStepIndex, activeSteps.length - 1);
                } else {
                    currentStepIndex = 0;
                }

                steps.forEach((step) => {
                    step.hidden = true;
                });

                const currentStep = activeSteps[currentStepIndex];

                if (currentStep) {
                    currentStep.hidden = false;
                }

                if (currentCategoryInput) {
                    currentCategoryInput.value = currentStep?.dataset.stepCategoryId || '';
                }

                progressPills.forEach((pill, index) => {
                    const isVisible = activeSteps.includes(steps[index]);
                    pill.hidden = ! isVisible;
                    pill.classList.toggle('is-active', isVisible && activeSteps[currentStepIndex] === steps[index]);
                    pill.classList.toggle('is-complete', isVisible && activeSteps.indexOf(steps[index]) < currentStepIndex);
                });

                const visibleQuestions = currentStep
                    ? Array.from(currentStep.querySelectorAll('[data-question]')).filter((question) => ! question.hidden)
                    : [];
                const answeredQuestions = visibleQuestions.filter((question) => hasAnswer(question)).length;
                const allVisibleQuestions = activeSteps.flatMap((step) => Array.from(step.querySelectorAll('[data-question]')).filter((question) => ! question.hidden));
                const allAnsweredQuestions = allVisibleQuestions.filter((question) => hasAnswer(question)).length;

                if (progressLabel) {
                    progressLabel.textContent = @json(__('hermes.questionnaire.step_of', ['current' => '__CURRENT__', 'total' => '__TOTAL__']))
                        .replace('__CURRENT__', String(currentStepIndex + 1))
                        .replace('__TOTAL__', String(totalSteps));
                }

                if (progressTitle) {
                    progressTitle.textContent = currentStep?.dataset.stepTitle || '';
                }

                if (progressMeta) {
                    progressMeta.textContent = @json(__('hermes.questionnaire.section_progress', ['answered' => '__ANSWERED__', 'total' => '__TOTAL__']))
                        .replace('__ANSWERED__', String(answeredQuestions))
                        .replace('__TOTAL__', String(Math.max(visibleQuestions.length, 1)));
                }

                if (totalProgress) {
                    totalProgress.textContent = @json(__('hermes.questionnaire.total_progress', ['answered' => '__ANSWERED__', 'total' => '__TOTAL__']))
                        .replace('__ANSWERED__', String(allAnsweredQuestions))
                        .replace('__TOTAL__', String(Math.max(allVisibleQuestions.length, 1)));
                }

                if (previousButton) {
                    previousButton.hidden = currentStepIndex === 0;
                }

                if (nextButton) {
                    nextButton.hidden = currentStepIndex >= totalSteps - 1;
                }

                if (submitButton) {
                    submitButton.hidden = currentStepIndex !== totalSteps - 1;
                }

                if (draftButton) {
                    draftButton.hidden = isCompletedResponse;
                }
            };

            const buildAutosavePayload = () => {
                const formData = new FormData(form);

                formData.set('intent', 'autosave');

                if (currentCategoryInput?.value) {
                    formData.set('current_category_id', currentCategoryInput.value);
                }

                return formData;
            };

            const autosave = async () => {
                if (isCompletedResponse) {
                    return;
                }

                if (! isDirty || submitButton?.disabled) {
                    return;
                }

                if (autosaveRequest) {
                    autosaveRequest.abort();
                }

                const controller = new AbortController();

                autosaveRequest = controller;

                setAutosaveStatus(@json(__('hermes.questionnaire.autosave_saving')));

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: buildAutosavePayload(),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: controller.signal,
                    });

                    if (! response.ok) {
                        throw new Error('Autosave failed.');
                    }

                    const payload = await response.json();

                    isDirty = false;
                    setAutosaveStatus(payload.message || @json(__('hermes.questionnaire.autosave_saved_generic')));
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    setAutosaveStatus(@json(__('hermes.questionnaire.autosave_failed')));
                }
            };

            const scheduleAutosave = () => {
                if (isCompletedResponse) {
                    return;
                }

                isDirty = true;

                if (autosaveTimeout) {
                    window.clearTimeout(autosaveTimeout);
                }

                autosaveTimeout = window.setTimeout(() => {
                    autosave();
                }, 1500);
            };

            previousButton?.addEventListener('click', () => {
                if (currentStepIndex === 0) {
                    return;
                }

                currentStepIndex -= 1;
                renderStep();
                scrollToFormTop();
            });

            nextButton?.addEventListener('click', () => {
                const currentStep = visibleSteps()[currentStepIndex];

                if (! currentStep || ! validateStep(currentStep)) {
                    return;
                }

                currentStepIndex += 1;
                renderStep();
                scrollToFormTop();
            });

            form.addEventListener('submit', (event) => {
                const submitter = event.submitter;

                if (autosaveTimeout) {
                    window.clearTimeout(autosaveTimeout);
                }

                if (! submitter || submitter.dataset.submitStep === undefined) {
                    return;
                }

                syncConditionalQuestions();

                const activeSteps = visibleSteps();

                for (const [index, step] of activeSteps.entries()) {
                    if (! validateStep(step)) {
                        event.preventDefault();
                        currentStepIndex = index;
                        renderStep();
                        return;
                    }
                }
            });

            form.addEventListener('change', () => {
                renderStep();
                scheduleAutosave();
            });

            form.addEventListener('input', (event) => {
                if (! event.target.matches('input, textarea, select')) {
                    return;
                }

                renderStep();
                scheduleAutosave();
            });

            renderStep();
            if (! isCompletedResponse) {
                setAutosaveStatus(@json(__('hermes.questionnaire.autosave_ready')));
            }
        });
    </script>
</x-layouts.hermes-dashboard>
