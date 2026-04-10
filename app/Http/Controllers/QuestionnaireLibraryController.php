<?php

namespace App\Http\Controllers;

use App\Support\Questionnaires\AvailableQuestionnaireCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionnaireLibraryController extends Controller
{
    public function __invoke(Request $request, AvailableQuestionnaireCatalog $catalog): View
    {
        $user = $request->user();
        $localeContext = $catalog->localeContext($request, $user);
        $availableQuestionnaires = $catalog->forUser($user, $localeContext['locale']);

        return view('questionnaires.index', [
            'availableQuestionnaires' => $availableQuestionnaires,
            'activeQuestionnaireLocale' => $localeContext['locale'],
            'activeQuestionnaireLocaleLabel' => $localeContext['label'],
            'activeQuestionnaireLocaleSource' => $localeContext['source'],
        ]);
    }
}
