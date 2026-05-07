@php
    use App\Models\JournalEntry;
    use Illuminate\Support\Str;

    $defaultEntryDate = min(now()->toDateString(), $activeMonth->endOfMonth()->toDateString());
    $isTimelineValidationState = $errors->any() && old('return_to') === 'journal.timeline';
    $selectedType = $isTimelineValidationState ? old('entry_type') : null;
    $hasActiveFilters = $selectedTypes !== [];

    $typeCards = [
        JournalEntry::TYPE_DAILY_NOTE => __('hermes.journal.sections.daily_note'),
        JournalEntry::TYPE_THREE_GOOD_THINGS => __('hermes.journal.sections.three_good_things'),
        JournalEntry::TYPE_STRENGTHS_REFLECTION => __('hermes.journal.sections.strengths_reflection'),
        JournalEntry::TYPE_WEEKLY_INTENTION => __('hermes.journal.sections.weekly_intention'),
    ];
@endphp

<x-layouts.hermes-dashboard :title="__('hermes.journal.timeline_page_title')">
    <x-slot:head>
        <style>
            .timeline-page,
            .timeline-list,
            .timeline-item__content,
            .timeline-item__icon,
            .timeline-item__meta,
            .timeline-empty,
            .timeline-composer,
            .timeline-filter,
            .timeline-composer__section,
            .timeline-form {
                display: grid;
                gap: 20px;
            }

            .timeline-page {
                gap: 28px;
            }

            .timeline-shell {
                padding: 28px;
                border-radius: 32px;
                background:
                    radial-gradient(circle at top left, rgba(32, 69, 58, 0.08), transparent 24%),
                    linear-gradient(180deg, #faf6ef 0%, #f7f3ec 100%);
                border: 1px solid rgba(23, 35, 31, 0.08);
                box-shadow: 0 22px 60px rgba(20, 38, 32, 0.08);
            }

            .timeline-toolbar {
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 16px;
                padding: 6px 4px 18px;
            }

            .timeline-monthbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                width: 100%;
                max-width: 100%;
            }

            .timeline-toolbar__actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
            }

            .timeline-monthbar__title,
            .timeline-item__weekday,
            .timeline-item__date,
            .timeline-item__time,
            .timeline-empty p,
            .timeline-composer__intro,
            .timeline-composer__button,
            .timeline-form__type-text,
            .timeline-form__type-title,
            .timeline-form label,
            .timeline-form input,
            .timeline-form select,
            .timeline-form textarea {
                margin: 0;
                font-family: "Avenir Next", "Segoe UI", sans-serif;
            }

            .timeline-monthbar__title {
                font-size: 1.05rem;
                font-weight: 700;
                color: #3b4a45;
                text-align: center;
                flex: 1;
            }

            .timeline-monthbar__nav,
            .timeline-toolbar__add,
            .timeline-toolbar__filter {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                border: 1px solid rgba(23, 35, 31, 0.1);
                background: rgba(255, 255, 255, 0.72);
                color: #41514c;
                font-family: "Avenir Next", "Segoe UI", sans-serif;
                font-weight: 700;
            }

            .timeline-monthbar__nav {
                width: 34px;
                height: 34px;
                font-size: 1rem;
                flex-shrink: 0;
            }

            .timeline-toolbar__filter,
            .timeline-toolbar__add {
                width: 42px;
                height: 42px;
                cursor: pointer;
            }

            .timeline-toolbar__filter {
                color: #315247;
                background: rgba(255, 255, 255, 0.9);
            }

            .timeline-toolbar__filter svg {
                width: 18px;
                height: 18px;
            }

            .timeline-toolbar__filter.is-active {
                border-color: rgba(31, 74, 61, 0.2);
                box-shadow: 0 10px 20px rgba(31, 74, 61, 0.1);
            }

            .timeline-toolbar__add {
                font-size: 1.5rem;
                line-height: 1;
                color: #fff;
                border-color: rgba(31, 74, 61, 0.2);
                background: linear-gradient(135deg, #4ab4ef 0%, #329bd8 100%);
                box-shadow: 0 14px 30px rgba(50, 155, 216, 0.24);
            }

            .timeline-monthbar__nav:hover,
            .timeline-monthbar__nav:focus-visible {
                color: #1f4a3d;
                border-color: rgba(31, 74, 61, 0.2);
            }

            .timeline-toolbar__filter:hover,
            .timeline-toolbar__filter:focus-visible,
            .timeline-toolbar__add:hover,
            .timeline-toolbar__add:focus-visible {
                transform: translateY(-1px);
            }

            .timeline-card,
            .timeline-composer,
            .timeline-filter {
                border-radius: 28px;
                background: rgba(255, 252, 248, 0.98);
                overflow: hidden;
                border: 1px solid rgba(23, 35, 31, 0.06);
            }

            .timeline-panel-toggle {
                position: absolute;
                opacity: 0;
                pointer-events: none;
            }

            .timeline-composer,
            .timeline-filter {
                padding: 22px;
                display: none;
            }

            .timeline-filter__form {
                display: grid;
                gap: 16px;
            }

            .timeline-filter__options {
                display: grid;
                gap: 10px;
            }

            .timeline-filter__option {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.86);
                border: 1px solid rgba(23, 35, 31, 0.08);
                font-family: "Avenir Next", "Segoe UI", sans-serif;
                color: #2a3733;
            }

            .timeline-filter__option input {
                width: 16px;
                height: 16px;
                margin: 0;
            }

            .timeline-filter__actions {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .timeline-composer__picker {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .timeline-composer__button {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: 12px 16px;
                border: 1px solid rgba(23, 35, 31, 0.08);
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.86);
                text-align: left;
                color: #2a3733;
                cursor: pointer;
            }

            .timeline-composer__button strong {
                font-size: 0.96rem;
            }

            .timeline-composer__button.is-active {
                border-color: rgba(50, 155, 216, 0.32);
                box-shadow: 0 12px 24px rgba(50, 155, 216, 0.12);
            }

            .timeline-composer__button::after {
                content: ">";
                font-size: 0.95rem;
                font-weight: 700;
                color: #81908b;
            }

            .timeline-form {
                gap: 16px;
                padding-top: 4px;
            }

            .timeline-form__header {
                display: grid;
                gap: 6px;
            }

            .timeline-form__type-title {
                font-size: 1.05rem;
                font-weight: 700;
                color: #21302b;
            }

            .timeline-form__type-text {
                color: #66746f;
                line-height: 1.55;
            }

            .timeline-form__fields,
            .timeline-form__actions {
                display: grid;
                gap: 16px;
            }

            .timeline-form label {
                display: grid;
                gap: 8px;
                font-size: 0.94rem;
                font-weight: 700;
                color: #16211d;
            }

            .timeline-form input,
            .timeline-form select,
            .timeline-form textarea {
                width: 100%;
                border: 1px solid rgba(22, 33, 29, 0.14);
                border-radius: 18px;
                padding: 14px 16px;
                background: rgba(255, 255, 255, 0.92);
                color: #16211d;
                font: inherit;
                line-height: 1.5;
            }

            .timeline-form textarea {
                min-height: 108px;
                resize: vertical;
            }

            .timeline-form__actions {
                grid-template-columns: auto auto;
                justify-content: end;
            }

            .timeline-panel--filter,
            .timeline-panel--menu,
            .timeline-panel--daily_note,
            .timeline-panel--three_good_things,
            .timeline-panel--strengths_reflection,
            .timeline-panel--weekly_intention {
                display: none;
            }

            .timeline-card:has(#timeline-panel-filter:checked) .timeline-filter,
            .timeline-card:has(#timeline-panel-menu:checked) .timeline-composer,
            .timeline-card:has(#timeline-panel-daily_note:checked) .timeline-composer,
            .timeline-card:has(#timeline-panel-three_good_things:checked) .timeline-composer,
            .timeline-card:has(#timeline-panel-strengths_reflection:checked) .timeline-composer,
            .timeline-card:has(#timeline-panel-weekly_intention:checked) .timeline-composer {
                display: grid;
            }

            .timeline-card:has(#timeline-panel-filter:checked) .timeline-list,
            .timeline-card:has(#timeline-panel-menu:checked) .timeline-list,
            .timeline-card:has(#timeline-panel-daily_note:checked) .timeline-list,
            .timeline-card:has(#timeline-panel-three_good_things:checked) .timeline-list,
            .timeline-card:has(#timeline-panel-strengths_reflection:checked) .timeline-list,
            .timeline-card:has(#timeline-panel-weekly_intention:checked) .timeline-list {
                display: none;
            }

            .timeline-card:has(#timeline-panel-filter:checked) .timeline-panel--filter,
            .timeline-card:has(#timeline-panel-menu:checked) .timeline-panel--menu,
            .timeline-card:has(#timeline-panel-daily_note:checked) .timeline-panel--daily_note,
            .timeline-card:has(#timeline-panel-three_good_things:checked) .timeline-panel--three_good_things,
            .timeline-card:has(#timeline-panel-strengths_reflection:checked) .timeline-panel--strengths_reflection,
            .timeline-card:has(#timeline-panel-weekly_intention:checked) .timeline-panel--weekly_intention {
                display: grid;
            }

            .timeline-list {
                gap: 0;
            }

            .timeline-item {
                display: flex;
                align-items: stretch;
                gap: 18px;
                padding: 18px 22px;
                border-bottom: 1px solid rgba(23, 35, 31, 0.08);
                background: rgba(255, 255, 255, 0.94);
            }

            .timeline-item__leading {
                display: flex;
                width: 56px;
                flex-shrink: 0;
                align-items: flex-start;
                justify-content: center;
                padding-top: 2px;
            }

            .timeline-item__calendar {
                display: grid;
                gap: 2px;
                text-align: center;
                color: #3d4c47;
            }

            .timeline-item__weekday {
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #75837d;
            }

            .timeline-item__date {
                font-size: 1.65rem;
                font-weight: 700;
                line-height: 1;
            }

            .timeline-item__content {
                flex: 1;
                min-width: 0;
                gap: 4px;
                padding-right: 8px;
            }

            .timeline-item__title,
            .timeline-item__summary {
                margin: 0;
                font-family: "Avenir Next", "Segoe UI", sans-serif;
                min-width: 0;
            }

            .timeline-item__title {
                font-size: 1.02rem;
                font-weight: 700;
                color: #1d2b27;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .timeline-item__summary {
                color: #5c6a65;
                line-height: 1.45;
            }

            .timeline-item__meta {
                gap: 4px;
            }

            .timeline-item__time {
                font-size: 0.88rem;
                color: #7a8681;
            }

            .timeline-item__icon {
                place-items: center;
                align-content: center;
                width: 72px;
                min-width: 72px;
                aspect-ratio: 1;
                border-radius: 18px;
                border: 1px solid rgba(23, 35, 31, 0.08);
                background: linear-gradient(160deg, #fbf6ee 0%, #f0e1cb 100%);
                color: #9d5a2c;
            }

            .timeline-item__icon--three-good-things {
                background: linear-gradient(160deg, #fff0e6 0%, #f6d9c3 100%);
                color: #c25f21;
            }

            .timeline-item__icon--strengths-reflection {
                background: linear-gradient(160deg, #eef3f1 0%, #d8e3de 100%);
                color: #2f6254;
            }

            .timeline-item__icon--weekly-intention {
                background: linear-gradient(160deg, #e8f0ec 0%, #cfddd7 100%);
                color: #29564a;
            }

            .timeline-item__icon svg {
                width: 28px;
                height: 28px;
            }

            .timeline-footer {
                padding: 16px 22px 22px;
                background: rgba(255, 252, 248, 0.98);
            }

            .timeline-footer .pagination {
                margin: 0;
            }

            .timeline-empty {
                padding: 24px 22px 28px;
            }

            .timeline-empty h2 {
                margin: 0;
                font-family: "Iowan Old Style", "Palatino Linotype", Georgia, serif;
                color: #1b2925;
            }

            .timeline-empty p {
                color: #5d6b66;
                line-height: 1.7;
            }

            @media (max-width: 760px) {
                .timeline-shell {
                    padding: 20px;
                }

                .timeline-toolbar {
                    grid-template-columns: 1fr;
                }

                .timeline-toolbar__actions {
                    justify-content: start;
                }

                .timeline-item {
                    gap: 14px;
                    padding: 16px;
                }

                .timeline-item__icon {
                    width: 56px;
                    min-width: 56px;
                    border-radius: 16px;
                }

                .timeline-item__icon svg {
                    width: 24px;
                    height: 24px;
                }

                .timeline-item__leading {
                    width: 48px;
                }

                .timeline-item__date {
                    font-size: 1.4rem;
                }

                .timeline-filter__actions,
                .timeline-form__actions {
                    justify-content: stretch;
                }
            }
        </style>
    </x-slot:head>

    <div class="timeline-page">
        @if (session('status'))
            <div class="user-feedback user-feedback--status">
                {{ session('status') }}
            </div>
        @endif

        <div class="timeline-shell">
            <div class="timeline-toolbar">
                <div class="timeline-monthbar">
                    <a href="{{ route('journal.timeline', ['month' => $previousMonth->format('Y-m'), 'types' => $selectedTypes]) }}" class="timeline-monthbar__nav" aria-label="Previous month">&lt;</a>
                    <p class="timeline-monthbar__title">{{ ucfirst($activeMonth->translatedFormat('F Y')) }}</p>
                    <a href="{{ route('journal.timeline', ['month' => $nextMonth->format('Y-m'), 'types' => $selectedTypes]) }}" class="timeline-monthbar__nav" aria-label="Next month">&gt;</a>
                </div>

                <div class="timeline-toolbar__actions">
                    <label for="timeline-panel-filter" class="timeline-toolbar__filter {{ $hasActiveFilters ? 'is-active' : '' }}" aria-label="{{ __('hermes.journal.timeline_filter') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 6h16" />
                            <path d="M7 12h10" />
                            <path d="M10 18h4" />
                        </svg>
                    </label>
                    <label for="timeline-panel-menu" class="timeline-toolbar__add" aria-label="{{ __('hermes.journal.timeline_add') }}">+</label>
                </div>
            </div>

            <div class="timeline-card">
                <input type="radio" name="timeline_panel" id="timeline-panel-closed" class="timeline-panel-toggle" @checked(! $isTimelineValidationState)>
                <input type="radio" name="timeline_panel" id="timeline-panel-filter" class="timeline-panel-toggle">
                <input type="radio" name="timeline_panel" id="timeline-panel-menu" class="timeline-panel-toggle" @checked($isTimelineValidationState && $selectedType === null)>
                <input type="radio" name="timeline_panel" id="timeline-panel-daily_note" class="timeline-panel-toggle" @checked($selectedType === JournalEntry::TYPE_DAILY_NOTE)>
                <input type="radio" name="timeline_panel" id="timeline-panel-three_good_things" class="timeline-panel-toggle" @checked($selectedType === JournalEntry::TYPE_THREE_GOOD_THINGS)>
                <input type="radio" name="timeline_panel" id="timeline-panel-strengths_reflection" class="timeline-panel-toggle" @checked($selectedType === JournalEntry::TYPE_STRENGTHS_REFLECTION)>
                <input type="radio" name="timeline_panel" id="timeline-panel-weekly_intention" class="timeline-panel-toggle" @checked($selectedType === JournalEntry::TYPE_WEEKLY_INTENTION)>

                <div class="timeline-panel--filter timeline-filter">
                    <form method="GET" action="{{ route('journal.timeline') }}" class="timeline-filter__form">
                        <input type="hidden" name="month" value="{{ $activeMonth->format('Y-m') }}">

                        <div class="timeline-filter__options">
                            @foreach ($entryTypes as $entryType)
                                <label class="timeline-filter__option">
                                    <input type="checkbox" name="types[]" value="{{ $entryType }}" @checked(in_array($entryType, $selectedTypes, true))>
                                    <span>{{ __('hermes.journal.sections.'.$entryType) }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="timeline-filter__actions">
                            <a href="{{ route('journal.timeline', ['month' => $activeMonth->format('Y-m')]) }}" class="pill pill--neutral">{{ __('hermes.journal.timeline_filter_clear') }}</a>
                            <label for="timeline-panel-closed" class="pill pill--neutral">{{ __('hermes.journal.timeline_cancel') }}</label>
                            <button type="submit" class="pill">{{ __('hermes.journal.timeline_filter_apply') }}</button>
                        </div>
                    </form>
                </div>

                <div class="timeline-composer">
                    <div class="timeline-panel--menu timeline-composer__picker">
                        @foreach ($entryTypes as $entryType)
                            <label
                                for="timeline-panel-{{ $entryType }}"
                                class="timeline-composer__button"
                            >
                                <strong>{{ __('hermes.journal.sections.'.$entryType) }}</strong>
                            </label>
                        @endforeach

                        <label for="timeline-panel-closed" class="pill pill--neutral">{{ __('hermes.journal.timeline_cancel') }}</label>
                    </div>

                    @foreach ($entryTypes as $entryType)
                        <div class="timeline-panel--{{ $entryType }} timeline-composer__section">
                            <form method="POST" action="{{ route('journal.store') }}" class="timeline-form">
                                @csrf
                                <input type="hidden" name="entry_type" value="{{ $entryType }}">
                                <input type="hidden" name="return_to" value="journal.timeline">
                                <input type="hidden" name="return_month" value="{{ $activeMonth->format('Y-m') }}">
                                @foreach ($selectedTypes as $selectedFilterType)
                                    <input type="hidden" name="return_types[]" value="{{ $selectedFilterType }}">
                                @endforeach

                                <div class="timeline-form__header">
                                    <p class="timeline-form__type-title">{{ $typeCards[$entryType] }}</p>
                                    <p class="timeline-form__type-text">{{ __('hermes.journal.types.'.$entryType.'.text') }}</p>
                                </div>

                                <div class="timeline-form__fields">
                                    <label for="timeline_entry_date_{{ $entryType }}">
                                        {{ __('hermes.journal.fields.entry_date') }}
                                        <input
                                            id="timeline_entry_date_{{ $entryType }}"
                                            name="entry_date"
                                            type="date"
                                            value="{{ old('entry_type') === $entryType ? old('entry_date', $defaultEntryDate) : $defaultEntryDate }}"
                                            max="{{ now()->toDateString() }}"
                                            required
                                        >
                                    </label>

                                    @include('journal.partials.entry-fields', [
                                        'type' => $entryType,
                                        'values' => old('entry_type') === $entryType ? old('content', []) : [],
                                        'prefix' => 'content',
                                        'suffix' => 'timeline_'.$entryType,
                                        'strengthOptions' => $strengthOptions,
                                        'selectedStrengthKeys' => $selectedStrengthKeys,
                                    ])
                                </div>

                                <div class="timeline-form__actions">
                                    <label for="timeline-panel-closed" class="pill pill--neutral">{{ __('hermes.journal.timeline_cancel') }}</label>
                                    <button type="submit" class="pill">{{ __('hermes.journal.save') }}</button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="timeline-list">
                    @forelse ($entries as $entry)
                        @php
                            $typeKey = "hermes.journal.types.{$entry->entry_type}";

                            $title = match ($entry->entry_type) {
                                JournalEntry::TYPE_DAILY_NOTE => $entry->contentValue('title') ?: __('hermes.journal.sections.daily_note'),
                                JournalEntry::TYPE_THREE_GOOD_THINGS => $entry->contentValue('what_went_well') ?: __('hermes.journal.sections.three_good_things'),
                                JournalEntry::TYPE_STRENGTHS_REFLECTION => ($entry->strengthLabel() ?: __('hermes.journal.sections.strengths_reflection')),
                                JournalEntry::TYPE_WEEKLY_INTENTION => ($entry->strengthLabel() ?: __('hermes.journal.sections.weekly_intention')),
                                default => __('hermes.journal.log_title'),
                            };

                            $summary = match ($entry->entry_type) {
                                JournalEntry::TYPE_DAILY_NOTE => Str::limit($entry->contentValue('body') ?? '', 110),
                                JournalEntry::TYPE_THREE_GOOD_THINGS => Str::limit($entry->contentValue('my_contribution') ?? '', 110),
                                JournalEntry::TYPE_STRENGTHS_REFLECTION => Str::limit(trim(($entry->contentValue('situation') ?? '').' '.$entry->contentValue('how_used')), 110),
                                JournalEntry::TYPE_WEEKLY_INTENTION => Str::limit(trim(($entry->contentValue('planned_strength_use') ?? '').' '.$entry->contentValue('general_intention')), 110),
                                default => '',
                            };
                        @endphp

                        <article class="timeline-item">
                            <div class="timeline-item__leading">
                                <div class="timeline-item__calendar">
                                    <span class="timeline-item__weekday">{{ $entry->entry_date->translatedFormat('D') }}</span>
                                    <span class="timeline-item__date">{{ $entry->entry_date->format('d') }}</span>
                                </div>
                            </div>

                            <div class="timeline-item__content">
                                <p class="timeline-item__title">{{ $title }}</p>
                                <p class="timeline-item__summary">{{ $summary }}</p>
                                <div class="timeline-item__meta">
                                    <span class="timeline-item__time">{{ $entry->created_at?->format('H:i') }}</span>
                                </div>
                            </div>

                            <div class="timeline-item__icon timeline-item__icon--{{ str_replace('_', '-', $entry->entry_type) }}" aria-hidden="true">
                                @if ($entry->entry_type === JournalEntry::TYPE_DAILY_NOTE)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 3.5h6l4.5 4.5V19a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5.5a2 2 0 0 1 2-2Z" />
                                        <path d="M14 3.5V8h4.5" />
                                        <path d="M9 12h6" />
                                        <path d="M9 16h6" />
                                    </svg>
                                @elseif ($entry->entry_type === JournalEntry::TYPE_THREE_GOOD_THINGS)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3.5l1.9 4 4.4.6-3.2 3.1.8 4.4L12 13.5l-3.9 2.1.8-4.4-3.2-3.1 4.4-.6L12 3.5Z" />
                                        <path d="M18.5 16.5l.8 1.6 1.7.3-1.2 1.2.3 1.7-1.6-.9-1.5.9.3-1.7-1.2-1.2 1.7-.3.7-1.6Z" />
                                    </svg>
                                @elseif ($entry->entry_type === JournalEntry::TYPE_STRENGTHS_REFLECTION)
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20.5s-6-3.5-6-9a3.5 3.5 0 0 1 6-2.3 3.5 3.5 0 0 1 6 2.3c0 5.5-6 9-6 9Z" />
                                        <path d="M9.5 12.5l1.6 1.6 3.4-3.7" />
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="7.5" />
                                        <path d="M12 8.5v4l2.8 2.2" />
                                        <path d="M12 4v1.5" />
                                    </svg>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="timeline-empty">
                            <h2>{{ __('hermes.journal.empty.title') }}</h2>
                            <p>{{ __('hermes.journal.empty.text') }}</p>
                            <p>{{ __('hermes.journal.timeline_empty') }}</p>
                        </div>
                    @endforelse
                </div>

                @if ($entries->hasPages())
                    <div class="timeline-footer">
                        {{ $entries->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.hermes-dashboard>
