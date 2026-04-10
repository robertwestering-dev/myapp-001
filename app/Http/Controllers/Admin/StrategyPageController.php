<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class StrategyPageController extends Controller
{
    /**
     * @return array<string, array{slug: string, title: string, eyebrow: string, heading: string, lead: string, sections: array<int, array{title: string, body: array<int, string>}>}>
     */
    protected function pages(): array
    {
        return [
            'homepage-copy' => [
                'slug' => 'homepage-copy',
                'title' => 'Homepage-copy',
                'eyebrow' => 'Strategie',
                'heading' => 'Homepage-copy voor Hermes Results',
                'lead' => 'Deze versie positioneert Hermes Results tegelijk als gratis ontwikkelplatform voor individuen en als inzichtsplatform voor organisaties.',
                'sections' => [
                    [
                        'title' => 'Hero',
                        'body' => [
                            'Kop: Word sterker in digitale verandering',
                            'Subkop: Hermes Results helpt mensen en organisaties om digitale vaardigheden, weerbaarheid en adaptability zichtbaar en ontwikkelbaar te maken.',
                            'Primaire CTA: Maak gratis een account aan',
                            'Secundaire CTA: Bekijk de zakelijke mogelijkheden',
                        ],
                    ],
                    [
                        'title' => 'Kernboodschap',
                        'body' => [
                            'Voor individuele gebruikers biedt Hermes Results gratis trainingen, gratis scans en een gratis forum om te leren, te testen en mee te praten over digitale ontwikkeling.',
                            'Voor organisaties biedt Hermes Results organisatiebrede scans, rapportages en inzichten die helpen om digitale transformatie, adoptie en ontwikkelbehoeften beter te begrijpen.',
                        ],
                    ],
                    [
                        'title' => 'Bewijsblokken',
                        'body' => [
                            'Gratis Academy: praktische trainingen die gebruikers direct kunnen volgen.',
                            'Gratis quick scans: laagdrempelige tests voor digitale skills, weerbaarheid en adaptability.',
                            'Gratis forum: ruimte om vragen te stellen en ervaringen te delen met andere ingelogde gebruikers.',
                            'Zakelijke rapportages: inzicht op groeps- en organisatieniveau voor management, HR en L&D.',
                        ],
                    ],
                    [
                        'title' => 'Afsluiter',
                        'body' => [
                            'Hermes Results combineert leren, meten en kennisdeling in een platform dat individuen gratis verder helpt en organisaties beter laat sturen op digitale verandering.',
                        ],
                    ],
                ],
            ],
            'zakelijke-landingspagina' => [
                'slug' => 'zakelijke-landingspagina',
                'title' => 'Zakelijke landingspagina',
                'eyebrow' => 'B2B',
                'heading' => 'Zakelijke landingspagina voor bedrijfsaccounts',
                'lead' => 'Deze pagina vertaalt je gratis gebruikersaanbod naar een duidelijke zakelijke propositie voor werkgevers en organisaties.',
                'sections' => [
                    [
                        'title' => 'Hero',
                        'body' => [
                            'Kop: Maak digitale uitdagingen in je organisatie zichtbaar',
                            'Subkop: Geef medewerkers toegang tot scans en leercontent, en ontvang rapportages die laten zien waar digitale weerbaarheid, adaptability en ontwikkelbehoeften aandacht vragen.',
                            'CTA 1: Vraag een demo aan',
                            'CTA 2: Vraag een bedrijfsaccount aan',
                        ],
                    ],
                    [
                        'title' => 'Het probleem',
                        'body' => [
                            'Veel organisaties investeren in digitale verandering, maar missen zicht op hoe medewerkers daarin meekomen.',
                            'Daardoor blijven onzekerheid, weerstand, vaardigheidstekorten en overbelasting vaak te lang onzichtbaar.',
                        ],
                    ],
                    [
                        'title' => 'De oplossing',
                        'body' => [
                            'Met Hermes Results bied je medewerkers laagdrempelig toegang tot scans, leercontent en een kenniscommunity.',
                            'Tegelijkertijd ontvang je als organisatie geaggregeerde rapportages en trends die helpen om gerichter te sturen.',
                        ],
                    ],
                    [
                        'title' => 'Wat medewerkers krijgen',
                        'body' => [
                            'Toegang tot relevante scans en quick scans.',
                            'Persoonlijk inzicht in digitale vaardigheden, weerbaarheid en adaptability.',
                            'Gratis trainingen in de Academy.',
                            'Een forum om vragen te stellen en ervaringen uit te wisselen.',
                        ],
                    ],
                    [
                        'title' => 'Wat organisaties krijgen',
                        'body' => [
                            'Deelname-inzicht en responsontwikkeling.',
                            'Rapportages op groeps- en organisatieniveau.',
                            'Trends over tijd en signalering van aandachtspunten.',
                            'Input voor HR, L&D, change, leiderschap en digitale transformatie.',
                        ],
                    ],
                ],
            ],
            'pricing-en-abonnementen' => [
                'slug' => 'pricing-en-abonnementen',
                'title' => 'Pricing en abonnementen',
                'eyebrow' => 'Verdienmodel',
                'heading' => 'Concept voor pricing en abonnementen',
                'lead' => 'Dit model verkoopt geen losse vragenlijsten, maar inzicht, stuurinformatie en opvolging voor organisaties.',
                'sections' => [
                    [
                        'title' => 'Positionering',
                        'body' => [
                            'Individuele gebruikers houden gratis toegang tot Academy, scans en forum.',
                            'De betaalde laag zit in organisatiebrede deelname, dashboards, rapportages, trendanalyse en eventuele adviesondersteuning.',
                        ],
                    ],
                    [
                        'title' => 'Pakket 1: Team',
                        'body' => [
                            'Voor kleine organisaties, pilots of eerste interne trajecten.',
                            'Inclusief bedrijfsaccount, medewerkers uitnodigen, standaardscans, basisrapportage en periodieke meting.',
                        ],
                    ],
                    [
                        'title' => 'Pakket 2: Organisatie',
                        'body' => [
                            'Voor bredere uitrol binnen meerdere teams of afdelingen.',
                            'Inclusief alles uit Team, plus uitgebreidere dashboards, trends over tijd, exports en vergelijkingen tussen groepen.',
                        ],
                    ],
                    [
                        'title' => 'Pakket 3: Insight Plus',
                        'body' => [
                            'Voor organisaties die echt willen sturen op inzichten en opvolging.',
                            'Inclusief alles uit Organisatie, plus maatwerkrapportages, benchmarkmogelijkheden, extra duiding en periodieke reviewgesprekken.',
                        ],
                    ],
                    [
                        'title' => 'Commerciële boodschap',
                        'body' => [
                            'Verkoop op inzicht, voortgang en besluitvorming, niet op het aantal scans.',
                            'De kernvraag voor klanten is: waar lopen mensen vast in digitale verandering, en wat vraagt nu aandacht?',
                        ],
                    ],
                ],
            ],
            'privacy-en-vertrouwen' => [
                'slug' => 'privacy-en-vertrouwen',
                'title' => 'Privacy en vertrouwen',
                'eyebrow' => 'Vertrouwen',
                'heading' => 'Privacy- en vertrouwensboodschap',
                'lead' => 'Omdat je model deels op data steunt, is vertrouwen niet alleen juridisch maar ook commercieel een kernvoorwaarde.',
                'sections' => [
                    [
                        'title' => 'Heldere belofte',
                        'body' => [
                            'Hermes Results helpt gebruikers en organisaties met inzicht en ontwikkeling, maar gaat zorgvuldig om met persoonlijke gegevens.',
                            'Rapportages voor organisaties moeten primair gericht zijn op patronen en inzichten op groepsniveau.',
                        ],
                    ],
                    [
                        'title' => 'Wat je expliciet moet maken',
                        'body' => [
                            'Welke data je verzamelt en waarom.',
                            'Wat individuele gebruikers zelf zien.',
                            'Wat organisaties wel en niet te zien krijgen.',
                            'Dat analyses voor artikelen, benchmarks of zakelijke inzichten geanonimiseerd worden gebruikt.',
                        ],
                    ],
                    [
                        'title' => 'Ethische grens',
                        'body' => [
                            'Voorkom dat medewerkers het gevoel krijgen dat persoonlijke kwetsbaarheid of onzekerheid direct bij hun werkgever terechtkomt.',
                            'Werk daarom met geaggregeerde rapportages, duidelijke minimum groepsgroottes en begrijpelijke uitleg in gewone taal.',
                        ],
                    ],
                    [
                        'title' => 'Website-copy',
                        'body' => [
                            'Wij geloven dat betere digitale ontwikkeling begint met inzicht en vertrouwen.',
                            'Daarom geven we individuele gebruikers heldere feedback over hun eigen resultaten en gebruiken we organisatie-inzichten zorgvuldig en op passend niveau.',
                            'Wanneer data wordt gebruikt voor analyses, artikelen of benchmarks, gebeurt dat geanonimiseerd en met respect voor de context waarin die data is verzameld.',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function index(): View
    {
        return view('admin.strategy-pages.index', [
            'pages' => array_values($this->pages()),
        ]);
    }

    public function show(string $page): View
    {
        abort_unless(array_key_exists($page, $this->pages()), 404);

        return view('admin.strategy-pages.show', [
            'page' => $this->pages()[$page],
            'pages' => array_values($this->pages()),
        ]);
    }

    public function preview(string $page): View
    {
        abort_unless(array_key_exists($page, $this->pages()), 404);

        return view('admin.strategy-pages.preview', [
            'page' => $this->pages()[$page],
            'pages' => array_values($this->pages()),
        ]);
    }
}
