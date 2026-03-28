<?php

namespace App\Actions\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;

class SyncAdaptabilityAceQuestionnaire
{
    public const TITLE = 'Adaptability Scan volgens het A.C.E.-model';

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
            $questionnaire = Questionnaire::query()->updateOrCreate(
                ['title' => self::TITLE],
                [
                    'description' => 'Deze vragenlijst is geinspireerd op het A.C.E.-model uit Decoding AQ van Ross Thornley. De inhoud is vertaald naar een interne questionnaire voor het analyseren van adaptability en is geen officiele AQai-assessment.',
                    'is_active' => true,
                ],
            );

            $categorySortOrders = [];

            foreach ($this->categories() as $categoryDefinition) {
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
                        [
                            'prompt' => $questionDefinition['prompt'],
                            'help_text' => $questionDefinition['help_text'],
                            'type' => QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
                            'options' => self::AGREEMENT_OPTIONS,
                            'is_required' => true,
                        ],
                    );
                }

                $category->questions()
                    ->whereNotIn('sort_order', $questionSortOrders)
                    ->delete();
            }

            $questionnaire->categories()
                ->whereNotIn('sort_order', $categorySortOrders)
                ->delete();

            return $questionnaire->fresh(['categories.questions']);
        });
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     description: string,
     *     sort_order: int,
     *     questions: array<int, array{
     *         prompt: string,
     *         help_text: string,
     *         sort_order: int
     *     }>
     * }>
     */
    protected function categories(): array
    {
        return [
            [
                'title' => 'Ability',
                'description' => 'Ability gaat over het vermogen om nieuwe kennis op te nemen, signalen te duiden en gedrag praktisch aan te passen.',
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'Ik maak mij nieuwe kennis, tools of werkwijzen snel eigen wanneer de situatie daarom vraagt.',
                        'help_text' => 'Denk aan veranderingen in processen, systemen of verwachtingen.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ik herken vroeg wanneer bestaande aanpakken niet meer goed werken.',
                        'help_text' => 'Beoordeel hoe snel u ziet dat bijsturing nodig is.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ik kan meerdere oplossingsrichtingen bedenken wanneer een plan vastloopt.',
                        'help_text' => 'Het gaat om flexibiliteit in denken en handelen.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ik vertaal feedback of nieuwe informatie snel naar concreet ander gedrag.',
                        'help_text' => 'Kijk naar wat u in de praktijk doet na feedback of nieuwe inzichten.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'Ik blijf effectief prioriteren wanneer doelen, rollen of omstandigheden veranderen.',
                        'help_text' => 'Denk aan situaties met druk, onduidelijkheid of wisselende prioriteiten.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Character',
                'description' => 'Character gaat over mindset, emotionele stevigheid en de bereidheid om verantwoordelijkheid te nemen in verandering.',
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'Ik blijf doorgaans kalm en constructief wanneer uitkomsten onzeker zijn.',
                        'help_text' => 'Beoordeel hoe u reageert op ambiguiteit en verandering.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ik zie veranderingen eerder als kans om te leren dan als bedreiging.',
                        'help_text' => 'Het gaat om uw basishouding tegenover verandering.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Na een tegenslag hervind ik relatief snel mijn focus en energie.',
                        'help_text' => 'Denk aan herstelvermogen na stress, fouten of tegenvallers.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ik neem verantwoordelijkheid voor mijn eigen aanpassing, ook als niet alles duidelijk is.',
                        'help_text' => 'Beoordeel in hoeverre u zelf initiatief neemt in veranderende situaties.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'Ik durf bestaande aannames of routines ter discussie te stellen als de context daarom vraagt.',
                        'help_text' => 'Denk aan moed om te experimenteren of een ander gesprek te starten.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Environment',
                'description' => 'Environment kijkt naar de context waarin adaptability wordt ondersteund of juist geremd.',
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'In mijn werkomgeving is het veilig om vragen te stellen, te experimenteren en fouten bespreekbaar te maken.',
                        'help_text' => 'Beoordeel de mate van psychologische veiligheid.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ik krijg voldoende feedback en perspectieven om mij goed aan veranderende situaties aan te passen.',
                        'help_text' => 'Denk aan input van collega\'s, leidinggevenden of klanten.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Mijn team of organisatie ondersteunt actief leren, ontwikkelen en bijsturen.',
                        'help_text' => 'Kijk naar ruimte voor reflectie, training en verbetering.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ik heb genoeg autonomie om mijn aanpak aan te passen wanneer de situatie daarom vraagt.',
                        'help_text' => 'Het gaat om beslisruimte en handelingsvrijheid.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'De systemen, processen en samenwerking om mij heen helpen eerder mee dan dat ze aanpassing vertragen.',
                        'help_text' => 'Beoordeel of de omgeving verandering praktisch ondersteunt.',
                        'sort_order' => 5,
                    ],
                ],
            ],
        ];
    }
}
