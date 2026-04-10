<?php

namespace App\Support\Questionnaires\Results;

use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;

class DigitalMirrorQuestionnaireResponseAnalyzer implements QuestionnaireResponseAnalyzer
{
    /**
     * @var array<int, int>
     */
    private const START_LAYER_PRIORITY = [4, 3, 2, 5, 1, 6, 7];

    /**
     * @var array<string, bool>
     */
    private const REVERSED_QUESTIONS = [
        '2:4' => true,
        '4:1' => true,
        '5:3' => true,
        '6:2' => true,
        '6:4' => true,
    ];

    /**
     * @var array<int, array{statuses: array<string, array{summary: string, action_label: string|null}>}>
     */
    private const LAYER_CONTENT = [
        1 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'Je fundament vraagt aandacht. Zonder te weten wat je energie geeft en wat je kunt, is alles zwaarder dan het hoeft te zijn. Begin hier — niet als straf, maar als startpunt.',
                    'action_label' => 'Doe de zelftest Positief fundament →',
                ],
                'developing' => [
                    'summary' => 'Je hebt een basis, maar niet alle pijlers staan even stevig. De zelftest brengt in beeld welke PERMA-dimensies extra aandacht verdienen.',
                    'action_label' => 'Doe de zelftest Positief fundament →',
                ],
                'strong' => [
                    'summary' => 'Sterk fundament. Je weet wat je energie geeft en wat je kunt. Dit is je anker bij digitale verandering — gebruik het.',
                    'action_label' => null,
                ],
            ],
        ],
        2 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'De overtuiging \'dit is niets voor mij\' is het grootste obstakel. Niet de technologie. De zelftest Groeimindset en grit helpt je die overtuiging ter discussie te stellen.',
                    'action_label' => 'Doe de zelftest Groeimindset en grit →',
                ],
                'developing' => [
                    'summary' => 'Je gelooft dat groei mogelijk is — maar de volharding wisselt. De zelftest maakt zichtbaar of het de mindset of de grit is die je tegenhoudt.',
                    'action_label' => 'Doe de zelftest Groeimindset en grit →',
                ],
                'strong' => [
                    'summary' => 'Sterke mindset en volharding. Je gelooft in groei en bijt door. Dat maakt het verschil als het moeilijk wordt.',
                    'action_label' => null,
                ],
            ],
        ],
        3 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'Tegenslag kost je veel — misschien meer dan je beseft. Weerbaarheid is geen karakter maar een vaardigheid. En vaardigheden zijn trainbaar.',
                    'action_label' => 'Doe de zelftest Weerbaarheid →',
                ],
                'developing' => [
                    'summary' => 'Je veert terug, maar het kost energie. De zelftest laat zien welke van de zeven weerbaarheidsfactoren je het meest kunt versterken.',
                    'action_label' => 'Doe de zelftest Weerbaarheid →',
                ],
                'strong' => [
                    'summary' => 'Je veert goed terug. Tegenslag gooit je niet omver. Dat is een kracht die je kunt inzetten voor anderen om je heen.',
                    'action_label' => null,
                ],
            ],
        ],
        4 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'Je brein reageert sterk op digitale dreiging. Dat is biologie, geen zwakte. Maar het blokkeert al het andere. Begin hier — vóór je aan nieuwe vaardigheden werkt.',
                    'action_label' => 'Doe de zelftest Stress en het brein →',
                ],
                'developing' => [
                    'summary' => 'Je reageert op digitale stress, maar herstelt. De zelftest helpt je het herstel te versnellen en geeft je concrete technieken die bij jouw patroon passen.',
                    'action_label' => 'Doe de zelftest Stress en het brein →',
                ],
                'strong' => [
                    'summary' => 'Stabiel zenuwstelsel onder digitale druk. Je herstelt snel en kunt helder blijven denken als anderen vastlopen.',
                    'action_label' => null,
                ],
            ],
        ],
        5 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'Je wil er wel, maar de regie ontbreekt. Dat heeft niets met wilskracht te maken. Eén micro-doel per week — dat is het begin van het systeem dat je mist.',
                    'action_label' => 'Doe de zelftest Zelfleiderschap →',
                ],
                'developing' => [
                    'summary' => 'Je hebt de capaciteit, maar nog geen consistent systeem. De zelftest laat zien waar de regie weglekt — en hoe je die terugpakt.',
                    'action_label' => 'Doe de zelftest Zelfleiderschap →',
                ],
                'strong' => [
                    'summary' => 'Je hebt de regie over je eigen leerproces. Je stelt doelen, houdt vol en weet wanneer je bijsturen moet. Dat is zeldzaam.',
                    'action_label' => null,
                ],
            ],
        ],
        6 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'Jarenlange ervaring is waardevol — en soms de reden dat nieuw leren vastloopt. Afleren is de meest onderschatte stap. De zelftest helpt je zien wat je vasthoudt.',
                    'action_label' => 'Doe de zelftest Afleren en aanpassen →',
                ],
                'developing' => [
                    'summary' => 'Je kunt loslaten als het moet, maar het kost moeite. De zelftest helpt je zien welk patroon of welke overtuiging je specifiek blokkeert.',
                    'action_label' => 'Doe de zelftest Afleren en aanpassen →',
                ],
                'strong' => [
                    'summary' => 'Je durft los te laten. Dat vraagt moed, zeker als je veel ervaring hebt. Het maakt jou flexibeler dan de meeste mensen om je heen.',
                    'action_label' => null,
                ],
            ],
        ],
        7 => [
            'statuses' => [
                'attention' => [
                    'summary' => 'In de praktijk loopt het nog vast. Dat is het eerlijke beeld. Ga terug naar de laag met de laagste score — de integratie volgt vanzelf als de basis stevig is.',
                    'action_label' => 'Doe de zelftest van de aanbevolen startlaag →',
                ],
                'developing' => [
                    'summary' => 'Je functioneert digitaal, maar het voelt nog niet vanzelfsprekend. De zelftest brengt in beeld welke dimensies in de praktijk het meest knellen.',
                    'action_label' => 'Doe de zelftest Digitale weerbaarheid in de praktijk →',
                ],
                'strong' => [
                    'summary' => 'Digitale weerbaarheid in de praktijk: aanwezig. Je past je aan, vraagt hulp als dat nodig is en blijft nieuwsgierig. Dat is de kern.',
                    'action_label' => null,
                ],
            ],
        ],
    ];

    public function key(): string
    {
        return 'digital_mirror';
    }

    public function version(): int
    {
        return 1;
    }

    public function supports(QuestionnaireResponse $response): bool
    {
        return $response->organizationQuestionnaire->questionnaire->title === SyncDigitalMirrorQuestionnaire::TITLE;
    }

    public function analyze(QuestionnaireResponse $response): QuestionnaireAnalysisResult
    {
        $questionnaire = $response->organizationQuestionnaire->questionnaire;
        $answersByQuestionId = $response->answers->keyBy('questionnaire_question_id');
        $dimensions = [];
        $totalScore = 0;

        foreach ($questionnaire->categories->sortBy('sort_order') as $category) {
            $layerScore = $this->layerScore($category, $answersByQuestionId->all());
            $statusKey = $this->statusKeyForLayerScore($layerScore);
            $content = self::LAYER_CONTENT[(int) $category->sort_order]['statuses'][$statusKey];

            $dimensions[] = new QuestionnaireDimensionResult(
                key: 'layer_'.$category->sort_order,
                label: $category->title,
                score: $layerScore,
                maxScore: 20,
                statusKey: $statusKey,
                statusLabel: $this->statusLabel($statusKey),
                summary: $content['summary'],
                actionLabel: $content['action_label'],
                actionHref: null,
                isRecommended: false,
            );

            $totalScore += $layerScore;
        }

        $recommendedDimension = $this->recommendedDimension($dimensions);
        $profile = $this->overallProfile($totalScore);

        $dimensions = array_map(function (QuestionnaireDimensionResult $dimension) use ($recommendedDimension): QuestionnaireDimensionResult {
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

        return new QuestionnaireAnalysisResult(
            analyzerKey: $this->key(),
            analyzerVersion: $this->version(),
            title: $profile['title'],
            summary: $profile['summary'],
            profileKey: $profile['key'],
            profileLabel: $profile['label'],
            score: $totalScore,
            maxScore: 140,
            recommendedDimensionKey: $recommendedDimension->key,
            recommendedDimensionLabel: $recommendedDimension->label,
            recommendedActionLabel: $profile['action_label_prefix'].$recommendedDimension->label.' →',
            recommendedActionHref: null,
            dimensions: $dimensions,
        );
    }

    /**
     * @param  array<int, QuestionnaireResponseAnswer>  $answersByQuestionId
     */
    private function layerScore(QuestionnaireCategory $category, array $answersByQuestionId): int
    {
        return $category->questions
            ->sortBy('sort_order')
            ->sum(function (QuestionnaireQuestion $question) use ($answersByQuestionId, $category): int {
                $answer = $answersByQuestionId[$question->id] ?? null;
                $rawScore = $this->scoreForAnswer($question, $answer?->answer);

                if ($this->isReversed($category, $question)) {
                    return $rawScore > 0 ? 6 - $rawScore : 0;
                }

                return $rawScore;
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

    private function isReversed(QuestionnaireCategory $category, QuestionnaireQuestion $question): bool
    {
        return self::REVERSED_QUESTIONS[$category->sort_order.':'.$question->sort_order] ?? false;
    }

    private function statusKeyForLayerScore(int $score): string
    {
        if ($score <= 9) {
            return 'attention';
        }

        if ($score <= 14) {
            return 'developing';
        }

        return 'strong';
    }

    private function statusLabel(string $statusKey): string
    {
        return match ($statusKey) {
            'attention' => __('hermes.questionnaire.results.status_attention'),
            'developing' => __('hermes.questionnaire.results.status_developing'),
            default => __('hermes.questionnaire.results.status_strong'),
        };
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

            return array_search($this->layerNumber($left), self::START_LAYER_PRIORITY, true)
                <=>
                array_search($this->layerNumber($right), self::START_LAYER_PRIORITY, true);
        });

        return $dimensions[0];
    }

    private function layerNumber(QuestionnaireDimensionResult $dimension): int
    {
        return (int) str_replace('layer_', '', $dimension->key);
    }

    /**
     * @return array{key: string, label: string, title: string, summary: string, action_label_prefix: string}
     */
    private function overallProfile(int $score): array
    {
        if ($score <= 65) {
            return [
                'key' => 'vulnerable',
                'label' => 'Digitaal kwetsbaar',
                'title' => 'Je hebt de eerste stap al gezet.',
                'summary' => 'De spiegel is eerlijk, en dat vraagt moed. Je staat hier niet alleen in. Begin bij de laag die het meest urgent is — niet bij laag 1 per se, maar bij jouw persoonlijke prioriteit.',
                'action_label_prefix' => 'Start bij ',
            ];
        }

        if ($score <= 100) {
            return [
                'key' => 'developing',
                'label' => 'In ontwikkeling',
                'title' => 'Je groeit — en er is duidelijk richting.',
                'summary' => 'Twee of drie dimensies vragen nog aandacht. Dat is geen probleem — het is precies wat De Digitale Spiegel laat zien. Begin bij jouw laagste score.',
                'action_label_prefix' => 'Begin bij ',
            ];
        }

        return [
            'key' => 'resilient',
            'label' => 'Digitaal weerbaar',
            'title' => 'Je hebt een sterk digitaal fundament.',
            'summary' => 'De spiegel laat een solide profiel zien. Ga dieper in op de dimensie waar nog ruimte zit — de volledige zelftest geeft je het detail dat je nodig hebt.',
            'action_label_prefix' => 'Ga naar de zelftest van ',
        ];
    }
}
