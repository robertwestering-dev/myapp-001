<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionnaireCategoryRequest;
use App\Http\Requests\Admin\UpdateQuestionnaireCategoryRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class QuestionnaireCategoryController extends Controller
{
    public function create(Questionnaire $questionnaire): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('admin.questionnaire-categories.form', [
            'questionnaire' => $questionnaire,
            'title' => 'Nieuwe categorie',
            'intro' => 'Voeg een categorie toe aan deze questionnaire.',
            'submitLabel' => 'Categorie opslaan',
            'category' => new QuestionnaireCategory,
            'isEditing' => false,
        ]);
    }

    public function store(StoreQuestionnaireCategoryRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $questionnaire->categories()->create($request->validated());

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Categorie succesvol toegevoegd.');
    }

    public function edit(Questionnaire $questionnaire, QuestionnaireCategory $category): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);
        abort_unless($category->questionnaire_id === $questionnaire->id, 404);

        return view('admin.questionnaire-categories.form', [
            'questionnaire' => $questionnaire,
            'title' => 'Categorie wijzigen',
            'intro' => 'Werk deze categorie bij binnen de questionnaire.',
            'submitLabel' => 'Wijzigingen opslaan',
            'category' => $category,
            'isEditing' => true,
        ]);
    }

    public function update(
        UpdateQuestionnaireCategoryRequest $request,
        Questionnaire $questionnaire,
        QuestionnaireCategory $category
    ): RedirectResponse {
        abort_unless($request->user()?->isAdmin(), 403);
        abort_unless($category->questionnaire_id === $questionnaire->id, 404);

        $category->update($request->validated());

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Categorie succesvol bijgewerkt.');
    }

    public function destroy(Questionnaire $questionnaire, QuestionnaireCategory $category): RedirectResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403);
        abort_unless($category->questionnaire_id === $questionnaire->id, 404);

        $category->delete();

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', 'Categorie succesvol verwijderd.');
    }
}
