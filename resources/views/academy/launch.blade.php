<x-layouts.hermes-dashboard :title="$course->titleForLocale()">
    <x-slot:head>
        <style>
            .academy-launch {
                display: grid;
                gap: 18px;
            }

            .academy-launch__bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
            }

            .academy-launch__frame {
                width: 100%;
                min-height: min(760px, calc(100vh - 190px));
                border: 1px solid rgba(22, 33, 29, 0.12);
                border-radius: 8px;
                background: #fff;
            }
        </style>
    </x-slot:head>

    <section
        class="academy-launch"
        data-completion-url="{{ route('academy.courses.complete', $course->slug) }}"
        data-csrf-token="{{ csrf_token() }}"
    >
        <div class="academy-launch__bar">
            <x-user-section-heading
                :eyebrow="__('hermes.academy.course_label')"
                :title="$course->titleForLocale()"
            />

            <a href="{{ route('academy.index') }}" class="pill pill--neutral">
                {{ __('hermes.academy.back_to_academy') }}
            </a>
        </div>

        <iframe
            class="academy-launch__frame"
            src="{{ $courseContentUrl }}"
            title="{{ $course->titleForLocale() }}"
            allowfullscreen
        ></iframe>
    </section>

    <script nonce="{{ Vite::cspNonce() }}">
        (() => {
            const launcher = document.querySelector('[data-completion-url]');

            if (! launcher) {
                return;
            }

            let completionSent = false;

            const markCompleted = (eventName) => {
                if (completionSent) {
                    return;
                }

                completionSent = true;

                fetch(launcher.dataset.completionUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': launcher.dataset.csrfToken,
                    },
                    body: JSON.stringify({ event: eventName || 'academy-course-completed' }),
                }).catch(() => {
                    completionSent = false;
                });
            };

            window.addEventListener('message', (event) => {
                if (event.origin !== window.location.origin) {
                    return;
                }

                const message = event.data;
                const messageType = typeof message === 'string' ? message : message?.type;

                if (['academy-course-completed', 'ispring-course-completed'].includes(messageType)) {
                    markCompleted(messageType);
                }
            });
        })();
    </script>
</x-layouts.hermes-dashboard>
