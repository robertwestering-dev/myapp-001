<?php

namespace App\Support\Questionnaires\Results;

use App\Models\QuestionnaireResponse;

interface QuestionnaireResponseAnalyzer
{
    public function key(): string;

    public function version(): int;

    public function supports(QuestionnaireResponse $response): bool;

    public function analyze(QuestionnaireResponse $response): QuestionnaireAnalysisResult;
}
