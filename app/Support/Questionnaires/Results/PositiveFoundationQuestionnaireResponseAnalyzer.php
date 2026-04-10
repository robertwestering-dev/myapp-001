<?php

namespace App\Support\Questionnaires\Results;

use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;
use App\Models\User;

class PositiveFoundationQuestionnaireResponseAnalyzer implements QuestionnaireResponseAnalyzer
{
    /**
     * @var array<int, string>
     */
    private const PILLAR_PRIORITY = [
        1 => 'P',
        2 => 'E',
        3 => 'M',
        4 => 'R',
        5 => 'A',
    ];

    /**
     * @var array<int, array{
     *     code: string,
     *     labels: array<string, string>,
     *     advice: array<string, string>,
     *     module: array{free: string, pro: string}
     * }>
     */
    private const PILLAR_CONTENT = [
        1 => [
            'code' => 'P',
            'labels' => [
                'strong' => 'Sterk fundament',
                'partial' => 'Gedeeltelijk fundament',
                'fragile' => 'Fragiel fundament',
            ],
            'advice' => [
                'strong' => 'Je ervaart regelmatig plezier, rust en vertrouwen. Dit is een krachtig anker - ook bij digitale druk. Ga naar Laag 2.',
                'partial' => 'Je heeft positieve momenten, maar ze zijn niet consistent aanwezig. De e-learning helpt je die momenten bewuster op te zoeken. Start met module 1.',
                'fragile' => 'Positieve emotie is schaars op dit moment. Begin hier - niet als verplichting, maar als investering in jezelf. Module 1 van de e-learning is jouw startpunt.',
            ],
            'module' => [
                'free' => 'Start met module 1: Introductie PERMA en welbevinden →',
                'pro' => 'Start met module 1: Introductie PERMA en welbevinden →',
            ],
        ],
        2 => [
            'code' => 'E',
            'labels' => [
                'strong' => 'Sterk fundament',
                'partial' => 'Gedeeltelijk fundament',
                'fragile' => 'Fragiel fundament',
            ],
            'advice' => [
                'strong' => 'Je bent regelmatig in je element en weet wat je sterke kanten zijn. Bouw hierop verder in Laag 2.',
                'partial' => 'Je raakt soms in flow, maar niet structureel. Module 3 helpt je bewuster jouw sterke kanten in te zetten.',
                'fragile' => 'Je sterke kanten blijven onbenut. Dat is een gemiste kans - en het is oplosbaar. Start met module 2: sterke kanten ontdekken.',
            ],
            'module' => [
                'free' => 'Start met module 2: Sterke kanten ontdekken →',
                'pro' => 'Start met module 3: Zingeving en betrokkenheid verdiepen →',
            ],
        ],
        3 => [
            'code' => 'R',
            'labels' => [
                'strong' => 'Sterk fundament',
                'partial' => 'Gedeeltelijk fundament',
                'fragile' => 'Fragiel fundament',
            ],
            'advice' => [
                'strong' => 'Je relaties zijn een echte bron van kracht. Gebruik ze ook als anker bij digitale verandering.',
                'partial' => 'Je heeft relaties, maar de diepgang of steun kan groeien. De e-learning helpt je verbinding bewuster te voeden.',
                'fragile' => 'Verbinding met anderen is mager. Dat maakt alles zwaarder. Module 1 van de e-learning helpt je hier een begin mee te maken - zonder drempel.',
            ],
            'module' => [
                'free' => 'Start met module 1: Introductie PERMA en welbevinden →',
                'pro' => 'Start met een verdiepende e-learningmodule voor Relaties →',
            ],
        ],
        4 => [
            'code' => 'M',
            'labels' => [
                'strong' => 'Sterk fundament',
                'partial' => 'Gedeeltelijk fundament',
                'fragile' => 'Fragiel fundament',
            ],
            'advice' => [
                'strong' => 'Je weet waarom je doet wat je doet. Dat geeft ruggengraat bij verandering. Ga naar Laag 2.',
                'partial' => 'Je ervaart soms zingeving, maar het is niet altijd helder aanwezig. Module 4 helpt je dat scherper te krijgen.',
                'fragile' => 'Zingeving is vaag of afwezig. Dit is het meest bepalende dat je kunt ontwikkelen. Prioriteit: module 4 - zingeving en betrokkenheid verdiepen.',
            ],
            'module' => [
                'free' => 'Start met module 1: Introductie PERMA en welbevinden →',
                'pro' => 'Start met module 4: Persoonlijk actieplan bouwen →',
            ],
        ],
        5 => [
            'code' => 'A',
            'labels' => [
                'strong' => 'Sterk fundament',
                'partial' => 'Gedeeltelijk fundament',
                'fragile' => 'Fragiel fundament',
            ],
            'advice' => [
                'strong' => 'Je ervaart wat je doet als waardevol en je boekt vooruitgang. Een sterk fundament voor Laag 2.',
                'partial' => 'Je bereikt dingen, maar de voldoening is wisselend. Module 1 helpt je resultaten bewuster te erkennen en te vieren.',
                'fragile' => 'Gevoel van voldoening is zwak aanwezig. Dat ondermijnt motivatie. Begin klein: een doel, een stap. Module 1 helpt je dit opbouwen.',
            ],
            'module' => [
                'free' => 'Start met module 1: Introductie PERMA en welbevinden →',
                'pro' => 'Start met module 1: Introductie PERMA en welbevinden →',
            ],
        ],
    ];

    public function key(): string
    {
        return 'positive_foundation';
    }

    public function version(): int
    {
        return 1;
    }

    public function supports(QuestionnaireResponse $response): bool
    {
        return $response->organizationQuestionnaire->questionnaire->title === SyncPositiveFoundationQuestionnaire::TITLE;
    }

    public function analyze(QuestionnaireResponse $response): QuestionnaireAnalysisResult
    {
        $questionnaire = $response->organizationQuestionnaire->questionnaire;
        $answersByQuestionId = $response->answers->keyBy('questionnaire_question_id');
        $dimensions = [];
        $totalScore = 0;

        foreach ($questionnaire->categories->sortBy('sort_order') as $category) {
            $pillarScore = $this->pillarScore($category, $answersByQuestionId->all());
            $statusKey = $this->statusKeyForPillarScore($pillarScore);
            $content = self::PILLAR_CONTENT[(int) $category->sort_order];

            $dimensions[] = new QuestionnaireDimensionResult(
                key: strtolower($content['code']),
                label: $category->title,
                score: $pillarScore,
                maxScore: 20,
                statusKey: $statusKey,
                statusLabel: $content['labels'][$statusKey],
                summary: $content['advice'][$statusKey],
                actionLabel: $statusKey === 'strong'
                    ? null
                    : $content['module'][$this->subscriptionType($response)],
                actionHref: null,
                isRecommended: false,
            );

            $totalScore += $pillarScore;
        }

        $overallProfile = $this->overallProfile($totalScore);
        $recommendedDimension = $this->recommendedDimension($dimensions);
        $isProUser = $response->user?->role === User::ROLE_USER_PRO;

        return new QuestionnaireAnalysisResult(
            analyzerKey: $this->key(),
            analyzerVersion: $this->version(),
            title: $overallProfile['title'],
            summary: $overallProfile['summary'],
            profileKey: $overallProfile['key'],
            profileLabel: $overallProfile['label'],
            score: $totalScore,
            maxScore: 100,
            recommendedDimensionKey: $isProUser ? $recommendedDimension->key : null,
            recommendedDimensionLabel: $isProUser ? $recommendedDimension->label : null,
            recommendedActionLabel: $this->recommendedActionLabel($overallProfile['key'], $recommendedDimension, $isProUser),
            recommendedActionHref: null,
            dimensions: $isProUser ? $this->markRecommendedDimension($dimensions, $recommendedDimension) : [],
        );
    }

    /**
     * @param  array<int, QuestionnaireResponseAnswer>  $answersByQuestionId
     */
    private function pillarScore(QuestionnaireCategory $category, array $answersByQuestionId): int
    {
        return $category->questions
            ->sortBy('sort_order')
            ->sum(function (QuestionnaireQuestion $question) use ($answersByQuestionId): int {
                $answer = $answersByQuestionId[$question->id] ?? null;

                return $this->scoreForAnswer($question, $answer?->answer);
            });
    }

    private function scoreForAnswer(QuestionnaireQuestion $question, ?string $answer): int
    {
        if ($answer === null) {
            return 0;
        }

        $optionIndex = array_search($answer, $question->options ?? [], true);

        if ($optionIndex === false) {
            return 0;
        }

        return $optionIndex + 1;
    }

    private function statusKeyForPillarScore(int $score): string
    {
        if ($score >= 15) {
            return 'strong';
        }

        if ($score >= 9) {
            return 'partial';
        }

        return 'fragile';
    }

    /**
     * @param  array<int, QuestionnaireDimensionResult>  $dimensions
     */
    private function recommendedDimension(array $dimensions): QuestionnaireDimensionResult
    {
        usort($dimensions, function (QuestionnaireDimensionResult $left, QuestionnaireDimensionResult $right): int {
            $scoreComparison = ($left->score ?? PHP_INT_MAX) <=> ($right->score ?? PHP_INT_MAX);

            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return $this->priorityForDimension($left) <=> $this->priorityForDimension($right);
        });

        return $dimensions[0];
    }

    /**
     * @param  array<int, QuestionnaireDimensionResult>  $dimensions
     * @return array<int, QuestionnaireDimensionResult>
     */
    private function markRecommendedDimension(array $dimensions, QuestionnaireDimensionResult $recommendedDimension): array
    {
        return array_map(function (QuestionnaireDimensionResult $dimension) use ($recommendedDimension): QuestionnaireDimensionResult {
            if ($dimension->key !== $recommendedDimension->key) {
                return $dimension;
            }

            return new QuestionnaireDimensionResult(
                key: $dimension->key,
                label: $dimension->label,
                score: $dimension->score,
                maxScore: $dimension->maxScore,
                statusKey: $dimension->statusKey,
                statusLabel: $dimension->statusLabel,
                summary: $dimension->summary,
                actionLabel: $dimension->actionLabel,
                actionHref: $dimension->actionHref,
                isRecommended: true,
            );
        }, $dimensions);
    }

    /**
     * @return array{key: string, label: string, title: string, summary: string}
     */
    private function overallProfile(int $score): array
    {
        if ($score >= 75) {
            return [
                'key' => 'strong',
                'label' => 'Sterk fundament',
                'title' => 'Je positieve fundament staat stevig.',
                'summary' => 'Je totaalscore laat zien dat je over meerdere PERMA-pijlers een sterk fundament hebt. Gebruik dit als basis om door te groeien naar Laag 2.',
            ];
        }

        if ($score >= 45) {
            return [
                'key' => 'partial',
                'label' => 'Gedeeltelijk fundament',
                'title' => 'Je fundament is aanwezig, maar nog niet overal even sterk.',
                'summary' => 'Je hebt duidelijke aanknopingspunten om je welbevinden verder te versterken. Kijk vooral naar de pijler met de laagste score voor je eerstvolgende stap.',
            ];
        }

        return [
            'key' => 'fragile',
            'label' => 'Fragiel fundament',
            'title' => 'Je fundament vraagt nu eerst aandacht.',
            'summary' => 'Je totaalscore laat zien dat meerdere PERMA-pijlers kwetsbaar zijn. Begin klein en gericht met de eerste stap die het meeste verschil maakt.',
        ];
    }

    private function recommendedActionLabel(
        string $overallProfileKey,
        QuestionnaireDimensionResult $recommendedDimension,
        bool $isProUser
    ): string {
        if ($overallProfileKey === 'strong') {
            return 'Ga naar Laag 2 →';
        }

        if (! $isProUser) {
            return 'Start met module 1: Introductie PERMA en welbevinden →';
        }

        $content = collect(self::PILLAR_CONTENT)
            ->first(fn (array $pillar): bool => strtolower($pillar['code']) === $recommendedDimension->key);

        return $content['module']['pro'];
    }

    private function subscriptionType(QuestionnaireResponse $response): string
    {
        return $response->user?->role === User::ROLE_USER_PRO ? 'pro' : 'free';
    }

    private function priorityForDimension(QuestionnaireDimensionResult $dimension): int
    {
        return array_search(strtoupper($dimension->key), self::PILLAR_PRIORITY, true) ?: PHP_INT_MAX;
    }
}
