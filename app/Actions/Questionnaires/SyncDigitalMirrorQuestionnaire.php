<?php

namespace App\Actions\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncDigitalMirrorQuestionnaire
{
    public const TITLE = 'De digitale spiegel';

    /**
     * @var array<int, string>
     */
    protected const AGREEMENT_OPTIONS = [
        'Zeer mee oneens',
        'Mee oneens',
        'Neutraal',
        'Mee eens',
        'Zeer mee eens',
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
                            'options' => self::AGREEMENT_OPTIONS,
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
            'description' => 'Deze vragenlijst brengt in kaart hoe iemand kijkt naar eigen digitale groei, stress, veerkracht en aanpassingsvermogen in de praktijk.',
            'categories' => [
                [
                    'title' => 'Positief fundament',
                    'description' => null,
                    'sort_order' => 1,
                    'questions' => [
                        [
                            'prompt' => 'Ik weet welke activiteiten mij energie geven en maak daar bewust ruimte voor.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Ik kan mijn drie grootste sterke kanten benoemen zonder lang na te hoeven denken.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Mijn werk of dagelijkse bezigheden voelen zinvol voor mij.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Als ik aan mijn toekomst denk, voel ik me over het algemeen optimistisch.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Groeimindset en grit',
                    'description' => null,
                    'sort_order' => 2,
                    'questions' => [
                        [
                            'prompt' => 'Ik geloof dat ik digitale vaardigheden kan leren, ook als het in het begin moeilijk gaat.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Als ik vastloop bij iets nieuws, zie ik dat als een onderdeel van het leerproces in plaats van een teken dat ik het niet kan.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Ik geef niet snel op als iets me niet lukt op mijn eerste poging.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'De gedachte \'dit is niets voor mij\' houdt mij tegen bij het leren van nieuwe digitale dingen.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Weerbaarheid',
                    'description' => null,
                    'sort_order' => 3,
                    'questions' => [
                        [
                            'prompt' => 'Als er iets misgaat met een digitale tool of systeem, herstel ik snel en ga ik door.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Ik kan mijn emoties voldoende reguleren als ik gefrustreerd raak door technologie.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Ik vertrouw erop dat ik ook onbekende digitale uitdagingen uiteindelijk aankan.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Als een collega een nieuwe tool sneller oppakt dan ik, zegt me dat weinig over mijn eigen vermogen.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Stress en het brein',
                    'description' => null,
                    'sort_order' => 4,
                    'questions' => [
                        [
                            'prompt' => 'Bij het moeten werken met een nieuwe of onbekende tool voel ik spanning of weerstand in mijn lichaam.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Ik herken wanneer ik in een stressreactie zit en kan daar iets mee doen.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Na een vervelende digitale ervaring (crash, fout, onbegrijpelijke update) kom ik snel weer tot rust.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Ik beschik over technieken om mijn hoofd leeg te maken als digitale druk oploopt.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Zelfleiderschap',
                    'description' => null,
                    'sort_order' => 5,
                    'questions' => [
                        [
                            'prompt' => 'Ik stel haalbare doelen voor mezelf als ik iets nieuws digitaal wil leren.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Ik weet hoe ik mijzelf kan motiveren om door te gaan, ook als ik er even geen zin in heb.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Ik stel digitale leertaken regelmatig uit.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Ik heb een manier of systeem om bij te houden wat ik al geleerd heb en wat ik nog wil leren.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Afleren en aanpassen',
                    'description' => null,
                    'sort_order' => 6,
                    'questions' => [
                        [
                            'prompt' => 'Ik ben bereid mijn vertrouwde werkwijzen los te laten als een nieuwe aanpak beter werkt.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Als iemand zegt dat ik iets anders moet doen dan ik gewend ben, voel ik me snel defensief.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Ik kan experimenteren met nieuwe digitale werkwijzen, ook als ik nog niet zeker weet of ze werken.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Mijn jarenlange ervaring is voor mij soms een reden om nieuwe manieren van werken te vermijden.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
                [
                    'title' => 'Digitale weerbaarheid in de praktijk',
                    'description' => null,
                    'sort_order' => 7,
                    'questions' => [
                        [
                            'prompt' => 'Als er op mijn werk een nieuwe digitale tool wordt uitgerold, reageer ik daar over het algemeen met nieuwsgierigheid in plaats van weerstand.',
                            'sort_order' => 1,
                        ],
                        [
                            'prompt' => 'Ik voel me zelfverzekerd genoeg om anderen om hulp te vragen als ik digitaal vastloop.',
                            'sort_order' => 2,
                        ],
                        [
                            'prompt' => 'Ik pas mij redelijk snel aan als digitale systemen of procedures veranderen.',
                            'sort_order' => 3,
                        ],
                        [
                            'prompt' => 'Als ik terugkijk op de afgelopen twee jaar, zie ik duidelijk dat mijn digitale vaardigheden gegroeid zijn.',
                            'sort_order' => 4,
                        ],
                    ],
                ],
            ],
        ];
    }
}
