<?php

namespace App\Actions\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncPositiveFoundationQuestionnaire
{
    public const TITLE = 'Positief fundament';

    /**
     * @var array<int, string>
     */
    protected const SCALE_OPTIONS = [
        'Nooit / niet',
        'Zelden',
        'Soms',
        'Vaak',
        'Altijd / Volledig',
    ];

    public function handle(): Questionnaire
    {
        return DB::transaction(function (): Questionnaire {
            $questionnaireTableHasLocale = Schema::hasColumn('questionnaires', 'locale');
            $questionTableHasLocale = Schema::hasColumn('questionnaire_questions', 'locale');

            $definition = $this->definition();

            $questionnaire = Questionnaire::query()->updateOrCreate(
                ['title' => $definition['title']],
                array_filter([
                    'description' => $definition['description'],
                    'locale' => $questionnaireTableHasLocale ? $definition['locale'] : null,
                    'is_active' => true,
                ], fn (mixed $value): bool => $value !== null),
            );

            $categorySortOrders = [];

            foreach ($definition['categories'] as $categoryDefinition) {
                $categorySortOrders[] = $categoryDefinition['sort_order'];

                $category = QuestionnaireCategory::query()->updateOrCreate(
                    [
                        'questionnaire_id' => $questionnaire->id,
                        'sort_order' => $categoryDefinition['sort_order'],
                    ],
                    [
                        'title' => $categoryDefinition['title'],
                        'description' => $categoryDefinition['description'],
                    ],
                );

                $questionSortOrders = [];

                foreach ($categoryDefinition['questions'] as $questionDefinition) {
                    $questionSortOrders[] = $questionDefinition['sort_order'];

                    QuestionnaireQuestion::query()->updateOrCreate(
                        [
                            'questionnaire_category_id' => $category->id,
                            'sort_order' => $questionDefinition['sort_order'],
                        ],
                        array_filter([
                            'locale' => $questionTableHasLocale ? ($questionnaire->locale ?? $definition['locale']) : null,
                            'prompt' => $questionDefinition['prompt'],
                            'help_text' => null,
                            'type' => QuestionnaireQuestion::TYPE_LIKERT_SCALE,
                            'options' => self::SCALE_OPTIONS,
                            'is_required' => true,
                        ], fn (mixed $value): bool => $value !== null),
                    );
                }

                $category->questions()
                    ->whereNotIn('sort_order', $questionSortOrders)
                    ->delete();
            }

            $questionnaire->categories()
                ->whereNotIn('sort_order', $categorySortOrders)
                ->delete();

            return $questionnaire->fresh(['categories.questions']) ?? new Questionnaire;
        });
    }

    /**
     * @return array{
     *     locale: string,
     *     title: string,
     *     description: string,
     *     categories: array<int, array{
     *         title: string,
     *         description: string|null,
     *         sort_order: int,
     *         questions: array<int, array{
     *             prompt: string,
     *             sort_order: int
     *         }>
     *     }>
     * }
     */
    protected function definition(): array
    {
        return [
            'locale' => 'nl',
            'title' => self::TITLE,
            'description' => 'Deze PERMA-vragenlijst brengt het positieve fundament in kaart aan de hand van positieve emotie, betrokkenheid, relaties, zingeving en voldoening.',
            'categories' => [
                [
                    'title' => 'Positieve emotie',
                    'description' => null,
                    'sort_order' => 1,
                    'questions' => [
                        [
                            'prompt' => 'Hoe vaak voel je je tevreden met hoe je dag verloopt?',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Hoe vaak ervaar je echte vreugde of plezier - niet alleen opluchting, maar echt genieten?',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'In hoeverre kijk je met vertrouwen vooruit naar de komende maanden?',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Hoe vaak sta je stil bij kleine dingen in het dagelijks leven die jou goed doen?',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Betrokkenheid',
                    'description' => null,
                    'sort_order' => 2,
                    'questions' => [
                        [
                            'prompt' => 'Hoe vaak ben je zo verdiept in iets dat je de tijd vergeet?',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'In hoeverre doe je dagelijks dingen waarbij je jouw sterke kanten echt inzet?',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Hoe goed weet jij wat jouw drie grootste sterke kanten zijn?',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Hoe vaak doe je dingen die je energie geven in plaats van energie kosten?',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Relaties',
                    'description' => null,
                    'sort_order' => 3,
                    'questions' => [
                        [
                            'prompt' => 'Hoe tevreden ben je over de relaties die je hebt met de mensen die je na staan?',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'In hoeverre voel je je gesteund door anderen als het moeilijk gaat?',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Hoe vaak heb je oprechte gesprekken waarbij je je echt begrepen voelt?',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'In hoeverre draag jij bij aan het welzijn van anderen om je heen?',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Zingeving',
                    'description' => null,
                    'sort_order' => 4,
                    'questions' => [
                        [
                            'prompt' => 'In hoeverre heeft wat je dagelijks doet betekenis voor jou?',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Hoe sterk voel je een persoonlijk doel of richting in je leven?',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'In hoeverre draag je bij aan iets wat groter is dan jijzelf?',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Hoe vaak heb je het gevoel dat je ertoe doet - voor anderen of voor de wereld?',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Voldoening',
                    'description' => null,
                    'sort_order' => 5,
                    'questions' => [
                        [
                            'prompt' => 'Hoe tevreden ben je over wat je de afgelopen tijd hebt bereikt?',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'In hoeverre stel je doelen voor jezelf en werk je daar actief naartoe?',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Hoe vaak heb je het gevoel dat je vooruitgaat - hoe klein ook?',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'In hoeverre blik je met tevredenheid terug op wat je in je leven hebt opgebouwd?',
                            'sort_order' => 4,
                        ],
                    ],
                ],
            ],
        ];
    }
}
