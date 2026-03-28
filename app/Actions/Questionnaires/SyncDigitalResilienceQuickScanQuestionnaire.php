<?php

namespace App\Actions\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;

class SyncDigitalResilienceQuickScanQuestionnaire
{
    public const TITLE = 'Quick scan digitale weerbaarheid';

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
                    'description' => 'Deze quick scan is een verdieping op de questionnaire "Adaptability Scan volgens het A.C.E.-model" en richt zich op concreet gedrag rond leerbereidheid, digitale ontwikkeling en het vermogen om bij te blijven in een snel veranderend digitaal tijdperk.',
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
                'title' => 'Leerhouding en nieuwsgierigheid',
                'description' => 'Deze categorie kijkt naar de mindset om te blijven leren, experimenteren en digitale vernieuwing actief op te zoeken.',
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'Ik reserveer bewust tijd om nieuwe digitale tools, toepassingen of werkwijzen te verkennen.',
                        'help_text' => 'Denk aan structurele aandacht voor ontwikkeling, ook als het druk is.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Wanneer ik een nieuwe digitale toepassing nog niet begrijp, zie ik dat vooral als iets wat ik kan leren.',
                        'help_text' => 'Beoordeel uw basishouding bij onbekende systemen of technologie.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ik zoek zelf actief uitleg, voorbeelden of training op wanneer digitale kennis mij ontbreekt.',
                        'help_text' => 'Het gaat om eigen initiatief om kennisgaten te verkleinen.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ik ben bereid om bestaande routines los te laten als een nieuw digitaal hulpmiddel beter werkt.',
                        'help_text' => 'Kijk naar uw openheid om vertrouwde werkwijzen te vervangen.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'Ik vind het belangrijk om mij ook op langere termijn digitaal te blijven ontwikkelen.',
                        'help_text' => 'Denk aan commitment aan een leven lang leren.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Digitaal leergedrag in de praktijk',
                'description' => 'Deze categorie zoomt in op concreet gedrag dat laat zien of iemand digitaal bijblijft en nieuwe middelen werkend krijgt.',
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'Ik probeer nieuwe digitale functies of instellingen eerst zelf uit voordat ik afhaak.',
                        'help_text' => 'Beoordeel hoe onderzoekend u bent in de praktijk.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ik leer vlot van digitale fouten of mislukte pogingen en pas mijn aanpak daarna aan.',
                        'help_text' => 'Het gaat om leren door doen en bijsturen.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ik houd actief bij welke digitale ontwikkelingen relevant zijn voor mijn werk of rol.',
                        'help_text' => 'Denk aan updates, trends, tools of veranderende verwachtingen.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ik vraag gericht hulp of feedback als dat mijn digitale leerproces versnelt.',
                        'help_text' => 'Kijk naar slim gebruik van collega\'s, support of andere bronnen.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'Als ik een nieuwe digitale werkwijze heb geleerd, pas ik die daarna ook echt toe in mijn dagelijkse werk.',
                        'help_text' => 'Het gaat om transfer van leren naar concreet gedrag.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Wendbaarheid in digitale verandering',
                'description' => 'Deze categorie gaat over flexibiliteit, tempo van aanpassen en volhouden wanneer digitalisering iets anders van u vraagt.',
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'Ik pas mij snel aan wanneer mijn organisatie overstapt op nieuwe digitale systemen of processen.',
                        'help_text' => 'Beoordeel uw snelheid van omschakelen bij verandering.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ik blijf doorgaans rustig en praktisch wanneer digitale veranderingen onzekerheid of extra werk opleveren.',
                        'help_text' => 'Denk aan uw reactie onder druk of onduidelijkheid.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ik kan goed schakelen tussen verschillende digitale tools zonder snel vast te lopen.',
                        'help_text' => 'Het gaat om flexibiliteit in een omgeving met meerdere systemen.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ik neem verantwoordelijkheid om digitaal inzetbaar te blijven, ook als mijn werkgever niet alles voor mij organiseert.',
                        'help_text' => 'Kijk naar eigenaarschap voor uw eigen duurzame inzetbaarheid.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'Ik geloof dat digitale verandering iets is waar ik mij blijvend toe moet verhouden, niet iets tijdelijks.',
                        'help_text' => 'Beoordeel of u digitalisering ziet als een blijvend onderdeel van professioneel handelen.',
                        'sort_order' => 5,
                    ],
                ],
            ],
        ];
    }
}
