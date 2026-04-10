<?php

namespace App\Support\Questionnaires\Results;

/**
 * @phpstan-type QuestionnaireAnalysisResultArray array{
 *     analyzer_key: string,
 *     analyzer_version: int,
 *     title: string,
 *     summary: string,
 *     profile_key: string,
 *     profile_label: string,
 *     score: int|null,
 *     max_score: int|null,
 *     recommended_dimension_key: string|null,
 *     recommended_dimension_label: string|null,
 *     recommended_action_label: string|null,
 *     recommended_action_href: string|null,
 *     dimensions: array<int, array{
 *         key: string,
 *         label: string,
 *         score: int|null,
 *         max_score: int|null,
 *         status_key: string|null,
 *         status_label: string|null,
 *         summary: string,
 *         action_label: string|null,
 *         action_href: string|null,
 *         is_recommended: bool
 *     }>
 * }
 */
readonly class QuestionnaireAnalysisResult
{
    /**
     * @param  array<int, QuestionnaireDimensionResult>  $dimensions
     */
    public function __construct(
        public string $analyzerKey,
        public int $analyzerVersion,
        public string $title,
        public string $summary,
        public string $profileKey,
        public string $profileLabel,
        public ?int $score,
        public ?int $maxScore,
        public ?string $recommendedDimensionKey,
        public ?string $recommendedDimensionLabel,
        public ?string $recommendedActionLabel,
        public ?string $recommendedActionHref,
        public array $dimensions = [],
    ) {}

    /**
     * @return QuestionnaireAnalysisResultArray
     */
    public function toArray(): array
    {
        return [
            'analyzer_key' => $this->analyzerKey,
            'analyzer_version' => $this->analyzerVersion,
            'title' => $this->title,
            'summary' => $this->summary,
            'profile_key' => $this->profileKey,
            'profile_label' => $this->profileLabel,
            'score' => $this->score,
            'max_score' => $this->maxScore,
            'recommended_dimension_key' => $this->recommendedDimensionKey,
            'recommended_dimension_label' => $this->recommendedDimensionLabel,
            'recommended_action_label' => $this->recommendedActionLabel,
            'recommended_action_href' => $this->recommendedActionHref,
            'dimensions' => array_map(
                static fn (QuestionnaireDimensionResult $dimension): array => $dimension->toArray(),
                $this->dimensions,
            ),
        ];
    }

    /**
     * @param  QuestionnaireAnalysisResultArray  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            analyzerKey: (string) ($data['analyzer_key'] ?? ''),
            analyzerVersion: (int) ($data['analyzer_version'] ?? 1),
            title: (string) ($data['title'] ?? ''),
            summary: (string) ($data['summary'] ?? ''),
            profileKey: (string) ($data['profile_key'] ?? ''),
            profileLabel: (string) ($data['profile_label'] ?? ''),
            score: isset($data['score']) ? (int) $data['score'] : null,
            maxScore: isset($data['max_score']) ? (int) $data['max_score'] : null,
            recommendedDimensionKey: isset($data['recommended_dimension_key']) ? (string) $data['recommended_dimension_key'] : null,
            recommendedDimensionLabel: isset($data['recommended_dimension_label']) ? (string) $data['recommended_dimension_label'] : null,
            recommendedActionLabel: isset($data['recommended_action_label']) ? (string) $data['recommended_action_label'] : null,
            recommendedActionHref: isset($data['recommended_action_href']) ? (string) $data['recommended_action_href'] : null,
            dimensions: array_map(
                static fn (array $dimension): QuestionnaireDimensionResult => QuestionnaireDimensionResult::fromArray($dimension),
                $data['dimensions'] ?? [],
            ),
        );
    }
}
