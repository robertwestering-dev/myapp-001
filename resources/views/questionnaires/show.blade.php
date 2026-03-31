<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $organizationQuestionnaire->questionnaire->title }}</title>
    <x-favicon-links />
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
        .panel--instructions .muted {
            color: rgba(246, 242, 235, 0.82);
        }

        .meta-list,
        .instruction-grid {
            display: grid;
            gap: 14px;
        }

        .instruction-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .instruction-card {
            padding: 18px;
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.08);
        }

        .instruction-card strong,
        .instruction-card span {
            display: block;
        }

        .instruction-card strong {
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .instruction-card span {
            font-family: Arial, Helvetica, sans-serif;
            color: rgba(246, 242, 235, 0.82);
        }

        .status,
        .error-summary {
            padding: 16px 18px;
            border-radius: var(--radius-md);
            font-family: Arial, Helvetica, sans-serif;
        }

        .status {
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.18);
        }

        .error-summary {
            background: rgba(217, 106, 43, 0.1);
            border: 1px solid rgba(168, 74, 25, 0.2);
            color: var(--accent-deep);
        }

        .error-summary div + div {
            margin-top: 6px;
        }

        .progress-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .progress-label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.94rem;
            font-weight: 600;
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

        label,
        .question legend {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .actions__group {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
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

        @media (max-width: 920px) {
            .instruction-grid {
                grid-template-columns: 1fr;
            }
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

            .actions {
                align-items: stretch;
            }

            .actions__group,
            .questionnaire-pill,
            .ghost-pill {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header :show-booking="false" />

    @php
        $categories = $organizationQuestionnaire->questionnaire->categories->values();
        $firstErrorCategoryIndex = $categories->search(function ($category) use ($errors): bool {
            return $category->questions->contains(function ($question) use ($errors): bool {
                return $errors->has("answers.{$question->id}");
            });
        });
        $initialStepIndex = $firstErrorCategoryIndex === false ? 0 : (int) $firstErrorCategoryIndex;
    @endphp

    <main>
        <div class="page">
            <section class="panel panel--intro">
                <span class="eyebrow">{{ __('hermes.questionnaire.eyebrow') }}</span>
                <h1>{{ $organizationQuestionnaire->questionnaire->title }}</h1>
                <p class="lead">{{ $organizationQuestionnaire->questionnaire->description }}</p>

                <div class="meta-list">
                    @if (session('status'))
                        <div class="status">{{ session('status') }}</div>
                    @endif

                    @if ($response?->submitted_at)
                        <p class="muted">{{ __('hermes.questionnaire.last_saved', ['datetime' => $response->submitted_at->format('d-m-Y H:i')]) }}</p>
                    @endif

                    @if ($errors->any())
                        <div class="error-summary">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            <section class="panel panel--instructions">
                <span class="eyebrow">{{ __('hermes.questionnaire.instructions') }}</span>
                <p class="lead">{{ __('hermes.questionnaire.instructions_text') }}</p>

                <div class="instruction-grid">
                    <div class="instruction-card">
                        <strong>{{ __('hermes.questionnaire.organization') }}</strong>
                        <span>{{ $organizationQuestionnaire->organization->naam ?? 'Niet gekoppeld' }}</span>
                    </div>

                    <div class="instruction-card">
                        <strong>{{ __('hermes.questionnaire.categories') }}</strong>
                        <span>{{ __('hermes.questionnaire.categories_count', ['count' => $categories->count()]) }}</span>
                    </div>

                    <div class="instruction-card">
                        <strong>{{ __('hermes.questionnaire.save') }}</strong>
                        <span>{{ __('hermes.questionnaire.save_text') }}</span>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="progress-row">
                    <div class="progress-label" data-questionnaire-progress-label>
                        {{ __('hermes.questionnaire.step_of', ['current' => $initialStepIndex + 1, 'total' => max($categories->count(), 1)]) }}
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
                    data-questionnaire-form
                    data-step-total="{{ max($categories->count(), 1) }}"
                    data-initial-step="{{ $initialStepIndex }}"
                >
                    @csrf

                    @foreach ($categories as $categoryIndex => $category)
                        <section
                            class="step"
                            data-questionnaire-step
                            data-step-index="{{ $categoryIndex }}"
                            @if ($categoryIndex !== $initialStepIndex) hidden @endif
                        >
                            <div class="step__header">
                                <span class="step__counter">{{ __('hermes.questionnaire.step_of', ['current' => $categoryIndex + 1, 'total' => $categories->count()]) }}</span>
                                <h2>{{ $category->title }}</h2>

                                @if ($category->description)
                                    <p class="muted">{{ $category->description }}</p>
                                @endif
                            </div>

                            <div class="question-list">
                                @foreach ($category->questions as $question)
                                    @php($answer = old("answers.{$question->id}", $existingAnswers[$question->id] ?? null))
                                    <div
                                        class="question"
                                        data-question
                                        data-required="{{ $question->is_required ? 'true' : 'false' }}"
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
                        <div class="actions__group">
                            <button type="button" class="ghost-pill" data-previous-step>{{ __('hermes.questionnaire.previous_step') }}</button>
                            <button type="button" class="questionnaire-pill" data-next-step>{{ __('hermes.questionnaire.next_step') }}</button>
                            <button type="submit" class="questionnaire-pill" data-submit-step>{{ __('hermes.questionnaire.submit') }}</button>
                        </div>

                        <a href="{{ route('dashboard') }}" class="ghost-pill">{{ __('hermes.questionnaire.back_to_dashboard') }}</a>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <x-hermes-footer />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('[data-questionnaire-form]');

            if (! form) {
                return;
            }

            const steps = Array.from(form.querySelectorAll('[data-questionnaire-step]'));
            const progressLabel = document.querySelector('[data-questionnaire-progress-label]');
            const progressPills = Array.from(document.querySelectorAll('[data-progress-pill]'));
            const previousButton = form.querySelector('[data-previous-step]');
            const nextButton = form.querySelector('[data-next-step]');
            const submitButton = form.querySelector('[data-submit-step]');
            const totalSteps = Number(form.dataset.stepTotal || steps.length || 1);
            let currentStepIndex = Number(form.dataset.initialStep || 0);

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

            const validateStep = (step) => {
                const questions = Array.from(step.querySelectorAll('[data-question]'));
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
                steps.forEach((step, index) => {
                    step.hidden = index !== currentStepIndex;
                });

                progressPills.forEach((pill, index) => {
                    pill.classList.toggle('is-active', index === currentStepIndex);
                    pill.classList.toggle('is-complete', index < currentStepIndex);
                });

                if (progressLabel) {
                    progressLabel.textContent = @json(__('hermes.questionnaire.step_of', ['current' => '__CURRENT__', 'total' => '__TOTAL__']))
                        .replace('__CURRENT__', String(currentStepIndex + 1))
                        .replace('__TOTAL__', String(totalSteps));
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
            };

            previousButton?.addEventListener('click', () => {
                if (currentStepIndex === 0) {
                    return;
                }

                currentStepIndex -= 1;
                renderStep();
            });

            nextButton?.addEventListener('click', () => {
                const currentStep = steps[currentStepIndex];

                if (! currentStep || ! validateStep(currentStep)) {
                    return;
                }

                currentStepIndex += 1;
                renderStep();
            });

            renderStep();
        });
    </script>
</body>
</html>
