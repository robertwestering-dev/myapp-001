<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\ProvidesOrganizationOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationQuestionnaireRequest;
use App\Http\Requests\Admin\UpdateOrganizationQuestionnaireRequest;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrganizationQuestionnaireController extends Controller
{
    use ProvidesOrganizationOptions;

    public function index(Request $request, Questionnaire $questionnaire): View
    {
        /** @var User $actor */
        $actor = $request->user();

        $linkages = $questionnaire->organizationQuestionnaires()
            ->with('organization:org_id,naam')
            ->when(! $actor->isAdmin(), fn ($query) => $query->where('org_id', $actor->org_id))
            ->orderBy('org_id')
            ->get();

        return view('admin.organization-questionnaires.availability-index', [
            'questionnaire' => $questionnaire,
            'linkages' => $linkages,
            'canManageLibrary' => $actor->isAdmin(),
        ]);
    }

    public function create(Request $request, Questionnaire $questionnaire): View
    {
        /** @var User $actor */
        $actor = $request->user();

        return view('admin.organization-questionnaires.form', [
            'availability' => new OrganizationQuestionnaire(['is_active' => true]),
            'additionalOrganizations' => $this->additionalOrganizationOptions($actor, $questionnaire),
            'intro' => 'Stel deze questionnaire beschikbaar per organisatie en bepaal per koppeling de periode en activatie.',
            'isEditing' => false,
            'linkedOrganizations' => $this->linkedOrganizationNames($actor, $questionnaire),
            'organizations' => $this->organizationOptions($actor),
            'questionnaire' => $questionnaire,
            'submitLabel' => 'Beschikbaarheid opslaan',
            'title' => __('hermes.admin.form_titles.new_organization_questionnaire'),
        ]);
    }

    public function store(StoreOrganizationQuestionnaireRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $request->validated();

        foreach ($request->selectedOrganizationConfigurations() as $configuration) {
            OrganizationQuestionnaire::query()->updateOrCreate(
                [
                    'questionnaire_id' => $questionnaire->id,
                    'org_id' => $configuration['org_id'],
                ],
                [
                    'available_from' => $configuration['available_from'],
                    'available_until' => $configuration['available_until'],
                    'is_active' => $configuration['is_active'],
                ],
            );
        }

        return redirect()
            ->route('admin.questionnaires.availability.index', $questionnaire)
            ->with('status', __('hermes.admin.organization_questionnaires.saved'));
    }

    public function edit(
        Request $request,
        Questionnaire $questionnaire,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): View {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($organizationQuestionnaire->questionnaire_id === $questionnaire->id, 404);
        abort_unless($actor->canManageOrganization($organizationQuestionnaire->org_id), 403);

        return view('admin.organization-questionnaires.form', [
            'availability' => $organizationQuestionnaire,
            'additionalOrganizations' => $this->additionalOrganizationOptions($actor, $questionnaire, $organizationQuestionnaire),
            'intro' => 'Werk de beschikbaarheid voor deze organisatie bij.',
            'isEditing' => true,
            'linkedOrganizations' => $this->linkedOrganizationNames($actor, $questionnaire),
            'organizations' => $this->organizationOptions($actor),
            'questionnaire' => $questionnaire,
            'submitLabel' => 'Wijzigingen opslaan',
            'title' => __('hermes.admin.form_titles.edit_organization_questionnaire'),
        ]);
    }

    public function update(
        UpdateOrganizationQuestionnaireRequest $request,
        Questionnaire $questionnaire,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): RedirectResponse {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($organizationQuestionnaire->questionnaire_id === $questionnaire->id, 404);
        abort_unless($actor->canManageOrganization($organizationQuestionnaire->org_id), 403);

        $attributes = $request->validated();

        $organizationQuestionnaire->update([
            'available_from' => $attributes['available_from'] ?? null,
            'available_until' => $attributes['available_until'] ?? null,
            'is_active' => $attributes['is_active'],
        ]);

        return redirect()
            ->route('admin.questionnaires.availability.index', $questionnaire)
            ->with('status', __('hermes.admin.organization_questionnaires.updated'));
    }

    public function destroy(
        Request $request,
        Questionnaire $questionnaire,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): RedirectResponse {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($organizationQuestionnaire->questionnaire_id === $questionnaire->id, 404);
        abort_unless($actor->canManageOrganization($organizationQuestionnaire->org_id), 403);

        $organizationQuestionnaire->delete();

        return redirect()
            ->route('admin.questionnaires.availability.index', $questionnaire)
            ->with('status', __('hermes.admin.organization_questionnaires.deleted'));
    }

    public function toggle(
        Request $request,
        Questionnaire $questionnaire,
        Organization $organization,
    ): RedirectResponse {
        /** @var User $actor */
        $actor = $request->user();

        abort_unless($actor->canManageOrganization($organization->org_id), 403);

        $availability = OrganizationQuestionnaire::query()->firstOrNew([
            'questionnaire_id' => $questionnaire->id,
            'org_id' => $organization->org_id,
        ]);

        $willBeActive = ! $availability->exists || ! $availability->is_active;

        if ($willBeActive && ! $questionnaire->is_active) {
            return back()->withErrors([
                'availability' => __('hermes.questionnaires.availability_requires_active_questionnaire'),
            ]);
        }

        if (! $availability->exists) {
            $availability->fill([
                'available_from' => null,
                'available_until' => null,
                'is_active' => true,
            ])->save();

            return redirect()
                ->route('admin.questionnaires.availability.index', $questionnaire)
                ->with('status', __('hermes.admin.organization_questionnaires.saved'));
        }

        $availability->update([
            'is_active' => ! $availability->is_active,
        ]);

        return redirect()
            ->route('admin.questionnaires.availability.index', $questionnaire)
            ->with('status', __('hermes.admin.organization_questionnaires.updated'));
    }

    /**
     * @return array<int, string>
     */
    protected function additionalOrganizationOptions(
        User $actor,
        Questionnaire $questionnaire,
        ?OrganizationQuestionnaire $currentAvailability = null,
    ): array {
        $linkedOrganizationIds = $questionnaire->organizationQuestionnaires()
            ->when(
                ! $actor->isAdmin(),
                fn ($query) => $query->where('org_id', $actor->org_id),
            )
            ->pluck('org_id')
            ->filter()
            ->map(fn (int|string $organizationId): int => (int) $organizationId)
            ->all();

        return collect($this->organizationOptions($actor))
            ->reject(fn (string $name, int $organizationId): bool => in_array($organizationId, $linkedOrganizationIds, true))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function linkedOrganizationNames(User $actor, Questionnaire $questionnaire): array
    {
        return $questionnaire->organizationQuestionnaires()
            ->with('organization:org_id,naam')
            ->when(
                ! $actor->isAdmin(),
                fn ($query) => $query->where('org_id', $actor->org_id),
            )
            ->get()
            ->pluck('organization.naam')
            ->filter()
            ->values()
            ->all();
    }
}
