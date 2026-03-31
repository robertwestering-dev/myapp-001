<?php

namespace Database\Seeders;

use App\Models\AcademyCourse;
use Illuminate\Database\Seeder;

class AcademyCourseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->courses() as $course) {
            AcademyCourse::query()->updateOrCreate(
                ['slug' => $course['slug']],
                $course,
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function courses(): array
    {
        return [
            [
                'slug' => 'adaptability-foundations',
                'theme' => AcademyCourse::THEME_ADAPTABILITY,
                'path' => 'academy-courses/adaptability-foundations',
                'estimated_minutes' => 45,
                'sort_order' => 10,
                'is_active' => true,
                'title' => [
                    'nl' => 'Adaptability Fundamentals',
                    'en' => 'Adaptability Fundamentals',
                    'de' => 'Adaptability Fundamentals',
                    'fr' => 'Adaptability Fundamentals',
                ],
                'audience' => [
                    'nl' => 'Medewerkers en professionals die vaker moeten schakelen in een veranderende digitale context.',
                    'en' => 'Employees and professionals who need to switch gears more often in a changing digital context.',
                    'de' => 'Mitarbeitende und Fachkrafte, die in einem sich verandernden digitalen Umfeld haufiger umschalten mussen.',
                    'fr' => 'Collaborateurs et professionnels qui doivent s adapter plus souvent dans un contexte numerique en evolution.',
                ],
                'goal' => [
                    'nl' => 'Het vergroten van het aanpassingsvermogen in dagelijkse werkpraktijk en verandertrajecten.',
                    'en' => 'To strengthen adaptability in daily work and throughout transformation initiatives.',
                    'de' => 'Die Anpassungsfahigkeit im Arbeitsalltag und in Transformationsvorhaben zu starken.',
                    'fr' => 'Renforcer l adaptabilite dans le travail quotidien et dans les trajectoires de transformation.',
                ],
                'summary' => [
                    'nl' => 'Een compacte basistraining over omgaan met verandering, onzekerheid en nieuwe verwachtingen in teams en organisaties.',
                    'en' => 'A compact foundational training on dealing with change, uncertainty, and new expectations in teams and organizations.',
                    'de' => 'Ein kompaktes Basistraining zum Umgang mit Veranderung, Unsicherheit und neuen Erwartungen in Teams und Organisationen.',
                    'fr' => 'Une formation de base concise sur la gestion du changement, de l incertitude et des nouvelles attentes dans les equipes et les organisations.',
                ],
                'learning_goals' => [
                    'nl' => [
                        'Herkennen welke factoren adaptability versterken of verzwakken.',
                        'Leren hoe gedrag, mindset en context elkaar tijdens verandering beinvloeden.',
                        'Oefenen met praktische routines om sneller en rustiger mee te bewegen.',
                    ],
                    'en' => [
                        'Recognize which factors strengthen or weaken adaptability.',
                        'Understand how behavior, mindset, and context influence each other during change.',
                        'Practice practical routines to adapt more quickly and calmly.',
                    ],
                    'de' => [
                        'Erkennen, welche Faktoren Anpassungsfahigkeit starken oder schwachen.',
                        'Verstehen, wie Verhalten, Mindset und Kontext sich wahrend Veranderung gegenseitig beeinflussen.',
                        'Praktische Routinen einuben, um schneller und ruhiger mitzubewegen.',
                    ],
                    'fr' => [
                        'Identifier les facteurs qui renforcent ou affaiblissent l adaptabilite.',
                        'Comprendre comment le comportement, l etat d esprit et le contexte interagissent pendant le changement.',
                        'Pratiquer des routines concretes pour s ajuster plus vite et plus sereinement.',
                    ],
                ],
                'contents' => [
                    'nl' => [
                        'Uitleg van de kernprincipes van adaptability.',
                        'Praktische voorbeelden uit digitale transformatie en teamdynamiek.',
                        'Reflectievragen en concrete acties voor de eigen werkcontext.',
                    ],
                    'en' => [
                        'Explanation of the core principles behind adaptability.',
                        'Practical examples from digital transformation and team dynamics.',
                        'Reflection prompts and concrete actions for personal work contexts.',
                    ],
                    'de' => [
                        'Erklarung der Grundprinzipien von Adaptability.',
                        'Praxisbeispiele aus digitaler Transformation und Teamdynamik.',
                        'Reflexionsfragen und konkrete Aktionen fur den eigenen Arbeitskontext.',
                    ],
                    'fr' => [
                        'Presentation des principes essentiels de l adaptability.',
                        'Exemples concrets lies a la transformation numerique et a la dynamique d equipe.',
                        'Questions de reflexion et actions concretes pour son propre contexte de travail.',
                    ],
                ],
            ],
            [
                'slug' => 'digital-resilience-basics',
                'theme' => AcademyCourse::THEME_RESILIENCE,
                'path' => 'academy-courses/digital-resilience-basics',
                'estimated_minutes' => 35,
                'sort_order' => 20,
                'is_active' => true,
                'title' => [
                    'nl' => 'Digital Resilience Basics',
                    'en' => 'Digital Resilience Basics',
                    'de' => 'Digital Resilience Basics',
                    'fr' => 'Digital Resilience Basics',
                ],
                'audience' => [
                    'nl' => 'Medewerkers die werken met meerdere systemen, digitale communicatie en hoge informatiebelasting.',
                    'en' => 'Employees working across multiple systems, digital communication flows, and high information loads.',
                    'de' => 'Mitarbeitende, die mit mehreren Systemen, digitaler Kommunikation und hoher Informationslast arbeiten.',
                    'fr' => 'Collaborateurs qui travaillent avec plusieurs systemes, de nombreux flux numeriques et une forte charge informationnelle.',
                ],
                'goal' => [
                    'nl' => 'Het versterken van digitale weerbaarheid en het verkleinen van stress en foutgevoeligheid in digitale werkomgevingen.',
                    'en' => 'To strengthen digital resilience and reduce stress and error sensitivity in digital work environments.',
                    'de' => 'Digitale Resilienz zu starken und Stress sowie Fehleranfalligkeit in digitalen Arbeitsumgebungen zu reduzieren.',
                    'fr' => 'Renforcer la resilience numerique et reduire le stress ainsi que la sensibilite aux erreurs dans les environnements de travail numeriques.',
                ],
                'summary' => [
                    'nl' => 'Een gerichte e-learning over digitaal zelfvertrouwen, focus en gezonde gewoonten in een omgeving met continue digitale prikkels.',
                    'en' => 'A focused e-learning on digital confidence, focus, and healthy habits in an environment full of constant digital input.',
                    'de' => 'Ein fokussiertes E-Learning zu digitalem Selbstvertrauen, Konzentration und gesunden Gewohnheiten in einer Umgebung voller permanenter digitaler Reize.',
                    'fr' => 'Un e-learning cible sur la confiance numerique, la concentration et les habitudes saines dans un environnement rempli de sollicitations digitales continues.',
                ],
                'learning_goals' => [
                    'nl' => [
                        'Inzicht krijgen in signalen van digitale overbelasting.',
                        'Leren hoe medewerkers digitaal alert en zelfstandig kunnen blijven.',
                        'Opbouwen van eenvoudige gewoonten voor focus, veiligheid en veerkracht.',
                    ],
                    'en' => [
                        'Recognize the signals of digital overload.',
                        'Learn how employees can stay digitally alert and self-reliant.',
                        'Build simple habits for focus, safety, and resilience.',
                    ],
                    'de' => [
                        'Signale digitaler Uberlastung erkennen.',
                        'Lernen, wie Mitarbeitende digital aufmerksam und selbststandig bleiben.',
                        'Einfache Gewohnheiten fur Fokus, Sicherheit und Resilienz aufbauen.',
                    ],
                    'fr' => [
                        'Reconnaitre les signes de surcharge numerique.',
                        'Comprendre comment rester autonome et vigilant dans le travail digital.',
                        'Installer des habitudes simples pour la concentration, la securite et la resilience.',
                    ],
                ],
                'contents' => [
                    'nl' => [
                        'Herkennen van digitale druk en onderbrekingspatronen.',
                        'Praktische tips voor focus, herstel en digitaal bewustzijn.',
                        'Concrete oefeningen voor veiliger en stabieler digitaal werken.',
                    ],
                    'en' => [
                        'Recognizing digital pressure and interruption patterns.',
                        'Practical tips for focus, recovery, and digital awareness.',
                        'Concrete exercises for safer and more stable digital work.',
                    ],
                    'de' => [
                        'Erkennen von digitalem Druck und Unterbrechungsmustern.',
                        'Praktische Tipps fur Fokus, Erholung und digitales Bewusstsein.',
                        'Konkrete Ubungen fur sichereres und stabileres digitales Arbeiten.',
                    ],
                    'fr' => [
                        'Identifier la pression numerique et les schemas d interruption.',
                        'Conseils pratiques pour la concentration, la recuperation et la conscience numerique.',
                        'Exercices concrets pour travailler de maniere plus sure et plus stable.',
                    ],
                ],
            ],
        ];
    }
}
