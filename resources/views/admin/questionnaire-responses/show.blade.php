<x-layouts.hermes-admin
    :title="__('hermes.reports.show_title')"
    :eyebrow="__('hermes.reports.eyebrow')"
    :heading="__('hermes.reports.show_heading', ['name' => $response->user->name])"
    :lead="__('hermes.reports.show_lead', ['questionnaire' => $response->organizationQuestionnaire->questionnaire->title, 'organization' => $response->organizationQuestionnaire->organization->naam])"
    menu-active="questionnaire-responses"
>
    <style>
        .response-card,
        .answer-card {
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }

        .response-list {
            display: grid;
            gap: 18px;
            margin-top: 24px;
        }

        .answer-card {
            display: grid;
            gap: 10px;
        }

        .muted {
            color: var(--muted);
        }

        .answer-value {
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(32, 69, 58, 0.06);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }
    </style>

    <section class="content-panel">
        <div class="response-card">
            <strong>{{ __('hermes.reports.show_user') }}</strong>
            <div>{{ $response->user->name }} · {{ $response->user->email }}</div>
            <div class="muted">{{ __('hermes.reports.show_submitted_at', ['datetime' => $response->submitted_at?->format('d-m-Y H:i') ?? __('hermes.reports.submitted_at_unknown')]) }}</div>
        </div>

        <div class="response-list">
            @foreach ($response->answers as $answer)
                <article class="answer-card">
                    <div class="muted">{{ $answer->question->category->title }}</div>
                    <strong>{{ $answer->question->prompt }}</strong>
                    <div class="answer-value">
                        {{ $answer->answer ?? implode(', ', $answer->answer_list ?? []) }}
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.hermes-admin>
