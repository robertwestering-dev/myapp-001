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
use Illuminate\Support\Carbon;

class AcademyWeeklyIntentionWidgetController extends Controller
{
    public function __construct(
        private readonly PositiveFoundationStrengthCatalog $strengthCatalog,
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
        $selectedStrengthKeys = collect($user->selected_strengths ?? [])
            ->filter(fn (mixed $strength): bool => is_string($strength) && $strength !== '')
            ->values()
            ->all();

        return view('academy.weekly-intention-widget', [
            'entry' => $user->journalEntries()
                ->whereDate('entry_date', $entryDate)
                ->where('entry_type', JournalEntry::TYPE_WEEKLY_INTENTION)
                ->first(),
            'entryDate' => $entryDate,
            'strengthOptions' => collect($this->strengthCatalog->options())
                ->filter(fn (array $option): bool => in_array($option['key'], $selectedStrengthKeys, true))
                ->values()
                ->all(),
        ]);
    }

    public function store(UpsertJournalEntryRequest $request): RedirectResponse
    {
        ($this->upsertJournalEntry)($request->user(), $request->validated());

        return redirect()
            ->route('academy.widgets.weekly-intention')
            ->with('status', __('hermes.academy.weekly_intention_widget.saved'));
    }
}
