<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\ProvidesOrganizationOptions;
use App\Enums\AuditAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionnaireRequest;
use App\Http\Requests\Admin\UpdateQuestionnaireRequest;
use App\Models\Questionnaire;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SpotlightQuestionnaireService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    use ProvidesOrganizationOptions;

    public function __construct(
        private readonly SpotlightQuestionnaireService $spotlightService,
        private readonly AuditLogger $audit,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $actor */
        $actor = $request->user();

        $questionnaires = Questionnaire::query()
            ->withCount(['categories', 'questions', 'organizationQuestionnaires'])
            ->with([
                'organizationQuestionnaires' => function ($query) use ($actor): void {
                    $query->with('organization:org_id,naam');

                    if (! $actor->isAdmin()) {
                        $query->where('org_id', $actor->org_id);
                    }
                },
            ])
            ->orderBy('title')
            ->paginate(config('app.per_page'));

        return view('admin.questionnaires.index', [
            'canManageLibrary' => $actor->isAdmin(),
            'organizationOptions' => $this->organizationOptions($actor),
            'questionnaires' => $questionnaires,
            'spotlightQuestionnaires' => $this->spotlightService->get($actor, withCounts: true),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('manage', Questionnaire::class);

        return view('admin.questionnaires.form', [
            'title' => __('hermes.admin.form_titles.new_questionnaire'),
            'intro' => 'Stel een nieuwe standaardvragenlijst samen met categorieen en vragen.',
            'submitLabel' => 'Questionnaire opslaan',
            'questionnaire' => new Questionnaire([
                'is_active' => true,
                'locale' => config('locales.primary'),
            ]),
            'isEditing' => false,
        ]);
    }

    public function store(StoreQuestionnaireRequest $request): RedirectResponse
    {
        $questionnaire = Questionnaire::create($request->validated());

        $this->audit->log(AuditAction::QuestionnaireCreated, "Questionnaire aangemaakt: {$questionnaire->title}", $questionnaire);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaires.created'));
    }

    public function edit(Request $request, Questionnaire $questionnaire): View
    {
        $this->authorize('manage', Questionnaire::class);

        $questionnaire->load([
            'categories.questions' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return view('admin.questionnaires.form', [
            'title' => __('hermes.admin.form_titles.edit_questionnaire'),
            'intro' => 'Werk de basisgegevens bij en beheer de opbouw van categorieen en vragen.',
            'submitLabel' => 'Wijzigingen opslaan',
            'questionnaire' => $questionnaire,
            'isEditing' => true,
        ]);
    }

    public function update(UpdateQuestionnaireRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        $questionnaire->update($request->validated());

        $this->audit->log(AuditAction::QuestionnaireUpdated, "Questionnaire bijgewerkt: {$questionnaire->title}", $questionnaire);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaires.updated'));
    }

    public function toggle(Request $request, Questionnaire $questionnaire): RedirectResponse
    {
        $this->authorize('manage', Questionnaire::class);

        $questionnaire->update(['is_active' => ! $questionnaire->is_active]);

        $action = $questionnaire->is_active ? AuditAction::QuestionnaireActivated : AuditAction::QuestionnaireDeactivated;
        $this->audit->log($action, "Questionnaire {$questionnaire->title} ".($questionnaire->is_active ? 'geactiveerd' : 'gedeactiveerd'), $questionnaire);

        return redirect()
            ->route('admin.questionnaires.index')
            ->with('status', $questionnaire->is_active
                ? __('hermes.admin.questionnaires.activated')
                : __('hermes.admin.questionnaires.deactivated')
            );
    }

    public function destroy(Request $request, Questionnaire $questionnaire): RedirectResponse
    {
        $this->authorize('manage', Questionnaire::class);

        $this->audit->log(AuditAction::QuestionnaireDeleted, "Questionnaire verwijderd: {$questionnaire->title}", $questionnaire);

        $questionnaire->delete();

        return redirect()
            ->route('admin.questionnaires.index')
            ->with('status', __('hermes.admin.questionnaires.deleted'));
    }
}
