<?php

namespace App\Http\Controllers;

use App\Actions\Journal\UpsertJournalEntry;
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
        private readonly UpsertJournalEntry $upsertJournalEntry,
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
        ($this->upsertJournalEntry)($request->user(), $request->validated());

        return redirect()
            ->route('journal.index')
            ->with('status', __('hermes.journal.status.saved'));
    }

    public function update(UpsertJournalEntryRequest $request, JournalEntry $journalEntry): RedirectResponse
    {
        $entry = $this->resolveOwnedEntry($request, $journalEntry);

        $entry->update($this->upsertJournalEntry->payload($request->validated()));

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
}
