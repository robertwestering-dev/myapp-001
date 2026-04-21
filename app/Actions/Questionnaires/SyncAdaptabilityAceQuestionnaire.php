<?php

namespace App\Actions\Questionnaires;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncAdaptabilityAceQuestionnaire
{
    public const TITLE = 'Adaptability Scan volgens het A.C.E.-model';

    public const ENGLISH_TITLE = 'Adaptability Scan based on the A.C.E. model';

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
                'description' => 'Deze vragenlijst is geinspireerd op het A.C.E.-model uit Decoding AQ van Ross Thornley. De inhoud is vertaald naar een interne questionnaire voor het analyseren van adaptability en is geen officiele AQai-assessment.',
                'agreement_options' => self::AGREEMENT_OPTIONS,
                'categories' => $this->dutchCategories(),
            ],
            [
                'locale' => 'en',
                'title' => self::ENGLISH_TITLE,
                'description' => 'This questionnaire is inspired by the A.C.E. model from Decoding AQ by Ross Thornley. The content has been translated into an internal questionnaire for analysing adaptability and is not an official AQai assessment.',
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
                'title' => 'Ability',
                'description' => 'Ability is about taking in new knowledge, interpreting signals, and adjusting behaviour in practical ways.',
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'I quickly make new knowledge, tools, or ways of working my own when the situation requires it.',
                        'help_text' => 'Think of changes in processes, systems, or expectations.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I recognize early when existing approaches are no longer working well.',
                        'help_text' => 'Assess how quickly you notice that an adjustment is needed.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I can come up with multiple ways forward when a plan gets stuck.',
                        'help_text' => 'This is about flexibility in thinking and acting.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I quickly turn feedback or new information into concrete behavioural change.',
                        'help_text' => 'Look at what you actually do after feedback or new insights.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'I continue to prioritize effectively when goals, roles, or circumstances change.',
                        'help_text' => 'Think of situations with pressure, ambiguity, or shifting priorities.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Character',
                'description' => 'Character is about mindset, emotional resilience, and the willingness to take responsibility during change.',
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'I generally stay calm and constructive when outcomes are uncertain.',
                        'help_text' => 'Assess how you respond to ambiguity and change.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I see change more as an opportunity to learn than as a threat.',
                        'help_text' => 'This is about your default attitude toward change.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'After a setback, I regain my focus and energy relatively quickly.',
                        'help_text' => 'Think about your ability to recover after stress, mistakes, or disappointment.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I take responsibility for adapting myself, even when not everything is clear yet.',
                        'help_text' => 'Assess the extent to which you take initiative in changing situations.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'I dare to question existing assumptions or routines when the context calls for it.',
                        'help_text' => 'Think of the courage to experiment or start a different conversation.',
                        'sort_order' => 5,
                    ],
                ],
            ],
            [
                'title' => 'Environment',
                'description' => 'Environment looks at the context that either supports or hinders adaptability.',
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'In my work environment, it is safe to ask questions, experiment, and discuss mistakes openly.',
                        'help_text' => 'Assess the level of psychological safety.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I receive enough feedback and perspectives to adapt well to changing situations.',
                        'help_text' => 'Think of input from colleagues, managers, or customers.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'My team or organization actively supports learning, development, and course correction.',
                        'help_text' => 'Look at the room for reflection, training, and improvement.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I have enough autonomy to adjust my approach when the situation requires it.',
                        'help_text' => 'This is about decision space and freedom to act.',
                        'sort_order' => 4,
                    ],
                    [
                        'prompt' => 'The systems, processes, and collaboration around me are more likely to help change than to slow it down.',
                        'help_text' => 'Assess whether the environment supports change in practical terms.',
                        'sort_order' => 5,
                    ],
                ],
            ],
        ];
    }
}
