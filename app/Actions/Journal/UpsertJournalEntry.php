<?php

namespace App\Actions\Journal;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

class UpsertJournalEntry
{
    /**
     * @param  array{entry_date: string, entry_type: string, content: array<string, mixed>}  $validated
     */
    public function __invoke(User $user, array $validated): JournalEntry
    {
        $entry = $user->journalEntries()
            ->whereDate('entry_date', $validated['entry_date'])
            ->where('entry_type', $validated['entry_type'])
            ->first();

        if ($entry !== null) {
            $entry->update($this->payload($validated));

            return $entry->fresh() ?? $entry;
        }

        try {
            /** @var JournalEntry $entry */
            $entry = $user->journalEntries()->create($this->payload($validated));

            return $entry;
        } catch (UniqueConstraintViolationException) {
            // A concurrent request created the same entry between our first() and create() calls.
            // Re-fetch and update so the user sees their latest content.
            $entry = $user->journalEntries()
                ->whereDate('entry_date', $validated['entry_date'])
                ->where('entry_type', $validated['entry_type'])
                ->firstOrFail();

            $entry->update($this->payload($validated));

            return $entry->fresh() ?? $entry;
        }
    }

    /**
     * @param  array{entry_date: string, entry_type: string, content: array<string, mixed>}  $validated
     * @return array<string, mixed>
     */
    public function payload(array $validated): array
    {
        $content = $validated['content'];

        return [
            ...$validated,
            'what_went_well' => $validated['entry_type'] === JournalEntry::TYPE_THREE_GOOD_THINGS
                ? (string) ($content['what_went_well'] ?? '')
                : '',
            'my_contribution' => $validated['entry_type'] === JournalEntry::TYPE_THREE_GOOD_THINGS
                ? (string) ($content['my_contribution'] ?? '')
                : '',
        ];
    }
}
