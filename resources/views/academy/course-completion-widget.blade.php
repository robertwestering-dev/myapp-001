<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('hermes.academy.course_completion_widget.title') }}</title>
    <style nonce="{{ Vite::cspNonce() }}">
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: transparent;
            color: #18231f;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: transparent;
        }

        .completion-widget {
            width: 100%;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 18px;
        }

        .completion-widget__panel {
            width: min(100%, 560px);
            border: 1px solid rgba(24, 35, 31, 0.14);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(24, 35, 31, 0.08);
            padding: 20px;
        }

        .completion-widget__eyebrow {
            margin: 0 0 8px;
            color: #587066;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .completion-widget__title {
            margin: 0;
            color: #18231f;
            font-size: clamp(1.35rem, 4vw, 2rem);
            line-height: 1.1;
            font-weight: 850;
            letter-spacing: 0;
        }

        .completion-widget__text {
            margin: 12px 0 0;
            color: #4f5f58;
            font-size: 1rem;
            line-height: 1.55;
        }

        .completion-widget__actions {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .completion-widget__button {
            min-height: 46px;
            border: 0;
            border-radius: 8px;
            background: #0f6b57;
            color: #ffffff;
            cursor: pointer;
            font-weight: 800;
            padding: 0 18px;
        }

        .completion-widget__button:hover {
            background: #0b5948;
        }

        .completion-widget__status {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            border-radius: 999px;
            background: #e9f7ef;
            color: #17613f;
            font-size: 0.9rem;
            font-weight: 800;
            padding: 0 12px;
        }
    </style>
</head>
<body>
    <main class="completion-widget">
        <section class="completion-widget__panel academy-course-completion-widget">
            <p class="completion-widget__eyebrow">{{ __('hermes.academy.course_completion_widget.eyebrow') }}</p>
            <h1 class="completion-widget__title">{{ __('hermes.academy.course_completion_widget.title') }}</h1>
            <p class="completion-widget__text">
                {{ $isCompleted
                    ? __('hermes.academy.course_completion_widget.completed_text')
                    : __('hermes.academy.course_completion_widget.intro', ['course' => $course->titleForLocale()]) }}
            </p>

            <div class="completion-widget__actions">
                @if ($isCompleted)
                    <span class="completion-widget__status">{{ __('hermes.academy.course_completion_widget.completed_badge') }}</span>
                @else
                    <form method="POST" action="{{ route('academy.widgets.course-completion.store', $course->slug) }}">
                        @csrf
                        <button class="completion-widget__button" type="submit">
                            {{ __('hermes.academy.course_completion_widget.submit') }}
                        </button>
                    </form>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
