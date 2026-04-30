<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('hermes.academy.strengths_widget.title') }}</title>
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
            --accent-soft: rgba(190, 91, 39, 0.14);
            --success: #2f7d4a;
            --success-soft: rgba(47, 125, 74, 0.14);
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
            max-width: 1120px;
            margin: 0 auto;
            padding: 12px;
        }

        .widget-card {
            display: grid;
            gap: 12px;
            padding: 14px;
            border: 1px solid rgba(24, 33, 29, 0.08);
            border-radius: 20px;
            background: rgba(255, 253, 248, 0.94);
            box-shadow: 0 16px 32px rgba(24, 33, 29, 0.07);
        }

        .widget-eyebrow {
            margin: 0;
            color: var(--forest);
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-heading {
            display: grid;
            gap: 6px;
            padding-bottom: 12px;
        }

        .widget-heading__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .widget-heading h1,
        .widget-heading p {
            margin: 0;
        }

        .widget-heading h1 {
            font-size: clamp(1.12rem, 1.5vw, 1.42rem);
            line-height: 1.04;
            color: var(--forest-deep);
        }

        .widget-heading p,
        .widget-helper,
        .widget-counter {
            color: var(--muted);
            font-size: 0.8rem;
            line-height: 1.32;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-status,
        .widget-errors {
            padding: 9px 11px;
            border-radius: 14px;
            font-size: 0.78rem;
            line-height: 1.3;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-status {
            color: var(--success);
            background: var(--success-soft);
        }

        .widget-errors {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .widget-errors p {
            margin: 0 0 4px;
            font-weight: 700;
        }

        .widget-errors ul {
            margin: 0;
            padding-left: 16px;
        }

        .widget-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .widget-counter strong {
            color: var(--forest-deep);
        }

        .widget-grid {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .widget-option {
            position: relative;
        }

        .widget-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .widget-option label {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 46px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid rgba(24, 33, 29, 0.1);
            background: #fff;
            cursor: pointer;
            transition: border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .widget-option label:hover {
            transform: translateY(-1px);
            border-color: rgba(36, 83, 70, 0.22);
            box-shadow: 0 8px 16px rgba(24, 33, 29, 0.05);
        }

        .widget-option__marker {
            display: inline-flex;
            width: 18px;
            height: 18px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(24, 33, 29, 0.16);
            background: var(--paper);
            color: transparent;
            font-size: 0.72rem;
            font-weight: 700;
            font-family: Arial, Helvetica, sans-serif;
            flex: 0 0 18px;
        }

        .widget-option__label {
            color: var(--ink);
            font-size: 0.84rem;
            line-height: 1.12;
            font-weight: 700;
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-option input:checked + label {
            border-color: rgba(36, 83, 70, 0.3);
            background: linear-gradient(180deg, rgba(36, 83, 70, 0.08), rgba(255, 253, 248, 0.96));
            box-shadow: inset 0 0 0 1px rgba(36, 83, 70, 0.08);
        }

        .widget-option input:checked + label .widget-option__marker {
            border-color: var(--forest);
            background: var(--forest);
            color: #fff;
        }

        .widget-option input:disabled + label {
            opacity: 0.52;
            cursor: not-allowed;
            background: rgba(246, 241, 232, 0.78);
            box-shadow: none;
            transform: none;
        }

        .widget-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .widget-button {
            appearance: none;
            border: 0;
            border-radius: 999px;
            padding: 10px 15px;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-deep) 100%);
            color: #fff;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            font-family: Arial, Helvetica, sans-serif;
            box-shadow: 0 10px 20px rgba(23, 60, 52, 0.16);
        }

        .widget-button:hover {
            filter: brightness(1.03);
        }

        @media (max-width: 980px) {
            .widget-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .widget-heading__top {
                flex-direction: column;
                align-items: stretch;
            }

            .widget {
                padding: 10px;
            }

            .widget-card {
                padding: 12px;
                border-radius: 16px;
            }

            .widget-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .widget-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $selectedStrengths = old('selected_strengths', $selectedStrengths ?? []);
        $selectedStrengths = is_array($selectedStrengths) ? array_values(array_map('strval', $selectedStrengths)) : [];
        $selectedCount = count($selectedStrengths);
        $errorMessages = collect($errors->get('selected_strengths'))
            ->merge($errors->get('selected_strengths.*'))
            ->flatten()
            ->unique()
            ->values();
    @endphp
    <main class="widget">
        <section class="widget-card academy-strengths-widget">
            <form method="POST" action="{{ route('academy.widgets.strengths.store') }}" data-strength-form data-max-selections="3">
                @csrf

                <div class="widget-heading">
                    <div class="widget-heading__top">
                        <div>
                            <p class="widget-eyebrow">{{ __('hermes.academy.strengths_widget.eyebrow') }}</p>
                            <h1>{{ __('hermes.academy.strengths_widget.title') }}</h1>
                        </div>
                        <button class="widget-button" type="submit">{{ __('hermes.academy.strengths_widget.submit') }}</button>
                    </div>
                    <p class="widget-heading__intro">{{ __('hermes.academy.strengths_widget.intro') }}</p>
                </div>

                @if (session('status'))
                    <div class="widget-status">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errorMessages->isNotEmpty())
                    <div class="widget-errors" role="alert">
                        <p>{{ __('hermes.academy.strengths_widget.validation_summary') }}</p>
                        <ul>
                            @foreach ($errorMessages as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="widget-toolbar">
                    <div class="widget-counter" data-counter-template="{{ __('hermes.academy.strengths_widget.counter', ['count' => '__COUNT__', 'max' => 3]) }}">
                        <strong data-selection-counter>
                            {{ __('hermes.academy.strengths_widget.counter', ['count' => $selectedCount, 'max' => 3]) }}
                        </strong>
                    </div>
                    <div class="widget-helper">{{ __('hermes.academy.strengths_widget.helper') }}</div>
                </div>

                <div class="widget-grid">
                    @foreach ($strengthOptions as $strength)
                        @php($isChecked = in_array($strength['key'], $selectedStrengths, true))
                        <div class="widget-option">
                            <input
                                id="strength-{{ $strength['key'] }}"
                                type="checkbox"
                                name="selected_strengths[]"
                                value="{{ $strength['key'] }}"
                                {{ $isChecked ? 'checked' : '' }}
                            >
                            <label for="strength-{{ $strength['key'] }}">
                                <span class="widget-option__marker">✓</span>
                                <span class="widget-option__label">{{ $strength['label'] }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </form>
        </section>
    </main>

    <script nonce="{{ Vite::cspNonce() }}">
        const form = document.querySelector('[data-strength-form]');

        if (form) {
            const maxSelections = Number(form.getAttribute('data-max-selections') || '3');
            const checkboxes = Array.from(form.querySelectorAll('input[type="checkbox"]'));
            const counter = form.querySelector('[data-selection-counter]');
            const counterTemplate = counter?.parentElement?.getAttribute('data-counter-template') ?? '__COUNT__ / ' + maxSelections;

            const updateSelectionState = () => {
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

                if (counter) {
                    counter.textContent = counterTemplate.replace('__COUNT__', String(checkedCount));
                }

                checkboxes.forEach((checkbox) => {
                    checkbox.disabled = checkedCount >= maxSelections && !checkbox.checked;
                });
            };

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateSelectionState);
            });

            updateSelectionState();
        }
    </script>
</body>
</html>
