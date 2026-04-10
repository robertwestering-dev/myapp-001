<?php

namespace App\Services;

use App\Actions\Questionnaires\SyncAdaptabilityAceQuestionnaire;
use App\Actions\Questionnaires\SyncDigitalResilienceQuickScanQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SpotlightQuestionnaireService
{
    /**
     * The titles of the spotlight questionnaires, in display order.
     *
     * @var array<int, string>
     */
    public static array $titles = [
        SyncAdaptabilityAceQuestionnaire::TITLE,
        SyncDigitalResilienceQuickScanQuestionnaire::TITLE,
    ];

    /**
     * Return spotlight questionnaires in canonical order, optionally scoped to an actor.
     *
     * Counts are scoped to the actor's organization when the actor is not an admin.
     * Pass $withCounts = true to include categories_count, questions_count, and
     * scoped_organization_questionnaires_count on each model.
     *
     * @return Collection<int, Questionnaire>
     */
    public function get(User $actor, bool $withCounts = false): Collection
    {
        $query = Questionnaire::query()->whereIn('title', self::$titles);

        if ($withCounts) {
            $query->withCount([
                'categories',
                'questions',
                'organizationQuestionnaires as scoped_organization_questionnaires_count' => function (Builder $query) use ($actor): void {
                    if (! $actor->isAdmin()) {
                        $query->where('org_id', $actor->org_id);
                    }
                },
            ]);
        }

        $questionnaires = $query->orderBy('title')->get();

        return collect(self::$titles)
            ->map(fn (string $title): ?Questionnaire => $questionnaires->firstWhere('title', $title))
            ->filter()
            ->values();
    }

    /**
     * Return a minimal spotlight collection for filter/link panels (no counts needed).
     *
     * @return Collection<int, Questionnaire>
     */
    public function getForFilters(): Collection
    {
        $questionnaires = Questionnaire::query()
            ->whereIn('title', self::$titles)
            ->get(['id', 'title', 'description']);

        return collect(self::$titles)
            ->map(fn (string $title): ?Questionnaire => $questionnaires->firstWhere('title', $title))
            ->filter()
            ->values();
    }
}
