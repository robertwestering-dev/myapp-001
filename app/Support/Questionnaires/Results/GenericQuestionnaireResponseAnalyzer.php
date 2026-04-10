<?php

namespace App\Support\Questionnaires\Results;

use App\Models\QuestionnaireResponse;

class GenericQuestionnaireResponseAnalyzer implements QuestionnaireResponseAnalyzer
{
    public function key(): string
    {
        return 'generic';
    }

    public function version(): int
    {
        return 1;
    }

    public function supports(QuestionnaireResponse $response): bool
    {
        return true;
    }

    public function analyze(QuestionnaireResponse $response): QuestionnaireAnalysisResult
    {
        $questionnaire = $response->organizationQuestionnaire->questionnaire;
        $questionCount = $questionnaire->questions->count();
        $answerCount = $response->answers->count();

        return new QuestionnaireAnalysisResult(
            analyzerKey: $this->key(),
            analyzerVersion: $this->version(),
            title: __('hermes.questionnaire.results.generic_title'),
            summary: __('hermes.questionnaire.results.generic_text', [
                'questionnaire' => $questionnaire->title,
                'answers' => $answerCount,
                'questions' => $questionCount,
            ]),
            profileKey: 'completed',
            profileLabel: __('hermes.questionnaire.results.completed_profile'),
            score: $answerCount,
            maxScore: $questionCount,
            recommendedDimensionKey: null,
            recommendedDimensionLabel: null,
            recommendedActionLabel: null,
            recommendedActionHref: null,
            dimensions: [],
        );
    }
}
