<?php

namespace App\Http\Requests;

use App\Models\JournalEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isProUser() ?? false;
    }

    public function rules(): array
    {
        /** @var JournalEntry|null $entry */
        $entry = $this->route('journalEntry');
        $strengthKeys = collect($this->user()?->selected_strengths ?? [])
            ->filter(fn (mixed $strength): bool => is_string($strength) && $strength !== '')
            ->values()
            ->all();
        $isThreeGoodThings = $this->entryType() === JournalEntry::TYPE_THREE_GOOD_THINGS;
        $isStrengthsReflection = $this->entryType() === JournalEntry::TYPE_STRENGTHS_REFLECTION;

        return [
            'entry_date' => array_filter([
                'required',
                'date',
                'before_or_equal:today',
                $entry !== null
                    ? Rule::unique('three_good_things_entries', 'entry_date')
                        ->where(fn ($query) => $query
                            ->where('user_id', $this->user()->getKey())
                            ->where('entry_type', $this->input('entry_type')))
                        ->ignore($entry)
                    : null,
            ]),
            'entry_type' => ['required', 'string', Rule::in(JournalEntry::entryTypeOptions())],
            'content' => ['required', 'array:what_went_well,my_contribution,strength_key,situation,how_used,reflection'],
            'content.what_went_well' => [Rule::requiredIf($isThreeGoodThings), 'nullable', 'string', 'max:255'],
            'content.my_contribution' => [Rule::requiredIf($isThreeGoodThings), 'nullable', 'string', 'max:255'],
            'content.strength_key' => [Rule::requiredIf($isStrengthsReflection), 'nullable', 'string', Rule::in($strengthKeys)],
            'content.situation' => [Rule::requiredIf($isStrengthsReflection), 'nullable', 'string', 'max:255'],
            'content.how_used' => [Rule::requiredIf($isStrengthsReflection), 'nullable', 'string', 'max:255'],
            'content.reflection' => [Rule::requiredIf($isStrengthsReflection), 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.strength_key.in' => __('hermes.journal.types.strengths_reflection.validation.invalid'),
        ];
    }

    public function attributes(): array
    {
        return [
            'entry_date' => __('hermes.journal.fields.entry_date'),
            'entry_type' => __('hermes.journal.fields.entry_type'),
            'content.what_went_well' => __('hermes.journal.types.three_good_things.fields.what_went_well'),
            'content.my_contribution' => __('hermes.journal.types.three_good_things.fields.my_contribution'),
            'content.strength_key' => __('hermes.journal.types.strengths_reflection.fields.strength_key'),
            'content.situation' => __('hermes.journal.types.strengths_reflection.fields.situation'),
            'content.how_used' => __('hermes.journal.types.strengths_reflection.fields.how_used'),
            'content.reflection' => __('hermes.journal.types.strengths_reflection.fields.reflection'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! is_array($this->input('content'))) {
            $this->merge([
                'content' => [],
            ]);
        }
    }

    protected function entryType(): ?string
    {
        $type = $this->input('entry_type');

        return is_string($type) ? $type : null;
    }
}
