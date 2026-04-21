<?php

namespace App\Support\Questionnaires;

use App\Actions\Questionnaires\SyncDigitalMirrorQuestionnaire;
use App\Actions\Questionnaires\SyncPositiveFoundationQuestionnaire;
use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Support\Collection;

class LocalizedQuestionnaireContent
{
    /**
     * Keep the canonical questionnaire/category structure, but expose only the
     * questions that best match the active locale.
     */
    public function apply(Questionnaire $questionnaire, string $locale): Questionnaire
    {
        $questionnaire->loadMissing([
            'categories' => fn ($query) => $query->orderBy('sort_order'),
            'categories.questions' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $questionnaire->setAttribute('localized_title', $this->questionnaireTitle($questionnaire, $locale));
        $questionnaire->setAttribute('localized_description', $this->questionnaireDescription($questionnaire, $locale));

        $categories = $questionnaire->categories
            ->map(function (QuestionnaireCategory $category) use ($locale, $questionnaire): QuestionnaireCategory {
                $category->setAttribute('localized_title', $this->categoryTitle($questionnaire, $category, $locale));
                $category->setAttribute('localized_description', $this->categoryDescription($questionnaire, $category, $locale));
                $category->setRelation(
                    'questions',
                    $this->questionsForLocale($category->questions, $locale),
                );

                return $category;
            })
            ->values();

        $questionnaire->setRelation('categories', $categories);

        return $questionnaire;
    }

    /**
     * @param  Collection<int, QuestionnaireQuestion>  $questions
     * @return Collection<int, QuestionnaireQuestion>
     */
    public function questionsForLocale(Collection $questions, string $locale): Collection
    {
        return $questions
            ->groupBy('sort_order')
            ->flatMap(function (Collection $questionsAtPosition) use ($locale): Collection {
                $localizedQuestions = $questionsAtPosition->where('locale', $locale);

                if ($localizedQuestions->isNotEmpty()) {
                    return $localizedQuestions;
                }

                $neutralQuestions = $questionsAtPosition
                    ->filter(fn (QuestionnaireQuestion $question): bool => blank($question->locale));

                if ($neutralQuestions->isNotEmpty()) {
                    return $neutralQuestions;
                }

                return $questionsAtPosition->take(1);
            })
            ->sortBy('sort_order')
            ->values();
    }

    public function questionnaireTitle(Questionnaire $questionnaire, string $locale): string
    {
        return (string) data_get(
            $this->questionnaireTranslations($questionnaire),
            "{$locale}.title",
            $questionnaire->title,
        );
    }

    public function questionnaireDescription(Questionnaire $questionnaire, string $locale): ?string
    {
        return data_get(
            $this->questionnaireTranslations($questionnaire),
            "{$locale}.description",
            $questionnaire->description,
        );
    }

    public function categoryTitle(Questionnaire $questionnaire, QuestionnaireCategory $category, string $locale): string
    {
        return (string) data_get(
            $this->categoryTranslations($questionnaire, $category),
            "{$locale}.title",
            $category->title,
        );
    }

    public function categoryDescription(Questionnaire $questionnaire, QuestionnaireCategory $category, string $locale): ?string
    {
        return data_get(
            $this->categoryTranslations($questionnaire, $category),
            "{$locale}.description",
            $category->description,
        );
    }

    /**
     * @return array<string, array{title: string, description: string}>
     */
    protected function questionnaireTranslations(Questionnaire $questionnaire): array
    {
        return match ($questionnaire->title) {
            SyncDigitalMirrorQuestionnaire::TITLE => [
                'en' => [
                    'title' => SyncDigitalMirrorQuestionnaire::ENGLISH_TITLE,
                    'description' => 'This questionnaire maps how you view your own digital growth, stress, resilience, and adaptability in practice.',
                ],
                'de' => [
                    'title' => SyncDigitalMirrorQuestionnaire::GERMAN_TITLE,
                    'description' => 'Dieser Fragebogen zeigt, wie Sie Ihr eigenes digitales Wachstum, Stress, Resilienz und Anpassungsfähigkeit in der Praxis einschätzen.',
                ],
            ],
            SyncPositiveFoundationQuestionnaire::TITLE => [
                'en' => [
                    'title' => SyncPositiveFoundationQuestionnaire::ENGLISH_TITLE,
                    'description' => 'This PERMA questionnaire maps the positive foundation through positive emotion, engagement, relationships, meaning, and accomplishment.',
                ],
                'de' => [
                    'title' => SyncPositiveFoundationQuestionnaire::GERMAN_TITLE,
                    'description' => 'Dieser PERMA-Fragebogen erfasst das positive Fundament anhand von positiver Emotion, Engagement, Beziehungen, Sinn und Erfüllung.',
                ],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, array{title: string, description: string|null}>
     */
    protected function categoryTranslations(Questionnaire $questionnaire, QuestionnaireCategory $category): array
    {
        return match ($questionnaire->title) {
            SyncDigitalMirrorQuestionnaire::TITLE => [
                'en' => [
                    'title' => match ((int) $category->sort_order) {
                        1 => 'Positive foundation',
                        2 => 'Growth mindset and grit',
                        3 => 'Resilience',
                        4 => 'Stress and the brain',
                        5 => 'Self-leadership',
                        6 => 'Unlearning and adapting',
                        7 => 'Digital resilience in practice',
                        default => $category->title,
                    },
                    'description' => null,
                ],
                'de' => [
                    'title' => match ((int) $category->sort_order) {
                        1 => 'Positive Grundlage',
                        2 => 'Wachstumsdenken und Ausdauer',
                        3 => 'Resilienz',
                        4 => 'Stress und das Gehirn',
                        5 => 'Selbstführung',
                        6 => 'Verlernen und Anpassen',
                        7 => 'Digitale Resilienz in der Praxis',
                        default => $category->title,
                    },
                    'description' => null,
                ],
            ],
            SyncPositiveFoundationQuestionnaire::TITLE => [
                'en' => [
                    'title' => match ((int) $category->sort_order) {
                        1 => 'Positive emotion',
                        2 => 'Engagement',
                        3 => 'Relationships',
                        4 => 'Meaning',
                        5 => 'Accomplishment',
                        default => $category->title,
                    },
                    'description' => null,
                ],
                'de' => [
                    'title' => match ((int) $category->sort_order) {
                        1 => 'Positive Emotion',
                        2 => 'Engagement',
                        3 => 'Beziehungen',
                        4 => 'Sinn',
                        5 => 'Erfüllung',
                        default => $category->title,
                    },
                    'description' => null,
                ],
            ],
            default => [],
        };
    }
}
