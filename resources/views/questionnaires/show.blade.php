<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $organizationQuestionnaire->questionnaire->title }}</title>
    <x-favicon-links />
    <style>
        :root {
            --bg: #f4efe6;
            --paper: rgba(255, 255, 255, 0.78);
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --forest: #20453a;
            --forest-soft: #2f5f52;
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
            z-index: 10;
            backdrop-filter: blur(16px);
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        .topbar__inner,
        .site-footer__inner,
        .hero {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80px;
            gap: 16px;
        }

        .topbar__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 60px;
            max-width: 100%;
            border-radius: 12px;
        }

        .pill,
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
        }

        .pill {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            border-color: transparent;
        }

        .pill--booking {
            background: linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97));
            color: #fff;
            border-color: transparent;
        }

        main {
            flex: 1;
            padding: 34px 0 60px;
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

        .hero {
            display: grid;
            grid-template-columns: 1.12fr 0.88fr;
            gap: 28px;
            align-items: start;
        }

        .hero__panel,
        .hero__side {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .hero__panel {
            border-radius: var(--radius-xl);
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero__panel::after {
            content: "";
            position: absolute;
            inset: auto -80px -120px auto;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 106, 43, 0.2), transparent 70%);
        }

        .hero__side {
            border-radius: var(--radius-xl);
            padding: 28px;
            background:
                linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                var(--forest);
            color: #f6f2eb;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1 {
            font-size: clamp(1.45rem, 3vw, 2.65rem);
            line-height: 1.08;
            margin: 22px 0 20px;
            max-width: 62ch;
        }

        .lead {
            max-width: 62ch;
            font-size: 1.08rem;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 28px;
        }

        form {
            display: grid;
            gap: 26px;
            margin-top: 28px;
        }

        .category {
            display: grid;
            gap: 18px;
            padding: 24px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.52);
        }

        .question {
            display: grid;
            gap: 10px;
            padding-top: 18px;
        }

        .question + .question {
            border-top: 1px solid rgba(22, 33, 29, 0.12);
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
            background: rgba(255, 255, 255, 0.9);
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

        .error {
            color: var(--accent-deep);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
        }

        .status {
            margin-bottom: 18px;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .summary-list {
            display: grid;
            gap: 14px;
            margin-top: 22px;
        }

        .summary-item {
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .summary-item strong,
        .summary-item span {
            display: block;
        }

        .summary-item strong {
            font-size: 1.05rem;
            margin-bottom: 6px;
        }

        .summary-item span {
            color: rgba(246, 242, 235, 0.82);
            font-family: Arial, Helvetica, sans-serif;
        }

        @media (max-width: 920px) {
            .hero {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .brand__logo {
                height: 60px;
            }

            .hero__panel,
            .hero__side {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header :show-booking="false" />

    <main>
        <div class="hero">
            <section class="hero__panel">
                <span class="eyebrow">Questionnaire</span>
                <h1>{{ $organizationQuestionnaire->questionnaire->title }}</h1>
                <p class="lead">{{ $organizationQuestionnaire->questionnaire->description }}</p>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                @if ($response?->submitted_at)
                    <p class="muted">Laatst opgeslagen op {{ $response->submitted_at->format('d-m-Y H:i') }}.</p>
                @endif

                @if ($errors->any())
                    <div class="error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('questionnaire-responses.store', $organizationQuestionnaire) }}">
                    @csrf

                    @foreach ($organizationQuestionnaire->questionnaire->categories as $category)
                        <section class="category">
                            <div>
                                <h2>{{ $category->title }}</h2>
                                @if ($category->description)
                                    <p class="muted">{{ $category->description }}</p>
                                @endif
                            </div>

                            @foreach ($category->questions as $question)
                                @php($answer = old("answers.{$question->id}", $existingAnswers[$question->id] ?? null))
                                <div class="question">
                                    <label for="question-{{ $question->id }}">
                                        {{ $question->prompt }}@if ($question->is_required) * @endif
                                    </label>

                                    @if ($question->help_text)
                                        <div class="muted">{{ $question->help_text }}</div>
                                    @endif

                                    @if ($question->type === \App\Models\QuestionnaireQuestion::TYPE_SHORT_TEXT)
                                        <input id="question-{{ $question->id }}" type="text" name="answers[{{ $question->id }}]" value="{{ is_array($answer) ? '' : $answer }}">
                                    @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_LONG_TEXT)
                                        <textarea id="question-{{ $question->id }}" name="answers[{{ $question->id }}]">{{ is_array($answer) ? '' : $answer }}</textarea>
                                    @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_NUMBER)
                                        <input id="question-{{ $question->id }}" type="number" step="any" name="answers[{{ $question->id }}]" value="{{ is_array($answer) ? '' : $answer }}">
                                    @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_DATE)
                                        <input id="question-{{ $question->id }}" type="date" name="answers[{{ $question->id }}]" value="{{ is_array($answer) ? '' : $answer }}">
                                    @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_BOOLEAN)
                                        <select id="question-{{ $question->id }}" name="answers[{{ $question->id }}]">
                                            <option value="">Maak een keuze</option>
                                            <option value="1" @selected((string) $answer === '1')>Ja</option>
                                            <option value="0" @selected((string) $answer === '0')>Nee</option>
                                        </select>
                                    @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_SINGLE_CHOICE)
                                        <fieldset>
                                            <legend class="muted">Kies een antwoord</legend>
                                            @foreach ($question->options ?? [] as $option)
                                                <label class="option">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" @checked($answer === $option)>
                                                    <span>{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </fieldset>
                                    @elseif ($question->type === \App\Models\QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE)
                                        <fieldset>
                                            <legend class="muted">Kies een of meer antwoorden</legend>
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
                                </div>
                            @endforeach
                        </section>
                    @endforeach

                    <div class="actions">
                        <button type="submit" class="pill">Antwoorden opslaan</button>
                        <a href="{{ route('dashboard') }}" class="ghost-pill">Terug naar dashboard</a>
                    </div>
                </form>
            </section>

            <aside class="hero__side">
                <h2>Invulinstructie</h2>

                <div class="summary-list">
                    <div class="summary-item">
                        <strong>Organisatie</strong>
                        <span>{{ $organizationQuestionnaire->organization->naam ?? 'Niet gekoppeld' }}</span>
                    </div>

                    <div class="summary-item">
                        <strong>Categorieen</strong>
                        <span>{{ $organizationQuestionnaire->questionnaire->categories->count() }} onderdelen in deze questionnaire</span>
                    </div>

                    <div class="summary-item">
                        <strong>Opslaan</strong>
                        <span>Uw antwoorden worden bewaard zodra u het formulier indient en zijn later opnieuw te openen.</span>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <x-hermes-footer />
</body>
</html>
