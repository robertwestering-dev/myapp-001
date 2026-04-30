<x-layouts.hermes-dashboard :title="__('hermes.journal.title')">
    <x-slot:head>
        <style>
            .journal-page,
            .journal-stack,
            .journal-form,
            .journal-entry,
            .journal-fields,
            .journal-hero,
            .journal-log {
                display: grid;
                gap: 24px;
            }

            .journal-page {
                gap: 28px;
            }

            .journal-hero {
                grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
                align-items: start;
            }

            .journal-card {
                padding: 32px;
            }

            .journal-card h2,
            .journal-card p {
                margin: 0;
            }

            .journal-hero__copy {
                display: grid;
                gap: 16px;
            }

            .journal-hero__message {
                max-width: 36rem;
                color: #5a6762;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 1.05rem;
                line-height: 1.7;
            }

            .journal-hero__actions {
                display: grid;
                gap: 14px;
                align-content: start;
            }

            .journal-hero__actions .pill {
                width: 100%;
                justify-content: center;
            }

            .journal-form {
                gap: 18px;
                margin-top: 24px;
            }

            .journal-field {
                display: grid;
                gap: 8px;
            }

            .journal-type-grid {
                display: grid;
                gap: 20px;
            }

            .journal-field label,
            .journal-entry__meta,
            .journal-helper,
            .journal-hint {
                font-family: Arial, Helvetica, sans-serif;
            }

            .journal-field label,
            .journal-entry__meta strong {
                color: #16211d;
                font-weight: 700;
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
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.5;
            }

            .journal-field textarea {
                min-height: 108px;
                resize: vertical;
            }

            .journal-helper,
            .journal-hint,
            .journal-empty p {
                color: #5a6762;
                line-height: 1.6;
            }

            .journal-log-list {
                display: grid;
                gap: 12px;
                margin-top: 18px;
            }

            .journal-log-item {
                display: grid;
                gap: 14px;
                padding: 18px 20px;
                border-radius: 22px;
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(22, 33, 29, 0.08);
            }

            .journal-log-row {
                display: block;
            }

            .journal-log-main {
                display: grid;
                gap: 6px;
                min-width: 0;
            }

            .journal-log-top {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 12px;
                align-items: center;
            }

            .journal-log-date,
            .journal-log-summary,
            .journal-log-actions,
            .journal-icon-button,
            .journal-entry__meta {
                font-family: Arial, Helvetica, sans-serif;
            }

            .journal-log-date {
                font-size: 0.92rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #5a6762;
            }

            .journal-log-summary {
                color: #16211d;
                line-height: 1.5;
            }

            .journal-entry__pill {
                display: inline-flex;
                align-items: center;
                padding: 6px 12px;
                border-radius: 999px;
                background: rgba(32, 69, 58, 0.1);
                color: #20453a;
                font-size: 0.88rem;
                font-weight: 700;
            }

            .journal-log-actions {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                flex-shrink: 0;
                justify-self: end;
                white-space: nowrap;
            }

            .journal-log-actions form {
                display: inline-flex;
            }

            .journal-edit-toggle {
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

            .journal-icon-button:hover,
            .journal-icon-button:focus-visible {
                border-color: rgba(217, 106, 43, 0.45);
                color: #a84a19;
            }

            .journal-icon-button svg {
                width: 17px;
                height: 17px;
            }

            .journal-log-item:has(.journal-edit-toggle:checked) .journal-icon-button {
                border-color: rgba(217, 106, 43, 0.45);
                color: #a84a19;
            }

            .journal-entry {
                display: none;
                gap: 18px;
                padding-top: 18px;
                border-top: 1px solid rgba(22, 33, 29, 0.08);
            }

            .journal-log-item:has(.journal-edit-toggle:checked) .journal-entry {
                display: grid;
            }

            .journal-entry__meta {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
                justify-content: space-between;
            }

            .journal-entry__body p,
            .journal-empty h2,
            .journal-empty p {
                margin: 0;
            }

            .journal-entry__body strong {
                display: block;
                margin-bottom: 4px;
            }

            .journal-section-anchor {
                scroll-margin-top: 110px;
            }

            @media (max-width: 920px) {
                .journal-hero {
                    grid-template-columns: 1fr;
                }

                .journal-log-top {
                    grid-template-columns: 1fr;
                }

                .journal-log-actions {
                    justify-self: start;
                }
            }
        </style>
    </x-slot:head>

    <div class="journal-page">
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
            </div>

            <div class="journal-hero__actions">
                <a href="#journal-three-good-things-form" class="pill">{{ __('hermes.journal.actions.three_good_things') }}</a>
                <a href="#journal-strengths-form" class="pill pill--neutral">{{ __('hermes.journal.actions.strengths_reflection') }}</a>
            </div>
        </x-user-surface-card>

        <div class="journal-stack">
            <x-user-surface-card class="journal-card journal-log">
                <x-user-section-heading :eyebrow="__('hermes.journal.entries_eyebrow')" />

                <div class="journal-log-list">
                    @forelse ($entries as $entry)
                        @php
                            $typeKey = "hermes.journal.types.{$entry->entry_type}";
                            $summary = $entry->isThreeGoodThings()
                                ? $entry->contentValue('what_went_well')
                                : trim(($entry->strengthLabel() ?? '').' · '.($entry->contentValue('situation') ?? ''));
                        @endphp
                        <x-user-surface-card variant="soft" class="journal-log-item">
                            <input
                                id="journal_edit_{{ $entry->getKey() }}"
                                type="checkbox"
                                class="journal-edit-toggle"
                            >

                            <div class="journal-log-row">
                                <div class="journal-log-main">
                                    <div class="journal-log-top">
                                        <span class="journal-log-date">{{ $entry->entry_date->format('d-m-Y') }}</span>

                                        <div class="journal-log-actions">
                                            <span class="journal-entry__pill">{{ __($typeKey.'.label') }}</span>

                                            <label for="journal_edit_{{ $entry->getKey() }}" class="journal-icon-button" aria-label="{{ __('hermes.journal.edit') }}">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z" />
                                                </svg>
                                            </label>

                                            <form method="POST" action="{{ route('journal.destroy', $entry) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="journal-icon-button" aria-label="{{ __('hermes.journal.delete') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M3 6h18" />
                                                        <path d="M8 6V4h8v2" />
                                                        <path d="M19 6l-1 14H6L5 6" />
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <span class="journal-log-summary">
                                        <strong>{{ __($typeKey.'.label') }}</strong>
                                        {{ \Illuminate\Support\Str::limit($summary ?? '', 120) }}
                                    </span>
                                </div>
                            </div>

                            <div class="journal-entry">
                                <div class="journal-entry__body">
                                    @if ($entry->isThreeGoodThings())
                                        <p><strong>{{ __($typeKey.'.fields.what_went_well') }}</strong>{{ $entry->contentValue('what_went_well') }}</p>
                                        <p><strong>{{ __($typeKey.'.fields.my_contribution') }}</strong>{{ $entry->contentValue('my_contribution') }}</p>
                                    @elseif ($entry->isStrengthsReflection())
                                        <p><strong>{{ __($typeKey.'.fields.strength_key') }}</strong>{{ $entry->strengthLabel() }}</p>
                                        <p><strong>{{ __($typeKey.'.fields.situation') }}</strong>{{ $entry->contentValue('situation') }}</p>
                                        <p><strong>{{ __($typeKey.'.fields.how_used') }}</strong>{{ $entry->contentValue('how_used') }}</p>
                                        <p><strong>{{ __($typeKey.'.fields.reflection') }}</strong>{{ $entry->contentValue('reflection') }}</p>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('journal.update', $entry) }}" class="journal-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="entry_type" value="{{ $entry->entry_type }}">

                                    <div class="journal-field">
                                        <label for="entry_date_{{ $entry->getKey() }}">{{ __('hermes.journal.fields.entry_date') }}</label>
                                        <input id="entry_date_{{ $entry->getKey() }}" name="entry_date" type="date" value="{{ $entry->entry_date->toDateString() }}" max="{{ now()->toDateString() }}" required>
                                    </div>

                                    @include('journal.partials.entry-fields', [
                                        'type' => $entry->entry_type,
                                        'values' => $entry->content ?? [],
                                        'prefix' => 'content',
                                        'suffix' => 'entry_'.$entry->getKey(),
                                        'strengthOptions' => $strengthOptions,
                                        'selectedStrengthKeys' => $selectedStrengthKeys,
                                    ])

                                    <x-user-action-row align="end">
                                        <button type="submit" class="pill">{{ __('hermes.journal.update') }}</button>
                                    </x-user-action-row>
                                </form>
                            </div>
                        </x-user-surface-card>
                    @empty
                        <x-user-surface-card variant="soft" class="journal-card journal-empty">
                            <h2>{{ __('hermes.journal.empty.title') }}</h2>
                            <p>{{ __('hermes.journal.empty.text') }}</p>
                        </x-user-surface-card>
                    @endforelse
                </div>

                @if ($entries->hasPages())
                    {{ $entries->links() }}
                @endif
            </x-user-surface-card>

            <x-user-surface-card id="journal-three-good-things-form" class="journal-card journal-section-anchor">
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
            </x-user-surface-card>

            <x-user-surface-card id="journal-strengths-form" class="journal-card journal-section-anchor">
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
            </x-user-surface-card>
        </div>
    </div>
</x-layouts.hermes-dashboard>
