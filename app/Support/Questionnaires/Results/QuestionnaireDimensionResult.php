<?php

namespace App\Support\Questionnaires\Results;

/**
 * @phpstan-type QuestionnaireDimensionResultArray array{
 *     key: string,
 *     label: string,
 *     score: int|null,
 *     max_score: int|null,
 *     status_key: string|null,
 *     status_label: string|null,
 *     summary: string,
 *     action_label: string|null,
 *     action_href: string|null,
 *     is_recommended: bool
 * }
 */
readonly class QuestionnaireDimensionResult
{
    public function __construct(
        public string $key,
        public string $label,
        public ?int $score,
        public ?int $maxScore,
        public ?string $statusKey,
        public ?string $statusLabel,
        public string $summary,
        public ?string $actionLabel = null,
        public ?string $actionHref = null,
        public bool $isRecommended = false,
    ) {}

    /**
     * @return QuestionnaireDimensionResultArray
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'score' => $this->score,
            'max_score' => $this->maxScore,
            'status_key' => $this->statusKey,
            'status_label' => $this->statusLabel,
            'summary' => $this->summary,
            'action_label' => $this->actionLabel,
            'action_href' => $this->actionHref,
            'is_recommended' => $this->isRecommended,
        ];
    }

    /**
     * @param  QuestionnaireDimensionResultArray  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            key: (string) ($data['key'] ?? ''),
            label: (string) ($data['label'] ?? ''),
            score: isset($data['score']) ? (int) $data['score'] : null,
            maxScore: isset($data['max_score']) ? (int) $data['max_score'] : null,
            statusKey: isset($data['status_key']) ? (string) $data['status_key'] : null,
            statusLabel: isset($data['status_label']) ? (string) $data['status_label'] : null,
            summary: (string) ($data['summary'] ?? ''),
            actionLabel: isset($data['action_label']) ? (string) $data['action_label'] : null,
            actionHref: isset($data['action_href']) ? (string) $data['action_href'] : null,
            isRecommended: (bool) ($data['is_recommended'] ?? false),
        );
    }
}
