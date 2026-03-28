<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationQuestionnaireRequest;
use App\Http\Requests\Admin\UpdateOrganizationQuestionnaireRequest;
use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class OrganizationQuestionnaireController extends Controller
{
    public function create(Questionnaire $questionnaire): View
    {
        /** @var User $actor */
        $actor = request()->user();

        return view('admin.organization-questionnaires.form', [
            'availability' => new OrganizationQuestionnaire(['is_active' => true]),
            'intro' => 'Stel deze standaardquestionnaire beschikbaar voor een organisatie.',
            'isEditing' => false,
            'organizations' => $this->organizationOptions($actor),
            'questionnaire' => $questionnaire,
            'submitLabel' => 'Beschikbaarheid opslaan',
            'title' => 'Questionnaire beschikbaar stellen',
        ]);
    }

    public function store(StoreOrganizationQuestionnaireRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $attributes = $request->validated();

        if (! $actor->isAdmin()) {
            $attributes['org_id'] = $actor->org_id;
        }

        $attributes['is_active'] = $request->boolean('is_active');

        OrganizationQuestionnaire::query()->updateOrCreate(
            [
                'questionnaire_id' => $questionnaire->id,
                'org_id' => $attributes['org_id'],
            ],
            [
                'available_from' => $attributes['available_from'] ?? null,
                'available_until' => $attributes['available_until'] ?? null,
                'is_active' => $attributes['is_active'],
            ],
        );

        return redirect()
            ->route('admin.questionnaires.index')
            ->with('status', 'Beschikbaarheid succesvol opgeslagen.');
    }

    public function edit(
        Questionnaire $questionnaire,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): View {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($organizationQuestionnaire->questionnaire_id === $questionnaire->id, 404);
        abort_unless($actor->canManageOrganization($organizationQuestionnaire->org_id), 403);

        return view('admin.organization-questionnaires.form', [
            'availability' => $organizationQuestionnaire,
            'intro' => 'Werk de beschikbaarheid voor deze organisatie bij.',
            'isEditing' => true,
            'organizations' => $this->organizationOptions($actor),
            'questionnaire' => $questionnaire,
            'submitLabel' => 'Wijzigingen opslaan',
            'title' => 'Beschikbaarheid wijzigen',
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

        if (! $actor->isAdmin()) {
            $attributes['org_id'] = $actor->org_id;
        }

        $attributes['is_active'] = $request->boolean('is_active');

        $organizationQuestionnaire->update($attributes);

        return redirect()
            ->route('admin.questionnaires.index')
            ->with('status', 'Beschikbaarheid succesvol bijgewerkt.');
    }

    public function destroy(
        Questionnaire $questionnaire,
        OrganizationQuestionnaire $organizationQuestionnaire
    ): RedirectResponse {
        /** @var User $actor */
        $actor = request()->user();

        abort_unless($organizationQuestionnaire->questionnaire_id === $questionnaire->id, 404);
        abort_unless($actor->canManageOrganization($organizationQuestionnaire->org_id), 403);

        $organizationQuestionnaire->delete();

        return redirect()
            ->route('admin.questionnaires.index')
            ->with('status', 'Beschikbaarheid succesvol verwijderd.');
    }

    /**
     * @return array<int, string>
     */
    protected function organizationOptions(User $actor): array
    {
        return Organization::query()
            ->when(! $actor->isAdmin(), function (Builder $query) use ($actor): void {
                $query->where('org_id', $actor->org_id);
            })
            ->orderBy('naam')
            ->pluck('naam', 'org_id')
            ->all();
    }
}
