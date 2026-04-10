<x-layouts.hermes-public
    :title="$page['heading'].' | Hermes Results Preview'"
    :meta-description="$page['lead']"
    :canonical-url="route('admin.strategy-pages.preview', $page['slug'])"
    :meta-image="asset('images/hermes-results-logo.png')"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $page['heading'],
        'description' => $page['lead'],
        'url' => route('admin.strategy-pages.preview', $page['slug']),
    ]"
    :force-guest-navigation="true"
>
    <x-slot:head>
        <style>
            .preview-page,
            .preview-hero,
            .preview-grid,
            .preview-card-grid,
            .preview-actions,
            .preview-price-grid,
            .preview-promise-grid,
            .preview-nav {
                display: grid;
                gap: 24px;
            }

            .preview-banner,
            .preview-hero,
            .preview-panel,
            .preview-price-card,
            .preview-aside {
                padding: 28px;
                border-radius: 32px;
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .preview-banner,
            .preview-panel,
            .preview-price-card,
            .preview-aside {
                background: rgba(255, 255, 255, 0.72);
            }

            .preview-hero {
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.28), transparent 28%),
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98));
                color: #f8f1e7;
                overflow: hidden;
            }

            .preview-banner {
                display: flex;
                justify-content: space-between;
                gap: 16px;
                align-items: center;
                flex-wrap: wrap;
            }

            .preview-banner p,
            .preview-hero p,
            .preview-panel p,
            .preview-price-card p,
            .preview-aside p,
            .preview-list {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.7;
            }

            .preview-hero .eyebrow {
                width: fit-content;
            }

            .preview-hero h1 {
                max-width: 12ch;
                margin-bottom: 12px;
                color: #f8f1e7;
                font-size: clamp(2.6rem, 5vw, 4.8rem);
                line-height: 0.96;
            }

            .preview-hero__lede {
                max-width: 64ch;
                color: rgba(248, 241, 231, 0.84);
                font-size: 1.08rem;
            }

            .preview-actions {
                grid-auto-flow: column;
                justify-content: start;
                gap: 14px;
            }

            .preview-actions .pill {
                width: fit-content;
            }

            .preview-grid {
                grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.8fr);
                align-items: start;
            }

            .preview-card-grid,
            .preview-price-grid,
            .preview-promise-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .preview-panel h2,
            .preview-price-card h2,
            .preview-aside h2 {
                margin-bottom: 12px;
            }

            .preview-panel h3,
            .preview-price-card h3 {
                margin-bottom: 10px;
                font-size: 1.3rem;
            }

            .preview-list {
                padding-left: 20px;
            }

            .preview-list li + li {
                margin-top: 8px;
            }

            .preview-note {
                color: var(--muted);
                font-size: 0.96rem;
            }

            .preview-price-card--accent {
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
                border-color: rgba(255, 255, 255, 0.08);
            }

            .preview-price-card--accent p,
            .preview-price-card--accent .preview-list {
                color: rgba(248, 241, 231, 0.84);
            }

            .preview-kicker {
                display: inline-flex;
                width: fit-content;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(188, 91, 44, 0.12);
                color: var(--clay-deep);
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.82rem;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .preview-nav {
                grid-auto-flow: row;
                gap: 12px;
            }

            .preview-nav .pill,
            .preview-nav .ghost-pill {
                width: fit-content;
            }

            @media (max-width: 980px) {
                .preview-grid,
                .preview-card-grid,
                .preview-price-grid,
                .preview-promise-grid {
                    grid-template-columns: 1fr;
                }

                .preview-actions {
                    grid-auto-flow: row;
                }
            }
        </style>
    </x-slot:head>

    <div class="preview-page">
        <section class="preview-banner">
            <div>
                <span class="eyebrow">Admin-preview</span>
                <p>Deze pagina simuleert hoe bezoekers deze versie live zouden zien. Alleen globale admins kunnen deze preview openen.</p>
            </div>

            <div class="preview-actions">
                <a href="{{ route('admin.strategy-pages.show', $page['slug']) }}" class="pill">Terug naar strategiecopy</a>
                <a href="{{ route('admin.strategy-pages.index') }}" class="pill">Overzicht</a>
            </div>
        </section>

        @if ($page['slug'] === 'homepage-copy')
            <section class="preview-hero">
                <span class="eyebrow eyebrow--light">Gratis voor individuen, waardevol voor organisaties</span>
                <h1>Word sterker in digitale verandering</h1>
                <p class="preview-hero__lede">Hermes Results helpt mensen en organisaties om digitale vaardigheden, weerbaarheid en adaptability zichtbaar en ontwikkelbaar te maken met gratis scans, trainingen en een inhoudelijke community.</p>

                <div class="preview-actions">
                    <a href="{{ route('register') }}" class="pill pill--strong">Maak gratis een account aan</a>
                    <a href="{{ route('admin.strategy-pages.preview', 'zakelijke-landingspagina') }}" class="pill">Bekijk de zakelijke mogelijkheden</a>
                </div>
            </section>

            <section class="preview-card-grid">
                <article class="preview-panel">
                    <span class="preview-kicker">Academy</span>
                    <h2>Gratis trainingen voor digitale groei</h2>
                    <p>Gebruikers krijgen gratis toegang tot praktische trainingen die helpen om digitale ontwikkeling concreet, rustig en toepasbaar te maken.</p>
                </article>

                <article class="preview-panel">
                    <span class="preview-kicker">Scans</span>
                    <h2>Gratis quick scans voor inzicht</h2>
                    <p>Laat gebruikers hun digitale skills, weerbaarheid en adaptability testen met scans die richting geven aan leren en ontwikkeling.</p>
                </article>

                <article class="preview-panel">
                    <span class="preview-kicker">Forum</span>
                    <h2>Gratis meepraten over relevante onderwerpen</h2>
                    <p>Het forum brengt vragen, ervaringen en inzichten samen in een omgeving waar ingelogde gebruikers elkaar verder kunnen helpen.</p>
                </article>

                <article class="preview-panel">
                    <span class="preview-kicker">Organisaties</span>
                    <h2>Van individueel gebruik naar organisatie-inzicht</h2>
                    <p>Voor organisaties groeit Hermes Results door naar rapportages en stuurinformatie die laten zien waar digitale verandering vertraagt of juist tractie krijgt.</p>
                </article>
            </section>
        @endif

        @if ($page['slug'] === 'zakelijke-landingspagina')
            <section class="preview-hero">
                <span class="eyebrow eyebrow--light">Zakelijke oplossing</span>
                <h1>Maak digitale uitdagingen in je organisatie zichtbaar</h1>
                <p class="preview-hero__lede">Geef medewerkers toegang tot scans en leercontent, en ontvang rapportages die laten zien waar digitale weerbaarheid, adaptability en ontwikkelbehoeften aandacht vragen.</p>

                <div class="preview-actions">
                    <a href="{{ route('home', ['contact' => 1], false) }}#contact" class="pill pill--strong">Vraag een demo aan</a>
                    <a href="{{ route('home', ['contact' => 1], false) }}#contact" class="pill">Vraag een bedrijfsaccount aan</a>
                </div>
            </section>

            <section class="preview-grid">
                <div class="preview-card-grid">
                    <article class="preview-panel">
                        <h2>Het probleem</h2>
                        <p>Veel organisaties investeren in digitale verandering, maar missen zicht op hoe medewerkers daarin meekomen. Daardoor blijven onzekerheid, weerstand, vaardigheidstekorten en overbelasting vaak te lang onzichtbaar.</p>
                    </article>

                    <article class="preview-panel">
                        <h2>De oplossing</h2>
                        <p>Met Hermes Results bied je medewerkers laagdrempelig toegang tot scans, leercontent en een kenniscommunity. Tegelijkertijd ontvang je als organisatie geaggregeerde rapportages en trends die helpen om gerichter te sturen.</p>
                    </article>

                    <article class="preview-panel">
                        <h2>Wat medewerkers krijgen</h2>
                        <ul class="preview-list">
                            <li>Toegang tot relevante scans en quick scans</li>
                            <li>Persoonlijk inzicht in digitale vaardigheden, weerbaarheid en adaptability</li>
                            <li>Gratis trainingen in de Academy</li>
                            <li>Een forum om vragen te stellen en ervaringen uit te wisselen</li>
                        </ul>
                    </article>

                    <article class="preview-panel">
                        <h2>Wat organisaties krijgen</h2>
                        <ul class="preview-list">
                            <li>Deelname-inzicht en responsontwikkeling</li>
                            <li>Rapportages op groeps- en organisatieniveau</li>
                            <li>Trends over tijd en signalering van aandachtspunten</li>
                            <li>Input voor HR, L&amp;D, change, leiderschap en digitale transformatie</li>
                        </ul>
                    </article>
                </div>

                <aside class="preview-aside">
                    <h2>Typische use-cases</h2>
                    <ul class="preview-list">
                        <li>Digitale transformatie en adoptie van nieuwe systemen</li>
                        <li>Werkdruk en zorgen over digitale overbelasting</li>
                        <li>Nulmeting en vervolgmetingen per team of afdeling</li>
                        <li>Input voor leiderschap, HR en leerinterventies</li>
                    </ul>
                </aside>
            </section>
        @endif

        @if ($page['slug'] === 'pricing-en-abonnementen')
            <section class="preview-hero">
                <span class="eyebrow eyebrow--light">Pricing</span>
                <h1>Kies een abonnementsvorm die past bij je organisatie</h1>
                <p class="preview-hero__lede">Hermes Results verkoopt geen losse vragenlijsten, maar inzicht, stuurinformatie en handelingsperspectief voor teams en organisaties die beter met digitale verandering willen omgaan.</p>
            </section>

            <section class="preview-price-grid">
                <article class="preview-price-card">
                    <span class="preview-kicker">Team</span>
                    <h2>Voor kleine teams en pilots</h2>
                    <p>Een laagdrempelige start voor organisaties die eerst willen valideren waar kansen en knelpunten liggen.</p>
                    <ul class="preview-list">
                        <li>Bedrijfsaccount</li>
                        <li>Medewerkers uitnodigen</li>
                        <li>Standaardscans</li>
                        <li>Basisrapportage</li>
                        <li>Periodieke meting</li>
                    </ul>
                </article>

                <article class="preview-price-card preview-price-card--accent">
                    <span class="preview-kicker">Organisatie</span>
                    <h2>Voor meerdere teams of afdelingen</h2>
                    <p>Meer overzicht, meer vergelijkingsmogelijkheden en meer grip op voortgang over tijd.</p>
                    <ul class="preview-list">
                        <li>Alles uit Team</li>
                        <li>Uitgebreidere dashboards</li>
                        <li>Trends over tijd</li>
                        <li>Exports</li>
                        <li>Vergelijkingen tussen groepen</li>
                    </ul>
                </article>

                <article class="preview-price-card">
                    <span class="preview-kicker">Insight Plus</span>
                    <h2>Voor organisaties die echt willen sturen</h2>
                    <p>Voor diepere analyse, benchmarkmogelijkheden en periodieke duiding bij complexe verandertrajecten.</p>
                    <ul class="preview-list">
                        <li>Alles uit Organisatie</li>
                        <li>Maatwerkrapportages</li>
                        <li>Benchmarking</li>
                        <li>Extra duiding</li>
                        <li>Reviewgesprekken</li>
                    </ul>
                </article>

                <article class="preview-price-card">
                    <h2>De commerciële kern</h2>
                    <p>De waarde zit niet in toegang, maar in inzicht, voortgang en besluitvorming. De centrale managementvraag is: waar lopen mensen vast in digitale verandering, en wat vraagt nu aandacht?</p>
                </article>
            </section>
        @endif

        @if ($page['slug'] === 'privacy-en-vertrouwen')
            <section class="preview-hero">
                <span class="eyebrow eyebrow--light">Vertrouwen en privacy</span>
                <h1>Betere digitale ontwikkeling begint met inzicht en vertrouwen</h1>
                <p class="preview-hero__lede">Hermes Results helpt gebruikers en organisaties met inzicht en ontwikkeling, maar gaat zorgvuldig om met persoonlijke gegevens. Vertrouwen is niet alleen juridisch noodzakelijk, maar ook essentieel voor adoptie en geloofwaardigheid.</p>
            </section>

            <section class="preview-promise-grid">
                <article class="preview-panel">
                    <h2>Wat we beloven</h2>
                    <p>Rapportages voor organisaties zijn primair gericht op patronen en inzichten op groepsniveau. Zo blijft de focus op leren, begrijpen en verbeteren.</p>
                </article>

                <article class="preview-panel">
                    <h2>Wat we helder uitleggen</h2>
                    <ul class="preview-list">
                        <li>Welke data we verzamelen en waarom</li>
                        <li>Wat individuele gebruikers zelf zien</li>
                        <li>Wat organisaties wel en niet te zien krijgen</li>
                        <li>Hoe analyses voor artikelen of benchmarks worden geanonimiseerd</li>
                    </ul>
                </article>

                <article class="preview-panel">
                    <h2>De ethische grens</h2>
                    <p>We voorkomen dat medewerkers het gevoel krijgen dat persoonlijke kwetsbaarheid of onzekerheid direct bij hun werkgever terechtkomt. Daarom werken we met geaggregeerde rapportages, heldere minimum groepsgroottes en begrijpelijke taal.</p>
                </article>

                <article class="preview-panel">
                    <h2>Vertrouwenscopy</h2>
                    <p>Daarom geven we individuele gebruikers heldere feedback over hun eigen resultaten en gebruiken we organisatie-inzichten zorgvuldig en op passend niveau. Wanneer data wordt gebruikt voor analyses, artikelen of benchmarks, gebeurt dat geanonimiseerd en met respect voor de context waarin die data is verzameld.</p>
                </article>
            </section>
        @endif

        <aside class="preview-aside">
            <h2>Meer previews</h2>
            <div class="preview-nav">
                @foreach ($pages as $linkedPage)
                    <a
                        href="{{ route('admin.strategy-pages.preview', $linkedPage['slug']) }}"
                        class="{{ $linkedPage['slug'] === $page['slug'] ? 'pill' : 'ghost-pill' }}"
                    >
                        {{ $linkedPage['title'] }}
                    </a>
                @endforeach
            </div>
            <p class="preview-note">Deze previews zijn bedoeld om richting, structuur en uitstraling te beoordelen voordat iets publiek live komt te staan.</p>
        </aside>
    </div>
</x-layouts.hermes-public>
