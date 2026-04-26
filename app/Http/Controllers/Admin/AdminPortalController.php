<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Translations\ManageHermesTranslations;
use App\Http\Controllers\Controller;
use App\Models\AcademyCourse;
use App\Models\AdminActivityLog;
use App\Models\BlogPost;
use App\Models\MediaAsset;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use App\Services\SpotlightQuestionnaireService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminPortalController extends Controller
{
    public function __construct(private readonly SpotlightQuestionnaireService $spotlightService) {}

    public function index(Request $request, ManageHermesTranslations $translations): View
    {
        /** @var User $actor */
        $actor = $request->user();

        return view('admin-portal', [
            'actor' => $actor,
            'canManageLibrary' => $actor->isAdmin(),
            'lead' => $actor->isAdmin()
                ? __('hermes.admin.portal.lead_admin', ['email' => $actor->email])
                : __('hermes.admin.portal.lead_manager', ['email' => $actor->email]),
            'spotlightQuestionnaires' => $this->spotlightService->get($actor, withCounts: true),
            ...$this->dashboardCounts($actor, $translations),
        ]);
    }

    /**
     * @return array<string, int>
     */
    protected function dashboardCounts(User $actor, ManageHermesTranslations $translations): array
    {
        return [
            'questionnaireCount' => Questionnaire::query()->count(),
            'academyCourseCount' => AcademyCourse::query()->count(),
            'blogPostCount' => BlogPost::query()->count(),
            'mediaAssetCount' => MediaAsset::query()->count(),
            'translationCount' => $translations->all()->count(),
            'auditLogCount' => AdminActivityLog::query()->count(),
            'scopedAvailabilityCount' => OrganizationQuestionnaire::query()
                ->when(! $actor->isAdmin(), fn (Builder $query) => $query->where('org_id', $actor->org_id))
                ->count(),
        ];
    }
}
