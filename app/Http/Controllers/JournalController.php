<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertJournalEntryRequest;
use App\Models\JournalEntry;
use App\Models\User;
use App\Support\Academy\PositiveFoundationStrengthCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function __construct(
        private readonly PositiveFoundationStrengthCatalog $strengthCatalog,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isProUser()) {
            return redirect()
                ->route('pro-upgrade.show')
                ->with('status', __('hermes.journal.pro_required'));
        }

        $selectedStrengthKeys = collect($user->selected_strengths ?? [])
            ->filter(fn (mixed $strength): bool => is_string($strength) && $strength !== '')
            ->values()
            ->all();

        $strengthOptions = collect($this->strengthCatalog->options())
            ->filter(fn (array $option): bool => in_array($option['key'], $selectedStrengthKeys, true))
            ->values()
            ->all();

        return view('journal.index', [
            'entries' => JournalEntry::query()
                ->forUser($user)
                ->recent()
                ->paginate(config('app.per_page')),
            'entryTypes' => JournalEntry::entryTypeOptions(),
            'selectedStrengthKeys' => $selectedStrengthKeys,
            'strengthOptions' => $strengthOptions,
        ]);
    }

    public function store(UpsertJournalEntryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $entry = $user->journalEntries()
            ->whereDate('entry_date', $validated['entry_date'])
            ->where('entry_type', $validated['entry_type'])
            ->first();

        if ($entry !== null) {
            $entry->update($this->entryPayload($validated));
        } else {
            $user->journalEntries()->create($this->entryPayload($validated));
        }

        return redirect()
            ->route('journal.index')
            ->with('status', __('hermes.journal.status.saved'));
    }

    public function update(UpsertJournalEntryRequest $request, JournalEntry $journalEntry): RedirectResponse
    {
        $entry = $this->resolveOwnedEntry($request, $journalEntry);

        $entry->update($this->entryPayload($request->validated()));

        return redirect()
            ->route('journal.index')
            ->with('status', __('hermes.journal.status.updated'));
    }

    public function destroy(Request $request, JournalEntry $journalEntry): RedirectResponse
    {
        $entry = $this->resolveOwnedEntry($request, $journalEntry);

        $entry->delete();

        return redirect()
            ->route('journal.index')
            ->with('status', __('hermes.journal.status.deleted'));
    }

    protected function resolveOwnedEntry(Request $request, JournalEntry $journalEntry): JournalEntry
    {
        abort_unless($journalEntry->user_id === $request->user()->getKey(), 404);

        return $journalEntry;
    }

    /**
     * @param  array{entry_date: string, entry_type: string, content: array<string, mixed>}  $validated
     * @return array<string, mixed>
     */
    protected function entryPayload(array $validated): array
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
