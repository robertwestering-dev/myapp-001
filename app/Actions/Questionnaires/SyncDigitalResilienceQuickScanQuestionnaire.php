<?php

namespace App\Actions\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncDigitalResilienceQuickScanQuestionnaire
{
    public const TITLE = 'Quick scan digitale weerbaarheid';

    public const ENGLISH_TITLE = 'Digital resilience quick scan';

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

    /**
     * @var array<int, string>
     */
    protected const AGREEMENT_OPTIONS_ENGLISH = [
        'Strongly disagree',
        'Disagree',
        'Neutral',
        'Agree',
        'Strongly agree',
    ];

    public function handle(): Questionnaire
    {
        return DB::transaction(function (): Questionnaire {
            $questionnaireTableHasLocale = Schema::hasColumn('questionnaires', 'locale');
            $questionTableHasLocale = Schema::hasColumn('questionnaire_questions', 'locale');
            $definitions = $this->definitions();
            $primaryDefinition = $definitions[0];

            $questionnaire = Questionnaire::query()->updateOrCreate(
                ['title' => self::TITLE],
                array_filter([
                    'description' => $primaryDefinition['description'],
                    'locale' => $questionnaireTableHasLocale ? config('locales.primary') : null,
                    'is_active' => true,
                ], fn (mixed $value): bool => $value !== null),
            );

            Questionnaire::query()
                ->where('title', self::ENGLISH_TITLE)
                ->whereKeyNot($questionnaire->id)
                ->update(['is_active' => false]);

            $categorySortOrders = [];
            $questionReferencesToKeep = [];

            foreach ($primaryDefinition['categories'] as $categoryDefinition) {
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

                foreach ($definitions as $definition) {
                    $localizedCategory = collect($definition['categories'])
                        ->firstWhere('sort_order', $categoryDefinition['sort_order']);

                    foreach (($localizedCategory['questions'] ?? []) as $questionDefinition) {
                        $questionReferencesToKeep[] = [
                            'category_id' => $category->id,
                            'sort_order' => $questionDefinition['sort_order'],
                            'locale' => $definition['locale'],
                        ];

                        QuestionnaireQuestion::query()->updateOrCreate(
                            array_filter([
                                'questionnaire_category_id' => $category->id,
                                'sort_order' => $questionDefinition['sort_order'],
                                'locale' => $questionTableHasLocale ? $definition['locale'] : null,
                            ], fn (mixed $value): bool => $value !== null),
                            array_filter([
                                'prompt' => $questionDefinition['prompt'],
                                'help_text' => $questionDefinition['help_text'],
                                'type' => QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
                                'options' => $definition['agreement_options'],
                                'is_required' => true,
                            ], fn (mixed $value): bool => $value !== null),
                        );
                    }
                }
            }

            $questionnaire->categories()
                ->whereNotIn('sort_order', $categorySortOrders)
                ->delete();

            $this->deleteStaleQuestions($questionnaire, $questionReferencesToKeep);

            return $questionnaire->fresh(['categories.questions']);
        });
    }

    /**
     * @param  array<int, array{category_id: int, sort_order: int, locale: string}>  $questionReferencesToKeep
     */
    protected function deleteStaleQuestions(Questionnaire $questionnaire, array $questionReferencesToKeep): void
    {
        $questionnaire->load('categories.questions');

        $references = collect($questionReferencesToKeep)
            ->map(fn (array $reference): string => $reference['category_id'].':'.$reference['sort_order'].':'.$reference['locale'])
            ->all();

        $questionnaire->categories
            ->flatMap->questions
            ->reject(fn (QuestionnaireQuestion $question): bool => in_array(
                $question->questionnaire_category_id.':'.$question->sort_order.':'.$question->locale,
                $references,
                true,
            ))
            ->each->delete();
    }

    /**
     * @return array<int, array{
     *     locale: string,
     *     title: string,
     *     description: string,
     *     agreement_options: array<int, string>,
     *     categories: array<int, array{
     *         title: string,
     *         description: string,
     *         sort_order: int,
     *         questions: array<int, array{
     *             prompt: string,
     *             help_text: string,
     *             sort_order: int
     *         }>
     *     }>
     * }>
     */
    protected function definitions(): array
    {
        return [
            [
                'locale' => 'nl',
                'title' => self::TITLE,
                'description' => 'Deze quick scan is een verdieping op de questionnaire "Adaptability Scan volgens het A.C.E.-model" en richt zich op concreet gedrag rond leerbereidheid, digitale ontwikkeling en het vermogen om bij te blijven in een snel veranderend digitaal tijdperk.',
                'agreement_options' => self::AGREEMENT_OPTIONS,
                'categories' => $this->dutchCategories(),
            ],
            [
                'locale' => 'en',
                'title' => self::ENGLISH_TITLE,
                'description' => 'This quick scan builds on the questionnaire "Adaptability Scan based on the A.C.E. model" and focuses on concrete behaviour related to willingness to learn, digital development, and the ability to keep up in a rapidly changing digital era.',
                'agreement_options' => self::AGREEMENT_OPTIONS_ENGLISH,
                'categories' => $this->englishCategories(),
            ],
        ];
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
    protected function dutchCategories(): array
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
    protected function englishCategories(): array
    {
        return [
            [
                'title' => 'Learning mindset and curiosity',
                'description' => 'This category looks at the mindset needed to keep learning, experimenting, and actively exploring digital innovation.',
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'I deliberately set aside time to explore new digital tools, applications, or ways of working.',
                        'help_text' => 'Think about making room for development even when things are busy.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'When I do not yet understand a new digital application, I mainly see it as something I can learn.',
                        'help_text' => 'Assess your default attitude toward unfamiliar systems or technology.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I actively look for explanations, examples, or training when I lack digital knowledge.',
                        'help_text' => 'This is about taking initiative to close knowledge gaps.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I am willing to let go of existing routines if a new digital tool works better.',
                        'help_text' => 'Look at your openness to replacing familiar ways of working.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'I believe it is important to continue developing digitally over the longer term as well.',
                        'help_text' => 'Think about your commitment to lifelong learning.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Digital learning behaviour in practice',
                'description' => 'This category zooms in on concrete behaviour that shows whether someone keeps up digitally and gets new tools working.',
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'I first try out new digital features or settings myself before giving up.',
                        'help_text' => 'Assess how exploratory you are in practice.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I learn quickly from digital mistakes or failed attempts and adjust my approach afterwards.',
                        'help_text' => 'This is about learning by doing and making corrections.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I actively keep track of digital developments that are relevant to my work or role.',
                        'help_text' => 'Think about updates, trends, tools, or changing expectations.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I ask for targeted help or feedback when that speeds up my digital learning process.',
                        'help_text' => 'Look at how well you use colleagues, support, or other sources.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'When I learn a new digital way of working, I actually apply it in my day-to-day work afterwards.',
                        'help_text' => 'This is about transferring learning into concrete behaviour.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Adaptability in digital change',
                'description' => 'This category is about flexibility, speed of adjustment, and staying the course when digitalization asks something different of you.',
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'I adapt quickly when my organization switches to new digital systems or processes.',
                        'help_text' => 'Assess how quickly you switch gears during change.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I generally stay calm and practical when digital changes create uncertainty or extra work.',
                        'help_text' => 'Think about how you respond under pressure or ambiguity.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I can switch well between different digital tools without getting stuck quickly.',
                        'help_text' => 'This is about flexibility in an environment with multiple systems.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I take responsibility for remaining digitally employable, even if my employer does not organize everything for me.',
                        'help_text' => 'Look at ownership of your own sustainable employability.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'I believe digital change is something I need to keep engaging with, not something temporary.',
                        'help_text' => 'Assess whether you see digitalization as a lasting part of professional practice.',
                        'sort_order' => 5,
                    ],
                ],
            ],
        ];
    }
}
