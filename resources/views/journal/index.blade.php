<x-layouts.hermes-dashboard :title="__('hermes.journal.title')">
    <x-slot:head>
        <style>
            .journal-page,
            .journal-stack,
            .journal-form,
            .journal-entry,
            .journal-fields,
            .journal-hero,
            .journal-log,
            .journal-type-grid,
            .journal-guided-grid {
                display: grid;
                gap: 24px;
            }

            .journal-page {
                gap: 30px;
            }

            .journal-card {
                padding: 32px;
                border-radius: 30px;
            }

            .journal-card h2,
            .journal-card h3,
            .journal-card p {
                margin: 0;
            }

            .journal-hero {
                align-items: start;
                background:
                    radial-gradient(circle at top left, rgba(217, 106, 43, 0.18), transparent 35%),
                    radial-gradient(circle at bottom right, rgba(32, 69, 58, 0.22), transparent 42%),
                    linear-gradient(145deg, #fcf7ef 0%, #f1e5d5 100%);
            }

            .journal-hero__copy,
            .journal-guided-card {
                display: grid;
                gap: 16px;
            }

            .journal-guided-card h3,
            .journal-log-summary strong,
            .journal-empty h2 {
                font-family: "Iowan Old Style", "Palatino Linotype", Georgia, serif;
                color: #17231f;
            }

            .journal-hero__message,
            .journal-guided-card p,
            .journal-helper,
            .journal-empty p,
            .journal-log-summary,
            .journal-field label,
            .journal-entry__meta,
            .journal-entry__body p {
                font-family: "Avenir Next", "Segoe UI", sans-serif;
                line-height: 1.7;
                color: #55635e;
            }

            .journal-hero__message {
                max-width: none;
                font-size: 1.04rem;
                color: #33413d;
            }

            .journal-card > .user-section-heading > p {
                max-width: none;
            }

            .journal-quick-links,
            .journal-log-actions,
            .journal-entry__meta,
            .journal-delete-dialog__actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
            }

            .journal-quick-links .pill {
                justify-content: center;
            }

            .journal-pill--sand {
                background: linear-gradient(135deg, #f4e4cc 0%, #e7cfab 100%);
                color: #21302b;
                border-color: rgba(33, 48, 43, 0.14);
            }

            .journal-pill--orange {
                background: linear-gradient(135deg, #d96a2b 0%, #b54d17 100%);
                color: #fff8f2;
                border-color: rgba(181, 77, 23, 0.28);
            }

            .journal-pill--gray {
                background: linear-gradient(135deg, #e3e7e4 0%, #cfd7d3 100%);
                color: #21302b;
                border-color: rgba(33, 48, 43, 0.14);
            }

            .journal-pill--green {
                background: linear-gradient(135deg, #20453a 0%, #162d26 100%);
                color: #f4efe6;
                border-color: rgba(22, 45, 38, 0.28);
            }

            .journal-guided-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
            }

            .journal-guided-card {
                padding: 22px;
                border-radius: 24px;
                border: 1px solid rgba(22, 33, 29, 0.08);
                background: rgba(255, 255, 255, 0.66);
            }

            .journal-guided-card--sand {
                background: linear-gradient(180deg, rgba(252, 246, 236, 0.95), rgba(244, 235, 219, 0.92));
            }

            .journal-guided-card--orange {
                background: linear-gradient(180deg, rgba(251, 237, 226, 0.95), rgba(246, 224, 207, 0.92));
            }

            .journal-guided-card--gray {
                background: linear-gradient(180deg, rgba(239, 243, 241, 0.95), rgba(228, 235, 232, 0.92));
            }

            .journal-guided-card--green {
                background: linear-gradient(180deg, rgba(230, 237, 234, 0.95), rgba(220, 229, 225, 0.92));
            }

            .journal-guided-card__eyebrow,
            .journal-entry__pill,
            .journal-log-date {
                font-family: "Avenir Next", "Segoe UI", sans-serif;
                font-size: 0.82rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .journal-guided-card__eyebrow {
                color: #7b4b28;
            }

            .journal-type-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .journal-entry-form-card {
                display: none;
            }

            .journal-entry-form-card:target,
            .journal-entry-form-card.is-active {
                display: block;
            }

            .journal-field {
                display: grid;
                gap: 8px;
            }

            .journal-field label {
                font-weight: 700;
                color: #16211d;
            }

            .journal-field input,
            .journal-field select,
            .journal-field textarea {
                width: 100%;
                border: 1px solid rgba(22, 33, 29, 0.14);
                border-radius: 18px;
                padding: 14px 16px;
                background: rgba(255, 255, 255, 0.92);
                color: #16211d;
                font: inherit;
                font-family: "Avenir Next", "Segoe UI", sans-serif;
                line-height: 1.5;
            }

            .journal-field textarea {
                min-height: 108px;
                resize: vertical;
            }

            .journal-log-list {
                display: grid;
                gap: 14px;
                margin-top: 18px;
            }

            .journal-log-item {
                display: grid;
                gap: 16px;
                padding: 18px 20px;
                border-radius: 24px;
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(22, 33, 29, 0.08);
            }

            .journal-log-top {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 12px;
                align-items: center;
                padding-bottom: 14px;
                border-bottom: 1px solid rgba(22, 33, 29, 0.08);
            }

            .journal-log-date {
                color: #5a6762;
            }

            .journal-log-summary {
                display: grid;
                gap: 8px;
            }

            .journal-entry__pill {
                display: inline-flex;
                align-items: center;
                padding: 6px 12px;
                border-radius: 999px;
                background: rgba(32, 69, 58, 0.1);
                color: #20453a;
            }

            .journal-edit-toggle,
            .journal-delete-toggle {
                position: absolute;
                opacity: 0;
                pointer-events: none;
            }

            .journal-icon-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                padding: 0;
                border: 1px solid rgba(22, 33, 29, 0.12);
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.92);
                color: #20453a;
                cursor: pointer;
            }

            .journal-icon-button__symbol {
                font-size: 1.3rem;
                font-weight: 700;
                line-height: 1;
            }

            .journal-icon-button__symbol--collapse,
            .journal-entry {
                display: none;
            }

            .journal-log-item:has(.journal-edit-toggle:checked) .journal-entry {
                display: grid;
            }

            .journal-log-item:has(.journal-edit-toggle:checked) .journal-icon-button__symbol--expand {
                display: none;
            }

            .journal-log-item:has(.journal-edit-toggle:checked) .journal-icon-button__symbol--collapse {
                display: inline;
            }

            .journal-entry__body strong {
                display: block;
                margin-bottom: 4px;
                color: #16211d;
            }

            .journal-delete-dialog {
                position: fixed;
                inset: 0;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 20px;
                z-index: 80;
            }

            .journal-log-item:has(.journal-delete-toggle:checked) .journal-delete-dialog {
                display: flex;
            }

            .journal-delete-dialog__backdrop {
                position: absolute;
                inset: 0;
                background: rgba(22, 33, 29, 0.35);
            }

            .journal-delete-dialog__card {
                position: relative;
                display: grid;
                gap: 18px;
                width: min(100%, 28rem);
                padding: 28px;
                border-radius: 24px;
                background: #fffdf8;
                border: 1px solid rgba(22, 33, 29, 0.08);
                box-shadow: 0 24px 60px rgba(22, 33, 29, 0.18);
            }

            .journal-section-anchor {
                scroll-margin-top: 110px;
            }

            @media (max-width: 1080px) {
                .journal-type-grid,
                .journal-guided-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 760px) {
                .journal-log-top {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <div class="journal-page">
        @php
            $activeEntryType = $errors->any() ? old('entry_type') : null;
            $journalEntryFormClass = 'user-surface-card user-surface-card--default journal-card journal-section-anchor journal-entry-form-card';
            $dailyNoteFormClass = $journalEntryFormClass.($activeEntryType === \App\Models\JournalEntry::TYPE_DAILY_NOTE ? ' is-active' : '');
            $threeGoodThingsFormClass = $journalEntryFormClass.($activeEntryType === \App\Models\JournalEntry::TYPE_THREE_GOOD_THINGS ? ' is-active' : '');
            $strengthsReflectionFormClass = $journalEntryFormClass.($activeEntryType === \App\Models\JournalEntry::TYPE_STRENGTHS_REFLECTION ? ' is-active' : '');
            $weeklyIntentionFormClass = $journalEntryFormClass.($activeEntryType === \App\Models\JournalEntry::TYPE_WEEKLY_INTENTION ? ' is-active' : '');
        @endphp

        @if (session('status'))
            <div class="user-feedback user-feedback--status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="user-feedback user-feedback--errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <x-user-surface-card class="journal-card journal-hero">
            <div class="journal-hero__copy">
                <x-user-section-heading :eyebrow="__('hermes.journal.eyebrow')" />
                <p class="journal-hero__message">{{ __('hermes.journal.hero_message') }}</p>

                <div class="journal-quick-links">
                    <a href="{{ route('journal.timeline') }}" class="pill pill--neutral">{{ __('hermes.journal.timeline_action') }}</a>
                </div>
            </div>
        </x-user-surface-card>

        <x-user-surface-card class="journal-card">
            <x-user-section-heading
                :eyebrow="__('hermes.journal.compose_eyebrow')"
                :title="__('hermes.journal.compose_title')"
                :text="__('hermes.journal.compose_text')"
            />

            <div class="journal-guided-grid">
                <div class="journal-guided-card journal-guided-card--sand">
                    <span class="journal-guided-card__eyebrow">{{ __('hermes.journal.types.daily_note.eyebrow') }}</span>
                    <h3>{{ __('hermes.journal.types.daily_note.title') }}</h3>
                    <p>{{ __('hermes.journal.types.daily_note.text') }}</p>
                    <a href="#journal-daily-note-form" class="pill journal-pill--sand">{{ __('hermes.journal.actions.daily_note') }}</a>
                </div>

                <div class="journal-guided-card journal-guided-card--orange">
                    <span class="journal-guided-card__eyebrow">{{ __('hermes.journal.types.three_good_things.eyebrow') }}</span>
                    <h3>{{ __('hermes.journal.types.three_good_things.title') }}</h3>
                    <p>{{ __('hermes.journal.types.three_good_things.text') }}</p>
                    <a href="#journal-three-good-things-form" class="pill journal-pill--orange">{{ __('hermes.journal.actions.three_good_things') }}</a>
                </div>

                <div class="journal-guided-card journal-guided-card--gray">
                    <span class="journal-guided-card__eyebrow">{{ __('hermes.journal.types.strengths_reflection.eyebrow') }}</span>
                    <h3>{{ __('hermes.journal.types.strengths_reflection.title') }}</h3>
                    <p>{{ __('hermes.journal.types.strengths_reflection.text') }}</p>
                    <a href="#journal-strengths-form" class="pill journal-pill--gray">{{ __('hermes.journal.actions.strengths_reflection') }}</a>
                </div>

                <div class="journal-guided-card journal-guided-card--green">
                    <span class="journal-guided-card__eyebrow">{{ __('hermes.journal.types.weekly_intention.eyebrow') }}</span>
                    <h3>{{ __('hermes.journal.types.weekly_intention.title') }}</h3>
                    <p>{{ __('hermes.journal.types.weekly_intention.text') }}</p>
                    <a href="#journal-weekly-intention-form" class="pill journal-pill--green">{{ __('hermes.journal.actions.weekly_intention') }}</a>
                </div>
            </div>

            <div class="journal-type-grid">
                <article
                    id="journal-daily-note-form"
                    class="{{ $dailyNoteFormClass }}"
                >
                    <x-user-section-heading
                        :eyebrow="__('hermes.journal.types.daily_note.eyebrow')"
                        :title="__('hermes.journal.sections.daily_note')"
                        :text="__('hermes.journal.types.daily_note.text')"
                    />

                    <form method="POST" action="{{ route('journal.store') }}" class="journal-form">
                        @csrf
                        <input type="hidden" name="entry_type" value="{{ \App\Models\JournalEntry::TYPE_DAILY_NOTE }}">

                        <div class="journal-field">
                            <label for="entry_date_daily_note">{{ __('hermes.journal.fields.entry_date') }}</label>
                            <input id="entry_date_daily_note" name="entry_date" type="date" value="{{ old('entry_type') === \App\Models\JournalEntry::TYPE_DAILY_NOTE ? old('entry_date', now()->toDateString()) : now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                        </div>

                        @include('journal.partials.entry-fields', [
                            'type' => \App\Models\JournalEntry::TYPE_DAILY_NOTE,
                            'values' => old('entry_type') === \App\Models\JournalEntry::TYPE_DAILY_NOTE ? old('content', []) : [],
                            'prefix' => 'content',
                            'suffix' => 'daily_note',
                            'strengthOptions' => $strengthOptions,
                            'selectedStrengthKeys' => $selectedStrengthKeys,
                        ])

                        <x-user-action-row align="end">
                            <button type="submit" class="pill">{{ __('hermes.journal.save') }}</button>
                        </x-user-action-row>
                    </form>
                </article>

                <article
                    id="journal-three-good-things-form"
                    class="{{ $threeGoodThingsFormClass }}"
                >
                    <x-user-section-heading
                        :eyebrow="__('hermes.journal.types.three_good_things.eyebrow')"
                        :title="__('hermes.journal.sections.three_good_things')"
                        :text="__('hermes.journal.types.three_good_things.text')"
                    />

                    <form method="POST" action="{{ route('journal.store') }}" class="journal-form">
                        @csrf
                        <input type="hidden" name="entry_type" value="{{ \App\Models\JournalEntry::TYPE_THREE_GOOD_THINGS }}">

                        <div class="journal-field">
                            <label for="entry_date_three_good_things">{{ __('hermes.journal.fields.entry_date') }}</label>
                            <input id="entry_date_three_good_things" name="entry_date" type="date" value="{{ old('entry_type') === \App\Models\JournalEntry::TYPE_THREE_GOOD_THINGS ? old('entry_date', now()->toDateString()) : now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                        </div>

                        @include('journal.partials.entry-fields', [
                            'type' => \App\Models\JournalEntry::TYPE_THREE_GOOD_THINGS,
                            'values' => old('entry_type') === \App\Models\JournalEntry::TYPE_THREE_GOOD_THINGS ? old('content', []) : [],
                            'prefix' => 'content',
                            'suffix' => 'three_good_things',
                            'strengthOptions' => $strengthOptions,
                            'selectedStrengthKeys' => $selectedStrengthKeys,
                        ])

                        <x-user-action-row align="end">
                            <button type="submit" class="pill">{{ __('hermes.journal.save') }}</button>
                        </x-user-action-row>
                    </form>
                </article>

                <article
                    id="journal-strengths-form"
                    class="{{ $strengthsReflectionFormClass }}"
                >
                    <x-user-section-heading
                        :eyebrow="__('hermes.journal.types.strengths_reflection.eyebrow')"
                        :title="__('hermes.journal.sections.strengths_reflection')"
                        :text="__('hermes.journal.types.strengths_reflection.text')"
                    />

                    <form method="POST" action="{{ route('journal.store') }}" class="journal-form">
                        @csrf
                        <input type="hidden" name="entry_type" value="{{ \App\Models\JournalEntry::TYPE_STRENGTHS_REFLECTION }}">

                        <div class="journal-field">
                            <label for="entry_date_strengths_reflection">{{ __('hermes.journal.fields.entry_date') }}</label>
                            <input id="entry_date_strengths_reflection" name="entry_date" type="date" value="{{ old('entry_type') === \App\Models\JournalEntry::TYPE_STRENGTHS_REFLECTION ? old('entry_date', now()->toDateString()) : now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                        </div>

                        @include('journal.partials.entry-fields', [
                            'type' => \App\Models\JournalEntry::TYPE_STRENGTHS_REFLECTION,
                            'values' => old('entry_type') === \App\Models\JournalEntry::TYPE_STRENGTHS_REFLECTION ? old('content', []) : [],
                            'prefix' => 'content',
                            'suffix' => 'strengths_reflection',
                            'strengthOptions' => $strengthOptions,
                            'selectedStrengthKeys' => $selectedStrengthKeys,
                        ])

                        <x-user-action-row align="end">
                            <button type="submit" class="pill">{{ __('hermes.journal.save') }}</button>
                        </x-user-action-row>
                    </form>
                </article>

                <article
                    id="journal-weekly-intention-form"
                    class="{{ $weeklyIntentionFormClass }}"
                >
                    <x-user-section-heading
                        :eyebrow="__('hermes.journal.types.weekly_intention.eyebrow')"
                        :title="__('hermes.journal.sections.weekly_intention')"
                        :text="__('hermes.journal.types.weekly_intention.text')"
                    />

                    <form method="POST" action="{{ route('journal.store') }}" class="journal-form">
                        @csrf
                        <input type="hidden" name="entry_type" value="{{ \App\Models\JournalEntry::TYPE_WEEKLY_INTENTION }}">

                        <div class="journal-field">
                            <label for="entry_date_weekly_intention">{{ __('hermes.journal.fields.entry_date') }}</label>
                            <input id="entry_date_weekly_intention" name="entry_date" type="date" value="{{ old('entry_type') === \App\Models\JournalEntry::TYPE_WEEKLY_INTENTION ? old('entry_date', now()->toDateString()) : now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                        </div>

                        @include('journal.partials.entry-fields', [
                            'type' => \App\Models\JournalEntry::TYPE_WEEKLY_INTENTION,
                            'values' => old('entry_type') === \App\Models\JournalEntry::TYPE_WEEKLY_INTENTION ? old('content', []) : [],
                            'prefix' => 'content',
                            'suffix' => 'weekly_intention',
                            'strengthOptions' => $strengthOptions,
                            'selectedStrengthKeys' => $selectedStrengthKeys,
                        ])

                        <x-user-action-row align="end">
                            <button type="submit" class="pill">{{ __('hermes.journal.save') }}</button>
                        </x-user-action-row>
                    </form>
                </article>
            </div>
        </x-user-surface-card>

        <div id="journal-compact-timeline" class="journal-section-anchor">
            @include('journal.timeline', [
                'timelineEmbedded' => true,
                'timelineRouteName' => 'journal.index',
                'timelineReturnTo' => 'journal.index',
                'timelineAnchor' => '#journal-compact-timeline',
            ])
        </div>
    </div>
</x-layouts.hermes-dashboard>
