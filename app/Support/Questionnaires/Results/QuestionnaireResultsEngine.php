<?php

namespace App\Support\Questionnaires\Results;

use App\Models\QuestionnaireResponse;

class QuestionnaireResultsEngine
{
    public function __construct(
        private readonly PositiveFoundationQuestionnaireResponseAnalyzer $positiveFoundationAnalyzer,
        private readonly DigitalMirrorQuestionnaireResponseAnalyzer $digitalMirrorAnalyzer,
        private readonly GenericQuestionnaireResponseAnalyzer $genericAnalyzer,
    ) {}

    public function forResponse(QuestionnaireResponse $response): QuestionnaireAnalysisResult
    {
        $response->loadMissing([
            'answers.question.category',
            'organizationQuestionnaire.questionnaire.categories.questions',
        ]);

        if ($this->hasCurrentSnapshot($response)) {
            /** @var array<string, mixed> $snapshot */
            $snapshot = $response->analysis_snapshot;

            return QuestionnaireAnalysisResult::fromArray($snapshot);
        }

        return $this->analyzeAndStore($response);
    }

    public function analyzeAndStore(QuestionnaireResponse $response): QuestionnaireAnalysisResult
    {
        $analysis = $this->analyze($response);

        $response->forceFill([
            'analysis_snapshot' => $analysis->toArray(),
        ])->save();

        return $analysis;
    }

    private function analyze(QuestionnaireResponse $response): QuestionnaireAnalysisResult
    {
        foreach ($this->analyzers() as $analyzer) {
            if ($analyzer->supports($response)) {
                return $analyzer->analyze($response);
            }
        }

        return $this->genericAnalyzer->analyze($response);
    }

    /**
     * @return array<int, QuestionnaireResponseAnalyzer>
     */
    private function analyzers(): array
    {
        return [
            $this->positiveFoundationAnalyzer,
            $this->digitalMirrorAnalyzer,
        ];
    }

    private function hasCurrentSnapshot(QuestionnaireResponse $response): bool
    {
        if (! is_array($response->analysis_snapshot)) {
            return false;
        }

        $currentAnalyzer = collect($this->analyzers())
            ->first(fn (QuestionnaireResponseAnalyzer $analyzer): bool => $analyzer->supports($response))
            ?? $this->genericAnalyzer;

        return ($response->analysis_snapshot['analyzer_key'] ?? null) === $currentAnalyzer->key()
            && (int) ($response->analysis_snapshot['analyzer_version'] ?? 0) === $currentAnalyzer->version();
    }
}
