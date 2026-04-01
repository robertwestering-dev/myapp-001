<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        if (BlogPost::query()->exists()) {
            return;
        }

        $admin = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('id')
            ->first();

        BlogPost::query()->create([
            'author_id' => $admin?->id,
            'slug' => 'digitale-transformatie-zonder-ruis',
            'cover_image_url' => null,
            'tags' => ['Digitale transformatie', 'Leiderschap', 'Adaptability'],
            'title' => [
                'nl' => 'Digitale transformatie zonder ruis begint bij duidelijke taal',
                'en' => 'Digital transformation without noise starts with clear language',
                'de' => 'Digitale Transformation ohne Rauschen beginnt mit klarer Sprache',
            ],
            'excerpt' => [
                'nl' => 'Een praktische kijk op hoe organisaties verandering concreet, uitlegbaar en werkbaar houden voor teams.',
                'en' => 'A practical view on how organizations keep change concrete, explainable, and workable for teams.',
                'de' => 'Ein praktischer Blick darauf, wie Organisationen Veränderung konkret, verständlich und umsetzbar für Teams halten.',
            ],
            'content' => [
                'nl' => "# Verandering landt pas als mensen begrijpen wat er van hen verwacht wordt\n\nDigitale transformatie vraagt niet alleen om nieuwe tooling, maar ook om rust, richting en consistente uitleg.\n\n## Wat goed werkt in de praktijk\n\n- Maak zichtbaar waarom de verandering nodig is.\n- Vertaal abstracte doelen naar concreet gedrag.\n- Gebruik metingen om gesprekken scherper te maken.\n\nWanneer teams weten wat prioriteit heeft, ontstaat minder ruis en meer eigenaarschap.",
                'en' => "# Change only lands when people understand what is expected of them\n\nDigital transformation is not just about tooling, but also about calm, direction, and consistent explanation.\n\n## What works in practice\n\n- Make it visible why the change matters.\n- Translate abstract goals into concrete behavior.\n- Use measurement to sharpen conversations.\n\nWhen teams know what matters most, noise drops and ownership grows.",
                'de' => "# Veränderung greift erst, wenn Menschen verstehen, was von ihnen erwartet wird\n\nDigitale Transformation braucht nicht nur neue Tools, sondern auch Ruhe, Richtung und konsistente Erklärung.\n\n## Was in der Praxis gut funktioniert\n\n- Machen Sie sichtbar, warum die Veränderung wichtig ist.\n- Übersetzen Sie abstrakte Ziele in konkretes Verhalten.\n- Nutzen Sie Messungen, um Gespräche zu schärfen.\n\nWenn Teams wissen, was Priorität hat, entsteht weniger Rauschen und mehr Verantwortung.",
            ],
            'is_published' => true,
            'is_featured' => true,
            'published_at' => now()->subDays(3),
        ]);

        BlogPost::query()->create([
            'author_id' => $admin?->id,
            'slug' => 'adaptability-begint-bij-duidelijkheid',
            'cover_image_url' => null,
            'tags' => ['Adaptability', 'Leiderschap', 'Teamontwikkeling'],
            'title' => [
                'nl' => 'Adaptability begint bij duidelijkheid in verwachtingen',
                'en' => 'Adaptability starts with clarity in expectations',
                'de' => 'Adaptability beginnt mit Klarheit in Erwartungen',
            ],
            'excerpt' => [
                'nl' => 'Kleine verbeteringen in uitleg, ritme en leiderschap maken verandering voor teams veel beter hanteerbaar.',
                'en' => 'Small improvements in explanation, rhythm, and leadership make change much more manageable for teams.',
                'de' => 'Kleine Verbesserungen bei Erklärung, Rhythmus und Führung machen Veränderung für Teams deutlich besser handhabbar.',
            ],
            'content' => [
                'nl' => "# Adaptability vraagt om meer dan motivatie\n\nMensen bewegen makkelijker mee wanneer verwachtingen duidelijk, haalbaar en consequent zijn.\n\n## Praktische principes\n\n- Maak rollen en prioriteiten concreet.\n- Herhaal de kernboodschap in gewone taal.\n- Geef teams ruimte om vragen vroeg te stellen.\n\nZo wordt verandering minder vaag en beter vol te houden.",
                'en' => "# Adaptability requires more than motivation\n\nPeople adapt more easily when expectations are clear, realistic, and consistent.\n\n## Practical principles\n\n- Make roles and priorities concrete.\n- Repeat the core message in plain language.\n- Give teams room to ask questions early.\n\nThat makes change less vague and easier to sustain.",
                'de' => "# Adaptability braucht mehr als Motivation\n\nMenschen passen sich leichter an, wenn Erwartungen klar, realistisch und konsistent sind.\n\n## Praktische Prinzipien\n\n- Machen Sie Rollen und Prioritäten konkret.\n- Wiederholen Sie die Kernbotschaft in klarer Sprache.\n- Geben Sie Teams früh Raum für Fragen.\n\nSo wird Veränderung weniger vage und besser durchzuhalten.",
            ],
            'is_published' => true,
            'is_featured' => false,
            'published_at' => now()->subDays(8),
        ]);

        BlogPost::query()->create([
            'author_id' => $admin?->id,
            'slug' => 'digitale-weerbaarheid-zonder-paniek',
            'cover_image_url' => null,
            'tags' => ['Digitale weerbaarheid', 'Digitale transformatie', 'AI adoptie'],
            'title' => [
                'nl' => 'Digitale weerbaarheid groeit niet door meer druk, maar door betere gewoonten',
                'en' => 'Digital resilience grows through better habits, not more pressure',
                'de' => 'Digitale Resilienz wächst durch bessere Gewohnheiten, nicht durch mehr Druck',
            ],
            'excerpt' => [
                'nl' => 'Digitale rust en stabiel gedrag ontstaan vaak uit simpele routines die medewerkers direct kunnen toepassen.',
                'en' => 'Digital calm and stable behavior often come from simple routines employees can apply right away.',
                'de' => 'Digitale Ruhe und stabiles Verhalten entstehen oft durch einfache Routinen, die Mitarbeitende sofort anwenden können.',
            ],
            'content' => [
                'nl' => "# Digitale weerbaarheid is praktisch gedrag\n\nVeel organisaties zoeken grote oplossingen, terwijl juist kleine routines dagelijks het verschil maken.\n\n## Wat helpt direct\n\n- Spreek focusblokken af zonder ruis.\n- Maak meldingen en kanalen bewuster.\n- Normaliseer korte herstelmomenten tijdens drukte.\n\nZo ontstaat een werkomgeving waarin digitale druk minder snel ontspoort.",
                'en' => "# Digital resilience is practical behavior\n\nMany organizations look for big solutions, while small routines often make the daily difference.\n\n## What helps immediately\n\n- Agree on focused blocks without noise.\n- Be more deliberate with notifications and channels.\n- Normalize short recovery moments during busy periods.\n\nThat creates a work environment where digital pressure is less likely to spiral.",
                'de' => "# Digitale Resilienz ist praktisches Verhalten\n\nViele Organisationen suchen nach großen Lösungen, obwohl kleine Routinen im Alltag oft den Unterschied machen.\n\n## Was sofort hilft\n\n- Vereinbaren Sie Fokusblöcke ohne Rauschen.\n- Gehen Sie bewusster mit Benachrichtigungen und Kanälen um.\n- Normalisieren Sie kurze Erholungsmomente in hektischen Phasen.\n\nSo entsteht ein Arbeitsumfeld, in dem digitaler Druck seltener eskaliert.",
            ],
            'is_published' => true,
            'is_featured' => false,
            'published_at' => now()->subDays(14),
        ]);
    }
}
