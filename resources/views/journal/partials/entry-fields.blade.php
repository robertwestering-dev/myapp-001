@php($baseKey = "hermes.journal.types.{$type}")

@if ($type === \App\Models\JournalEntry::TYPE_THREE_GOOD_THINGS)
    <div class="journal-fields">
        <div class="journal-field">
            <label for="{{ $suffix }}_what_went_well">{{ __($baseKey.'.fields.what_went_well') }}</label>
            <textarea id="{{ $suffix }}_what_went_well" name="{{ $prefix }}[what_went_well]" maxlength="255" placeholder="{{ __($baseKey.'.placeholder.what_went_well') }}" required>{{ $values['what_went_well'] ?? '' }}</textarea>
            <span class="journal-helper">{{ __($baseKey.'.helper.what_went_well') }}</span>
        </div>

        <div class="journal-field">
            <label for="{{ $suffix }}_my_contribution">{{ __($baseKey.'.fields.my_contribution') }}</label>
            <textarea id="{{ $suffix }}_my_contribution" name="{{ $prefix }}[my_contribution]" maxlength="255" required>{{ $values['my_contribution'] ?? '' }}</textarea>
            <span class="journal-helper">{{ __($baseKey.'.helper.my_contribution') }}</span>
        </div>
    </div>
@elseif ($type === \App\Models\JournalEntry::TYPE_STRENGTHS_REFLECTION)
    <div class="journal-fields">
        <div class="journal-field">
            <label for="{{ $suffix }}_strength_key">{{ __($baseKey.'.fields.strength_key') }}</label>
            <select id="{{ $suffix }}_strength_key" name="{{ $prefix }}[strength_key]" required>
                <option value="">{{ __('hermes.settings.profile.fields.choose_option') }}</option>
                @foreach ($strengthOptions as $option)
                    <option value="{{ $option['key'] }}" @selected(($values['strength_key'] ?? '') === $option['key'])>
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>
            <span class="journal-helper">{{ __($baseKey.'.selected_strengths_hint') }}</span>
        </div>

        <div class="journal-field">
            <label for="{{ $suffix }}_situation">{{ __($baseKey.'.fields.situation') }}</label>
            <textarea id="{{ $suffix }}_situation" name="{{ $prefix }}[situation]" maxlength="255" required>{{ $values['situation'] ?? '' }}</textarea>
            <span class="journal-helper">{{ __($baseKey.'.helper.situation') }}</span>
        </div>

        <div class="journal-field">
            <label for="{{ $suffix }}_how_used">{{ __($baseKey.'.fields.how_used') }}</label>
            <textarea id="{{ $suffix }}_how_used" name="{{ $prefix }}[how_used]" maxlength="255" required>{{ $values['how_used'] ?? '' }}</textarea>
            <span class="journal-helper">{{ __($baseKey.'.helper.how_used') }}</span>
        </div>

        <div class="journal-field">
            <label for="{{ $suffix }}_reflection">{{ __($baseKey.'.fields.reflection') }}</label>
            <textarea id="{{ $suffix }}_reflection" name="{{ $prefix }}[reflection]" maxlength="1000" required>{{ $values['reflection'] ?? '' }}</textarea>
            <span class="journal-helper">{{ __($baseKey.'.helper.reflection') }}</span>
        </div>
    </div>
@endif
