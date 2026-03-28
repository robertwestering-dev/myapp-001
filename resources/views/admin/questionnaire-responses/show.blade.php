<x-layouts.hermes-admin
    title="Questionnaire response"
    eyebrow="Responses"
    heading="Response van {{ $response->user->name }}"
    :lead="'Questionnaire: '.$response->organizationQuestionnaire->questionnaire->title.' · Organisatie: '.$response->organizationQuestionnaire->organization->naam"
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
            <strong>Gebruiker</strong>
            <div>{{ $response->user->name }} · {{ $response->user->email }}</div>
            <div class="muted">Ingezonden op {{ $response->submitted_at?->format('d-m-Y H:i') ?? 'onbekend' }}</div>
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
