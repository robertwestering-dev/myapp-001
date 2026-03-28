<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionnaireRequest;
use App\Http\Requests\Admin\UpdateQuestionnaireRequest;
use App\Models\Questionnaire;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
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
            ->paginate(15);

        return view('admin.questionnaires.index', [
            'canManageLibrary' => $actor->isAdmin(),
            'questionnaires' => $questionnaires,
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.questionnaires.form', [
            'title' => 'Nieuwe questionnaire',
            'intro' => 'Stel een nieuwe standaardvragenlijst samen met categorieen en vragen.',
            'submitLabel' => 'Questionnaire opslaan',
            'questionnaire' => new Questionnaire(['is_active' => true]),
            'isEditing' => false,
        ]);
    }

    public function store(StoreQuestionnaireRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $attributes = $request->validated();
        $attributes['is_active'] = $request->boolean('is_active');

        $questionnaire = Questionnaire::create($attributes);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Questionnaire succesvol toegevoegd. Voeg nu categorieen en vragen toe.');
    }

    public function edit(Questionnaire $questionnaire): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $questionnaire->load([
            'categories.questions' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        return view('admin.questionnaires.form', [
            'title' => 'Questionnaire wijzigen',
            'intro' => 'Werk de basisgegevens bij en beheer de opbouw van categorieen en vragen.',
            'submitLabel' => 'Wijzigingen opslaan',
            'questionnaire' => $questionnaire,
            'isEditing' => true,
        ]);
    }

    public function update(UpdateQuestionnaireRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $attributes = $request->validated();
        $attributes['is_active'] = $request->boolean('is_active');

        $questionnaire->update($attributes);

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Questionnaire succesvol bijgewerkt.');
    }

    public function destroy(Questionnaire $questionnaire): RedirectResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        $questionnaire->delete();

        return redirect()
            ->route('admin.questionnaires.index')
            ->with('status', 'Questionnaire succesvol verwijderd.');
    }
}
