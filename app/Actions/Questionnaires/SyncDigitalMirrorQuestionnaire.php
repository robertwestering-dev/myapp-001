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

    public const ENGLISH_TITLE = 'The digital mirror';

    public const GERMAN_TITLE = 'Der digitale Spiegel';

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

    /**
     * @var array<int, string>
     */
    protected const AGREEMENT_OPTIONS_GERMAN = [
        'Stimme überhaupt nicht zu',
        'Stimme nicht zu',
        'Neutral',
        'Stimme zu',
        'Stimme voll und ganz zu',
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
     *     agreement_options: array<int, string>,
     *     categories: array<int, array{
     *         title: string,
     *         description: string|null,
     *         sort_order: int,
     *         questions: array<int, array{
     *             prompt: string,
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
                'description' => 'Deze vragenlijst brengt in kaart hoe iemand kijkt naar eigen digitale groei, stress, veerkracht en aanpassingsvermogen in de praktijk.',
                'agreement_options' => self::AGREEMENT_OPTIONS,
                'categories' => $this->dutchCategories(),
            ],
            [
                'locale' => 'en',
                'title' => self::ENGLISH_TITLE,
                'description' => 'This questionnaire maps how someone views their own digital growth, stress, resilience, and adaptability in practice.',
                'agreement_options' => self::AGREEMENT_OPTIONS_ENGLISH,
                'categories' => $this->englishCategories(),
            ],
            [
                'locale' => 'de',
                'title' => self::GERMAN_TITLE,
                'description' => 'Dieser Fragebogen zeigt, wie jemand das eigene digitale Wachstum, Stress, Resilienz und Anpassungsfähigkeit in der Praxis einschätzt.',
                'agreement_options' => self::AGREEMENT_OPTIONS_GERMAN,
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
                'title' => 'Positive foundation',
                'description' => null,
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'I know which activities give me energy and consciously make room for them.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I can name my three greatest strengths without having to think for long.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'My work or daily activities feel meaningful to me.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'When I think about my future, I generally feel optimistic.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Growth mindset and grit',
                'description' => null,
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'I believe I can learn digital skills, even when it is difficult at first.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'When I get stuck with something new, I see it as part of the learning process rather than a sign that I cannot do it.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I do not give up quickly when something does not work on my first attempt.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'The thought "this is not for me" holds me back when learning new digital things.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Resilience',
                'description' => null,
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'When something goes wrong with a digital tool or system, I recover quickly and continue.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I can regulate my emotions sufficiently when I become frustrated by technology.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I trust that I can eventually handle unfamiliar digital challenges.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'When a colleague picks up a new tool faster than I do, that says little about my own ability.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Stress and the brain',
                'description' => null,
                'sort_order' => 4,
                'questions' => [
                    [
                        'prompt' => 'When I have to work with a new or unfamiliar tool, I feel tension or resistance in my body.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I recognize when I am in a stress response and can do something with that.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'After an unpleasant digital experience (crash, error, confusing update), I calm down quickly.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I have techniques to clear my head when digital pressure increases.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Self-leadership',
                'description' => null,
                'sort_order' => 5,
                'questions' => [
                    [
                        'prompt' => 'I set achievable goals for myself when I want to learn something new digitally.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I know how to motivate myself to keep going, even when I do not feel like it.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I regularly postpone digital learning tasks.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'I have a method or system to track what I have already learned and what I still want to learn.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Unlearning and adapting',
                'description' => null,
                'sort_order' => 6,
                'questions' => [
                    [
                        'prompt' => 'I am willing to let go of familiar ways of working when a new approach works better.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'When someone says I should do something differently than I am used to, I quickly feel defensive.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I can experiment with new digital ways of working, even when I am not yet sure they will work.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'My many years of experience are sometimes a reason for me to avoid new ways of working.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Digital resilience in practice',
                'description' => null,
                'sort_order' => 7,
                'questions' => [
                    [
                        'prompt' => 'When a new digital tool is rolled out at work, I generally respond with curiosity rather than resistance.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'I feel confident enough to ask others for help when I get stuck digitally.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'I adapt reasonably quickly when digital systems or procedures change.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Looking back over the past two years, I can clearly see that my digital skills have grown.',
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
                'title' => 'Positive Grundlage',
                'description' => null,
                'sort_order' => 1,
                'questions' => [
                    [
                        'prompt' => 'Ich weiß, welche Aktivitäten mir Energie geben, und schaffe bewusst Raum dafür.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ich kann meine drei größten Stärken nennen, ohne lange darüber nachdenken zu müssen.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Meine Arbeit oder meine täglichen Aktivitäten fühlen sich für mich sinnvoll an.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Wenn ich an meine Zukunft denke, fühle ich mich im Allgemeinen optimistisch.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Wachstumsdenken und Ausdauer',
                'description' => null,
                'sort_order' => 2,
                'questions' => [
                    [
                        'prompt' => 'Ich glaube, dass ich digitale Fähigkeiten lernen kann, auch wenn es am Anfang schwierig ist.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Wenn ich bei etwas Neuem nicht weiterkomme, sehe ich das als Teil des Lernprozesses und nicht als Zeichen, dass ich es nicht kann.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ich gebe nicht schnell auf, wenn mir etwas beim ersten Versuch nicht gelingt.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Der Gedanke "Das ist nichts für mich" hält mich davon ab, neue digitale Dinge zu lernen.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Resilienz',
                'description' => null,
                'sort_order' => 3,
                'questions' => [
                    [
                        'prompt' => 'Wenn mit einem digitalen Tool oder System etwas schiefgeht, erhole ich mich schnell und mache weiter.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ich kann meine Emotionen ausreichend regulieren, wenn ich durch Technologie frustriert bin.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ich vertraue darauf, dass ich auch unbekannte digitale Herausforderungen letztlich bewältigen kann.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Wenn ein Kollege ein neues Tool schneller versteht als ich, sagt das wenig über meine eigenen Fähigkeiten aus.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Stress und das Gehirn',
                'description' => null,
                'sort_order' => 4,
                'questions' => [
                    [
                        'prompt' => 'Wenn ich mit einem neuen oder unbekannten Tool arbeiten muss, spüre ich Anspannung oder Widerstand in meinem Körper.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ich erkenne, wenn ich in einer Stressreaktion bin, und kann etwas damit anfangen.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Nach einer unangenehmen digitalen Erfahrung (Absturz, Fehler, unverständliches Update) komme ich schnell wieder zur Ruhe.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ich verfüge über Techniken, um meinen Kopf freizubekommen, wenn digitaler Druck zunimmt.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Selbstführung',
                'description' => null,
                'sort_order' => 5,
                'questions' => [
                    [
                        'prompt' => 'Ich setze mir erreichbare Ziele, wenn ich digital etwas Neues lernen möchte.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ich weiß, wie ich mich motivieren kann weiterzumachen, auch wenn ich gerade keine Lust habe.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ich schiebe digitale Lernaufgaben regelmäßig auf.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Ich habe eine Methode oder ein System, um festzuhalten, was ich bereits gelernt habe und was ich noch lernen möchte.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Verlernen und Anpassen',
                'description' => null,
                'sort_order' => 6,
                'questions' => [
                    [
                        'prompt' => 'Ich bin bereit, vertraute Arbeitsweisen loszulassen, wenn ein neuer Ansatz besser funktioniert.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Wenn jemand sagt, dass ich etwas anders machen sollte, als ich es gewohnt bin, fühle ich mich schnell defensiv.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ich kann mit neuen digitalen Arbeitsweisen experimentieren, auch wenn ich noch nicht sicher bin, ob sie funktionieren.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Meine langjährige Erfahrung ist für mich manchmal ein Grund, neue Arbeitsweisen zu vermeiden.',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Digitale Resilienz in der Praxis',
                'description' => null,
                'sort_order' => 7,
                'questions' => [
                    [
                        'prompt' => 'Wenn bei meiner Arbeit ein neues digitales Tool eingeführt wird, reagiere ich darauf im Allgemeinen mit Neugier statt mit Widerstand.',
                        'sort_order' => 1,
                    ],
                    [
                        'prompt' => 'Ich fühle mich sicher genug, andere um Hilfe zu bitten, wenn ich digital nicht weiterkomme.',
                        'sort_order' => 2,
                    ],
                    [
                        'prompt' => 'Ich passe mich recht schnell an, wenn sich digitale Systeme oder Abläufe ändern.',
                        'sort_order' => 3,
                    ],
                    [
                        'prompt' => 'Wenn ich auf die letzten zwei Jahre zurückblicke, sehe ich deutlich, dass meine digitalen Fähigkeiten gewachsen sind.',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];
    }
}
