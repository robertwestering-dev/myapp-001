<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('hermes.academy.weekly_intention_widget.title') }}</title>
    <style>
        :root {
            color-scheme: light;
            --forest-deep: #143730;
            --forest: #245346;
            --paper: rgba(255, 252, 246, 0.96);
            --ink: #17211d;
            --muted: #5f6b66;
            --line: rgba(23, 33, 29, 0.12);
            --accent: #bc5b27;
            --accent-soft: rgba(188, 91, 39, 0.12);
            --success: #2f7d4a;
            --success-soft: rgba(47, 125, 74, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(36, 83, 70, 0.16), transparent 34%),
                linear-gradient(135deg, #fcf8f1 0%, #f2e7d7 100%);
            color: var(--ink);
            font-family: Georgia, "Times New Roman", serif;
        }

        .widget {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 12px;
        }

        .widget-card {
            display: grid;
            gap: 14px;
            padding: 16px;
            border: 1px solid rgba(23, 33, 29, 0.08);
            border-radius: 24px;
            background: var(--paper);
            box-shadow: 0 18px 34px rgba(23, 33, 29, 0.08);
        }

        .widget-heading {
            display: grid;
            gap: 6px;
        }

        .widget-heading h1,
        .widget-heading p,
        .widget-status,
        .widget-errors,
        .widget-field label,
        .widget-field span {
            margin: 0;
        }

        .widget-eyebrow,
        .widget-status,
        .widget-errors,
        .widget-field label,
        .widget-field span,
        .widget-button,
        .widget-field select,
        .widget-field textarea,
        .widget-field input {
            font-family: Arial, Helvetica, sans-serif;
        }

        .widget-eyebrow {
            color: var(--forest);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .widget-heading p,
        .widget-field span {
            color: var(--muted);
            font-size: 0.82rem;
            line-height: 1.38;
        }

        .widget-field--date span {
            white-space: nowrap;
        }

        .widget-status,
        .widget-errors {
            padding: 10px 12px;
            border-radius: 14px;
            font-size: 0.8rem;
            line-height: 1.35;
        }

        .widget-status {
            color: var(--success);
            background: var(--success-soft);
        }

        .widget-errors {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .widget-errors strong {
            display: block;
            margin-bottom: 4px;
        }

        .widget-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .widget-form {
            display: grid;
            gap: 14px;
        }

        .widget-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: auto auto minmax(0, 1fr);
            align-items: start;
        }

        .widget-field {
            display: grid;
            gap: 8px;
            align-content: start;
        }

        .widget-field--date {
            width: 11.75rem;
        }

        .widget-field--strength {
            width: 13.5rem;
        }

        .widget-field label {
            display: flex;
            align-items: flex-start;
            min-height: 2.4rem;
            color: var(--ink);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .widget-field input,
        .widget-field select,
        .widget-field textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
            color: var(--ink);
            padding: 12px 14px;
            font: inherit;
            line-height: 1.4;
        }

        .widget-field input,
        .widget-field select {
            height: 52px;
            min-height: 52px;
        }

        .widget-field textarea {
            min-height: 120px;
            resize: none;
        }

        .widget-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .widget-button {
            appearance: none;
            border: 0;
            border-radius: 999px;
            padding: 11px 18px;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-deep) 100%);
            color: #fff;
            font-size: 0.86rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(20, 55, 48, 0.18);
        }

        .widget-button:hover {
            filter: brightness(1.04);
        }

        @media (max-width: 980px) {
            .widget-grid {
                grid-template-columns: auto 1fr;
            }
        }

        @media (max-width: 860px) {
            .widget {
                padding: 10px;
            }

            .widget-card {
                padding: 14px;
                border-radius: 18px;
            }

            .widget-grid {
                grid-template-columns: 1fr;
            }

            .widget-field textarea {
                min-height: 90px;
            }
        }
    </style>
</head>
<body>
    <main class="widget" id="academy-weekly-intention-widget">
        <section class="widget-card">
            <header class="widget-heading">
                <p class="widget-eyebrow">{{ __('hermes.academy.weekly_intention_widget.eyebrow') }}</p>
            </header>

            @if (session('status'))
                <div class="widget-status" role="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="widget-errors" role="alert">
                    <strong>{{ __('hermes.academy.weekly_intention_widget.validation_summary') }}</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('academy.widgets.weekly-intention.store') }}" class="widget-form">
                @csrf
                <input type="hidden" name="entry_type" value="{{ \App\Models\JournalEntry::TYPE_WEEKLY_INTENTION }}">
                <input type="hidden" name="content[general_intention]" value="{{ old('content.general_intention', $entry?->contentValue('general_intention')) }}">

                <div class="widget-grid">
                    <div class="widget-field widget-field--date">
                        <label for="entry_date">{{ __('hermes.journal.fields.entry_date') }}</label>
                        <input
                            id="entry_date"
                            type="date"
                            name="entry_date"
                            max="{{ now()->toDateString() }}"
                            value="{{ old('entry_date', $entry?->entry_date?->toDateString() ?? $entryDate) }}"
                            required
                        >
                    </div>

                    <div class="widget-field widget-field--strength">
                        <label for="strength_key">{{ __('hermes.journal.types.weekly_intention.fields.strength_key') }}</label>
                        <select id="strength_key" name="content[strength_key]" required>
                            <option value="">{{ __('hermes.settings.profile.fields.choose_option') }}</option>
                            @foreach ($strengthOptions as $option)
                                <option value="{{ $option['key'] }}" @selected(old('content.strength_key', $entry?->contentValue('strength_key')) === $option['key'])>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="widget-field">
                        <label for="planned_strength_use">{{ __('hermes.journal.types.weekly_intention.fields.planned_strength_use') }}</label>
                        <textarea
                            id="planned_strength_use"
                            name="content[planned_strength_use]"
                            maxlength="255"
                            placeholder="{{ __('hermes.academy.weekly_intention_widget.placeholders.planned_strength_use') }}"
                            required
                        >{{ old('content.planned_strength_use', $entry?->contentValue('planned_strength_use')) }}</textarea>
                    </div>
                </div>

                <div class="widget-actions">
                    <button type="submit" class="widget-button">{{ __('hermes.academy.weekly_intention_widget.submit') }}</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
