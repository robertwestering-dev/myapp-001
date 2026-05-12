<?php

namespace App\Http\Controllers;

use App\Actions\Journal\UpsertJournalEntry;
use App\Http\Requests\UpsertJournalEntryRequest;
use App\Models\JournalEntry;
use App\Models\User;
use App\Support\Academy\PositiveFoundationStrengthCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
        $user = $this->resolveProUser($request);

        if ($user instanceof RedirectResponse) {
            return $user;
        }

        ['selectedStrengthKeys' => $selectedStrengthKeys, 'strengthOptions' => $strengthOptions] = $this->journalFormContext($user);
        $activeMonth = $this->resolveTimelineMonth($request);
        $selectedTypes = $this->resolveTimelineTypes($request);
        $previousMonth = $this->resolveAdjacentTimelineMonth($user, $activeMonth, $selectedTypes, 'previous');
        $nextMonth = $this->resolveAdjacentTimelineMonth($user, $activeMonth, $selectedTypes, 'next');
        $entriesQuery = $this->timelineEntriesQuery($user, $activeMonth, $selectedTypes);
        $allEntriesQuery = JournalEntry::query()->forUser($user);
        $entryCounts = (clone $entriesQuery)
            ->selectRaw('entry_type, COUNT(*) as aggregate')
            ->groupBy('entry_type')
            ->pluck('aggregate', 'entry_type')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();
        $latestEntryDate = (clone $allEntriesQuery)->max('entry_date');

        return view('journal.index', [
            'entries' => $entriesQuery
                ->recent()
                ->paginate(config('app.per_page'))
                ->withQueryString(),
            'activeMonth' => $activeMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
            'entryTypes' => JournalEntry::entryTypeOptions(),
            'selectedTypes' => $selectedTypes,
            'selectedStrengthKeys' => $selectedStrengthKeys,
            'strengthOptions' => $strengthOptions,
            'entryCounts' => $entryCounts,
            'totalEntries' => array_sum($entryCounts),
            'latestEntryDate' => $latestEntryDate,
        ]);
    }

    public function timeline(Request $request): View|RedirectResponse
    {
        $user = $this->resolveProUser($request);

        if ($user instanceof RedirectResponse) {
            return $user;
        }

        $activeMonth = $this->resolveTimelineMonth($request);
        ['selectedStrengthKeys' => $selectedStrengthKeys, 'strengthOptions' => $strengthOptions] = $this->journalFormContext($user);
        $selectedTypes = $this->resolveTimelineTypes($request);
        $previousMonth = $this->resolveAdjacentTimelineMonth($user, $activeMonth, $selectedTypes, 'previous');
        $nextMonth = $this->resolveAdjacentTimelineMonth($user, $activeMonth, $selectedTypes, 'next');
        $entriesQuery = $this->timelineEntriesQuery($user, $activeMonth, $selectedTypes);

        return view('journal.timeline-page', [
            'entries' => $entriesQuery
                ->recent()
                ->paginate(config('app.per_page'))
                ->withQueryString(),
            'activeMonth' => $activeMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
            'entryTypes' => JournalEntry::entryTypeOptions(),
            'selectedTypes' => $selectedTypes,
            'selectedStrengthKeys' => $selectedStrengthKeys,
            'strengthOptions' => $strengthOptions,
        ]);
    }

    public function store(UpsertJournalEntryRequest $request): RedirectResponse
    {
        ($this->upsertJournalEntry)($request->user(), $request->validated());

        $returnRoute = $this->resolveReturnRoute($request);

        return redirect()
            ->route($returnRoute, $this->resolveReturnParameters($request, $returnRoute))
            ->with('status', __('hermes.journal.status.saved'));
    }

    public function update(UpsertJournalEntryRequest $request, JournalEntry $journalEntry): RedirectResponse
    {
        $entry = $this->resolveOwnedEntry($request, $journalEntry);

        $entry->update($this->upsertJournalEntry->payload($request->validated()));

        $returnRoute = $this->resolveReturnRoute($request);

        return redirect()
            ->route($returnRoute, $this->resolveReturnParameters($request, $returnRoute))
            ->with('status', __('hermes.journal.status.updated'));
    }

    public function destroy(Request $request, JournalEntry $journalEntry): RedirectResponse
    {
        $entry = $this->resolveOwnedEntry($request, $journalEntry);

        $entry->delete();

        $returnRoute = $this->resolveReturnRoute($request);

        return redirect()
            ->route($returnRoute, $this->resolveReturnParameters($request, $returnRoute))
            ->with('status', __('hermes.journal.status.deleted'));
    }

    protected function resolveOwnedEntry(Request $request, JournalEntry $journalEntry): JournalEntry
    {
        abort_unless($journalEntry->user_id === $request->user()->getKey(), 404);

        return $journalEntry;
    }

    protected function resolveProUser(Request $request): User|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isProUser()) {
            return redirect()
                ->route('pro-upgrade.show')
                ->with('status', __('hermes.journal.pro_required'));
        }

        return $user;
    }

    protected function resolveTimelineMonth(Request $request): CarbonImmutable
    {
        $month = $request->query('month');

        if (! is_string($month) || $month === '') {
            return CarbonImmutable::now()->startOfMonth();
        }

        try {
            return CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            return CarbonImmutable::now()->startOfMonth();
        }
    }

    /**
     * @return array{selectedStrengthKeys: array<int, string>, strengthOptions: array<int, array{key: string, label: string}>}
     */
    protected function journalFormContext(User $user): array
    {
        $selectedStrengthKeys = collect($user->selected_strengths ?? [])
            ->filter(fn (mixed $strength): bool => is_string($strength) && $strength !== '')
            ->values()
            ->all();

        $strengthOptions = collect($this->strengthCatalog->options())
            ->filter(fn (array $option): bool => in_array($option['key'], $selectedStrengthKeys, true))
            ->values()
            ->all();

        return [
            'selectedStrengthKeys' => $selectedStrengthKeys,
            'strengthOptions' => $strengthOptions,
        ];
    }

    protected function resolveReturnRoute(Request $request): string
    {
        $returnTo = $request->input('return_to');

        return in_array($returnTo, ['journal.index', 'journal.timeline'], true)
            ? $returnTo
            : 'journal.index';
    }

    /**
     * @return array<string, string>
     */
    protected function resolveReturnParameters(Request $request, string $returnRoute): array
    {
        if (! in_array($returnRoute, ['journal.index', 'journal.timeline'], true)) {
            return [];
        }

        $month = $request->input('return_month');
        $types = $request->input('return_types');

        $parameters = is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month) === 1
            ? ['month' => $month]
            : [];

        if (is_array($types)) {
            $allowedTypes = JournalEntry::entryTypeOptions();
            $parameters['types'] = collect($types)
                ->filter(fn (mixed $type): bool => is_string($type) && in_array($type, $allowedTypes, true))
                ->values()
                ->all();
        }

        return $parameters;
    }

    /**
     * @return array<int, string>
     */
    protected function resolveTimelineTypes(Request $request): array
    {
        $types = $request->query('types');

        if (! is_array($types)) {
            return [];
        }

        $allowedTypes = JournalEntry::entryTypeOptions();

        return collect($types)
            ->filter(fn (mixed $type): bool => is_string($type) && in_array($type, $allowedTypes, true))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $selectedTypes
     */
    protected function timelineEntriesQuery(User $user, CarbonImmutable $activeMonth, array $selectedTypes): Builder
    {
        $entriesQuery = JournalEntry::query()
            ->forUser($user)
            ->whereDate('entry_date', '>=', $activeMonth->startOfMonth()->toDateString())
            ->whereDate('entry_date', '<=', $activeMonth->endOfMonth()->toDateString());

        if ($selectedTypes !== []) {
            $entriesQuery->whereIn('entry_type', $selectedTypes);
        }

        return $entriesQuery;
    }

    /**
     * @param  array<int, string>  $selectedTypes
     */
    protected function resolveAdjacentTimelineMonth(User $user, CarbonImmutable $activeMonth, array $selectedTypes, string $direction): ?CarbonImmutable
    {
        $entriesQuery = JournalEntry::query()->forUser($user);

        if ($selectedTypes !== []) {
            $entriesQuery->whereIn('entry_type', $selectedTypes);
        }

        if ($direction === 'previous') {
            $entriesQuery
                ->whereDate('entry_date', '<', $activeMonth->startOfMonth()->toDateString())
                ->orderByDesc('entry_date');
        } else {
            $entriesQuery
                ->whereDate('entry_date', '>', $activeMonth->endOfMonth()->toDateString())
                ->whereDate('entry_date', '<=', now()->toDateString())
                ->orderBy('entry_date');
        }

        $entryDate = $entriesQuery->value('entry_date');

        if ($entryDate === null) {
            return null;
        }

        return CarbonImmutable::parse($entryDate)->startOfMonth();
    }
}
