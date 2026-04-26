<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionnaireCategoryRequest;
use App\Http\Requests\Admin\UpdateQuestionnaireCategoryRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionnaireCategoryController extends Controller
{
    public function create(Request $request, Questionnaire $questionnaire): View
    {
        $this->authorize('manage', Questionnaire::class);

        return view('admin.questionnaire-categories.form', [
            'questionnaire' => $questionnaire,
            'title' => __('hermes.admin.form_titles.new_questionnaire_category'),
            'intro' => 'Voeg een categorie toe aan deze questionnaire.',
            'submitLabel' => 'Categorie opslaan',
            'category' => new QuestionnaireCategory,
            'isEditing' => false,
        ]);
    }

    public function store(StoreQuestionnaireCategoryRequest $request, Questionnaire $questionnaire): RedirectResponse
    {
        $this->authorize('manage', Questionnaire::class);

        $questionnaire->categories()->create($request->validated());

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaire_categories.created'));
    }

    public function edit(Request $request, Questionnaire $questionnaire, QuestionnaireCategory $category): View
    {
        $this->authorize('manage', Questionnaire::class);
        abort_unless($category->questionnaire_id === $questionnaire->id, 404);

        return view('admin.questionnaire-categories.form', [
            'questionnaire' => $questionnaire,
            'title' => __('hermes.admin.form_titles.edit_questionnaire_category'),
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
        $this->authorize('manage', Questionnaire::class);
        abort_unless($category->questionnaire_id === $questionnaire->id, 404);

        $category->update($request->validated());

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaire_categories.updated'));
    }

    public function destroy(Request $request, Questionnaire $questionnaire, QuestionnaireCategory $category): RedirectResponse
    {
        $this->authorize('manage', Questionnaire::class);
        abort_unless($category->questionnaire_id === $questionnaire->id, 404);

        $category->delete();

        return redirect()
            ->route('admin.questionnaires.edit', $questionnaire)
            ->with('status', __('hermes.admin.questionnaire_categories.deleted'));
    }
}
