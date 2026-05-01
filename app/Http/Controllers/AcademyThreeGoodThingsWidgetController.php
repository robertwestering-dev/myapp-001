<?php

namespace App\Http\Controllers;

use App\Actions\Journal\UpsertJournalEntry;
use App\Http\Requests\UpsertJournalEntryRequest;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AcademyThreeGoodThingsWidgetController extends Controller
{
    public function __construct(
        private readonly UpsertJournalEntry $upsertJournalEntry,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isProUser()) {
            return redirect()
                ->route('pro-upgrade.show')
                ->with('status', __('hermes.journal.pro_required'));
        }

        $entryDate = Carbon::today()->toDateString();

        return view('academy.three-good-things-widget', [
            'entry' => $user->journalEntries()
                ->whereDate('entry_date', $entryDate)
                ->where('entry_type', JournalEntry::TYPE_THREE_GOOD_THINGS)
                ->first(),
            'entryDate' => $entryDate,
        ]);
    }

    public function store(UpsertJournalEntryRequest $request): RedirectResponse
    {
        ($this->upsertJournalEntry)($request->user(), $request->validated());

        return redirect()
            ->route('academy.widgets.three-good-things')
            ->with('status', __('hermes.academy.three_good_things_widget.saved'));
    }
}
