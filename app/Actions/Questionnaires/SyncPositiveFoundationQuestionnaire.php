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

    public const ENGLISH_TITLE = 'Positive foundation';

    public const GERMAN_TITLE = 'Positives Fundament';

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

    /**
     * @var array<int, string>
     */
    protected const SCALE_OPTIONS_ENGLISH = [
        'Never / not at all',
        'Rarely',
        'Sometimes',
        'Often',
        'Always / fully',
    ];

    /**
     * @var array<int, string>
     */
    protected const SCALE_OPTIONS_GERMAN = [
        'Nie / gar nicht',
        'Selten',
        'Manchmal',
        'Oft',
        'Immer / vollständig',
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
                ->whereIn('title', [self::ENGLISH_TITLE, self::GERMAN_TITLE])
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
                                'help_text' => null,
                                'type' => QuestionnaireQuestion::TYPE_LIKERT_SCALE,
                                'options' => $definition['scale_options'],
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

            return $questionnaire->fresh(['categories.questions']) ?? new Questionnaire;
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
     *     scale_options: array<int, string>,
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
    protected function definitions(): array
    {
        return [
            [
                'locale' => 'nl',
                'title' => self::TITLE,
                'description' => 'Deze PERMA-vragenlijst brengt het positieve fundament in kaart aan de hand van positieve emotie, betrokkenheid, relaties, zingeving en voldoening.',
                'scale_options' => self::SCALE_OPTIONS,
                'categories' => $this->dutchCategories(),
            ],
            [
                'locale' => 'en',
                'title' => self::ENGLISH_TITLE,
                'description' => 'This PERMA questionnaire maps the positive foundation through positive emotion, engagement, relationships, meaning, and accomplishment.',
                'scale_options' => self::SCALE_OPTIONS_ENGLISH,
                'categories' => $this->englishCategories(),
            ],
            [
                'locale' => 'de',
                'title' => self::GERMAN_TITLE,
                'description' => 'Dieser PERMA-Fragebogen erfasst das positive Fundament anhand von positiver Emotion, Engagement, Beziehungen, Sinn und Erfüllung.',
                'scale_options' => self::SCALE_OPTIONS_GERMAN,
                'categories' => $this->germanCategories(),
            ],
        ];
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     description: string|null,
     *     sort_order: int,
     *     questions: array<int, array{prompt: string, sort_order: int}>
     * }>
     */
    protected function dutchCategories(): array
    {
        return [
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
        ];
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     description: string|null,
     *     sort_order: int,
     *     questions: array<int, array{prompt: string, sort_order: int}>
     * }>
     */
    protected function englishCategories(): array
    {
        return [
            [
                'title' => 'Positive emotion',
                'description' => null,
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'How often do you feel satisfied with how your day is going?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'How often do you experience genuine joy or pleasure - not just relief, but truly enjoying something?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'To what extent do you look ahead to the coming months with confidence?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'How often do you pause to notice small things in everyday life that do you good?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Engagement',
                'description' => null,
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'How often are you so absorbed in something that you lose track of time?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'To what extent do you do things every day where you truly use your strengths?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'How well do you know your three greatest strengths?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'How often do you do things that give you energy instead of costing you energy?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Relationships',
                'description' => null,
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'How satisfied are you with the relationships you have with the people close to you?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'To what extent do you feel supported by others when things are difficult?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'How often do you have sincere conversations in which you feel truly understood?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'To what extent do you contribute to the wellbeing of the people around you?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Meaning',
                'description' => null,
                'sort_order' => 4,
                'questions' => [
                    [
                        'prompt' => 'To what extent does what you do every day feel meaningful to you?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'How strongly do you feel a personal purpose or direction in your life?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'To what extent do you contribute to something larger than yourself?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'How often do you feel that you matter - to others or to the world?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Accomplishment',
                'description' => null,
                'sort_order' => 5,
                'questions' => [
                    [
                        'prompt' => 'How satisfied are you with what you have achieved recently?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'To what extent do you set goals for yourself and actively work toward them?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'How often do you feel that you are making progress - however small?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'To what extent do you look back with satisfaction on what you have built in your life?',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     description: string|null,
     *     sort_order: int,
     *     questions: array<int, array{prompt: string, sort_order: int}>
     * }>
     */
    protected function germanCategories(): array
    {
        return [
            [
                'title' => 'Positive Emotion',
                'description' => null,
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'Wie oft bist du zufrieden damit, wie dein Tag verläuft?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Wie oft erlebst du echte Freude oder Vergnügen - nicht nur Erleichterung, sondern wirkliches Genießen?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Inwieweit blickst du den kommenden Monaten zuversichtlich entgegen?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Wie oft hältst du inne und bemerkst kleine Dinge im Alltag, die dir guttun?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Engagement',
                'description' => null,
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'Wie oft bist du so in etwas vertieft, dass du die Zeit vergisst?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Inwieweit tust du täglich Dinge, bei denen du deine Stärken wirklich einsetzt?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Wie gut kennst du deine drei größten Stärken?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Wie oft tust du Dinge, die dir Energie geben, statt Energie zu kosten?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Beziehungen',
                'description' => null,
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'Wie zufrieden bist du mit den Beziehungen zu den Menschen, die dir nahestehen?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Inwieweit fühlst du dich von anderen unterstützt, wenn es schwierig wird?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Wie oft führst du aufrichtige Gespräche, in denen du dich wirklich verstanden fühlst?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Inwieweit trägst du zum Wohlbefinden der Menschen um dich herum bei?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Sinn',
                'description' => null,
                'sort_order' => 4,
                'questions' => [
                    [
                        'prompt' => 'Inwieweit hat das, was du täglich tust, Bedeutung für dich?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Wie stark spürst du ein persönliches Ziel oder eine Richtung in deinem Leben?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Inwieweit trägst du zu etwas bei, das größer ist als du selbst?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Wie oft hast du das Gefühl, dass du wichtig bist - für andere oder für die Welt?',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Erfüllung',
                'description' => null,
                'sort_order' => 5,
                'questions' => [
                    [
                        'prompt' => 'Wie zufrieden bist du mit dem, was du in letzter Zeit erreicht hast?',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Inwieweit setzt du dir Ziele und arbeitest aktiv darauf hin?',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Wie oft hast du das Gefühl, voranzukommen - und sei es nur in kleinen Schritten?',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Inwieweit blickst du zufrieden auf das zurück, was du in deinem Leben aufgebaut hast?',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];
    }
}
