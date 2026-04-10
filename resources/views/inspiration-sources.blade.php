<x-layouts.hermes-public
    title="Inspiratiebronnen | Hermes Results"
    meta-description="Lees welke wetenschappers, filosofen en praktijkdenkers het model van Hermes Results hebben geïnspireerd en waarom hun inzichten zo belangrijk zijn voor digitale weerbaarheid."
    :canonical-url="route('inspiration-sources.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="auth()->check()"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Inspiratiebronnen | Hermes Results',
        'description' => 'Lees welke wetenschappers, filosofen en praktijkdenkers het model van Hermes Results hebben geïnspireerd en waarom hun inzichten zo belangrijk zijn voor digitale weerbaarheid.',
        'url' => route('inspiration-sources.show'),
    ]"
>
    @guest
        <x-slot:headerMenu>
            <a class="home-menu-item" href="{{ route('home') }}">Home</a>
            <a class="home-menu-item" href="{{ route('blog.index') }}">Blog</a>
            <div class="home-menu-dropdown">
                <a class="home-menu-trigger" href="{{ route('about.show') }}">
                    Over
                    <span aria-hidden="true">▾</span>
                </a>
                <div class="home-submenu">
                    <a href="{{ route('inspiration-sources.show') }}">Inspiratiebronnen</a>
                    <a href="{{ route('about.show') }}">Over ons</a>
                    <a href="{{ route('pricing.show') }}">Prijzen</a>
                    <a href="{{ route('privacy.show') }}">{{ __('hermes.footer.privacy') }}</a>
                </div>
            </div>
            <a class="home-menu-item" href="{{ route('organizations.landing') }}">Organisaties</a>
        </x-slot:headerMenu>
    @endguest

    <x-slot:head>
        <style>
            .inspiration-page,
            .inspiration-hero,
            .inspiration-hero__summary,
            .inspiration-section,
            .inspiration-grid,
            .inspiration-card,
            .inspiration-cta {
                display: grid;
                gap: 24px;
            }

            .inspiration-page {
                gap: 32px;
            }

            .inspiration-hero,
            .inspiration-card,
            .inspiration-cta {
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .inspiration-hero {
                grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
                padding: 32px;
                border-radius: 30px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.24), transparent 34%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 243, 234, 0.82));
            }

            .inspiration-hero__summary,
            .inspiration-cta {
                padding: 24px;
                border-radius: 24px;
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .inspiration-hero__summary strong,
            .inspiration-hero__summary span {
                display: block;
            }

            .inspiration-hero__summary strong {
                color: rgba(248, 241, 231, 0.78);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.78rem;
            }

            .inspiration-hero__summary span {
                font-size: 1.2rem;
                line-height: 1.6;
                color: #fff7ee;
            }

            .inspiration-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .inspiration-card {
                padding: 28px;
                border-radius: 26px;
                background: rgba(255, 255, 255, 0.74);
            }

            .inspiration-card h2,
            .inspiration-card h3,
            .inspiration-cta h2 {
                margin: 0;
            }

            .inspiration-card p,
            .inspiration-cta p {
                margin: 0;
                color: var(--muted);
                line-height: 1.8;
                font-family: Arial, Helvetica, sans-serif;
            }

            .inspiration-card p + p,
            .inspiration-cta p + p {
                margin-top: 12px;
            }

            .inspiration-card h3 + p {
                margin-top: 12px;
            }

            .inspiration-card__book {
                color: var(--forest);
                font-weight: 700;
            }

            .inspiration-cta h2,
            .inspiration-cta p {
                color: #fff7ee;
            }

            .inspiration-cta .pill {
                width: fit-content;
            }

            @media (max-width: 980px) {
                .inspiration-hero,
                .inspiration-grid {
                    grid-template-columns: 1fr;
                }

                .inspiration-hero {
                    padding: 24px;
                }
            }
        </style>
    </x-slot:head>

    <div class="inspiration-page">
        <section class="inspiration-hero">
            <div>
                <x-user-page-heading
                    eyebrow="Over Hermes Results"
                    title="Op wiens schouders wij staan"
                >
                    <x-slot:meta>
                        <div class="user-page-heading__meta">
                            <p>Hermes Results is geen zelfverzonnen methode. Het is een eigen model, gebouwd op decennia van praktijkervaring en op het werk van wetenschappers en denkers die ons diep hebben geïnspireerd.</p>
                            <p>We geloven niet in één-guru-oplossingen. Digitale weerbaarheid is te complex voor één boek of één methode. Daarom putten we uit een brede, zorgvuldig gekozen selectie van wetenschappelijk onderbouwde inzichten. Hieronder vind je wie ons inspireren en waarom.</p>
                        </div>
                    </x-slot:meta>
                </x-user-page-heading>
            </div>

            <aside class="inspiration-hero__summary">
                <strong>Brede basis</strong>
                <span>Een eigen model, gevoed door positieve psychologie, leren, leiderschap en filosofie.</span>
            </aside>
        </section>

        <section class="inspiration-section">
            <x-user-section-heading
                eyebrow="Positieve psychologie"
                title="Wetenschappelijke bouwstenen voor groei en weerbaarheid"
                text="Deze denkers helpen ons begrijpen hoe mensen leren, volhouden, herstellen en nieuwe digitale uitdagingen met meer vertrouwen aangaan."
            />

            <div class="inspiration-grid">
                <article class="inspiration-card">
                    <h3>Martin Seligman</h3>
                    <p>De grondlegger van de positieve psychologie. Zijn PERMA-model, Positive emotions, Engagement, Relationships, Meaning, Achievement, vormt het fundament van laag 1 in ons model.</p>
                    <p>Seligman liet zien dat welbevinden geen geluk is dat je overkomt, maar iets dat je actief kunt bouwen.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Flourish</p>
                </article>

                <article class="inspiration-card">
                    <h3>Carol Dweck</h3>
                    <p>Haar onderzoek naar mindset veranderde de manier waarop we naar leren en ontwikkeling kijken.</p>
                    <p>Het onderscheid tussen een vaste en een groeiende mindset is voor onze doelgroep misschien wel de meest bevrijdende ontdekking: je bent niet te oud om te leren. Je gelooft alleen dat je dat bent.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Mindset</p>
                </article>

                <article class="inspiration-card">
                    <h3>Angela Duckworth</h3>
                    <p>Talent is minder bepalend dan we denken. Grit, de combinatie van passie en volharding voor langetermijndoelen, is wat het verschil maakt.</p>
                    <p>Goed nieuws: grit is trainbaar. En wie 30 jaar heeft doorgezet in zijn werk, heeft al bewezen dat hij het heeft.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Grit</p>
                </article>

                <article class="inspiration-card">
                    <h3>Karen Reivich</h3>
                    <p>Weerbaarheid is geen karaktertrek die je hebt of niet hebt. Het is een set vaardigheden die je kunt leren.</p>
                    <p>Reivich beschrijft zeven concrete, meetbare factoren, van emotieregulatie tot zelfeffectiviteit. Haar werk vormt de ruggengraat van laag 3 in ons model.</p>
                    <p class="inspiration-card__book">Sleutelwerk: The Resilience Factor</p>
                </article>

                <article class="inspiration-card">
                    <h3>Tom Rath</h3>
                    <p>Rath bouwt voort op Seligman met een praktische boodschap: stop met je zwaktes wegwerken en investeer in je sterke kanten.</p>
                    <p>Mensen die dagelijks doen waar ze goed in zijn, zijn productiever, gelukkiger en weerbaarder. Dat geldt ook in een digitale wereld.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Strengths Finder 2.0</p>
                </article>

                <article class="inspiration-card">
                    <h3>Mihaly Csikszentmihalyi</h3>
                    <p>Flow, de staat van volledige opgaan in wat je doet, treedt op als de uitdaging net boven je vaardigheidsniveau ligt. Te makkelijk geeft verveling, te moeilijk geeft angst.</p>
                    <p>Dit principe stuurt de opbouw van onze e-learnings: stap voor stap, net buiten je comfortzone, nooit overweldigend.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Flow</p>
                </article>

                <article class="inspiration-card">
                    <h3>Barbara Fredrickson</h3>
                    <p>Haar Broaden-and-Build Theory verklaart wetenschappelijk waarom positieve emoties zo belangrijk zijn: ze verbreden je denkvermogen en bouwen langetermijnmiddelen op.</p>
                    <p>Negatieve emoties, zoals digitale stress, vernauwen je focus en blokkeren het leren. Dit is waarom we bij Hermes Results altijd beginnen bij het fundament, niet bij de problemen.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Positivity</p>
                </article>

                <article class="inspiration-card">
                    <h3>Albert Bandura</h3>
                    <p>Bandura's onderzoek naar self-efficacy, de overtuiging dat je een specifieke taak kunt uitvoeren, is een van de meest gevalideerde theorieën in de psychologie.</p>
                    <p>Kleine successen versterken self-efficacy. Daarom bouwen we in ons model bewust op van eenvoudig naar complex: elke kleine digitale overwinning telt.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Self-Efficacy: The Exercise of Control</p>
                </article>

                <article class="inspiration-card">
                    <h3>C.R. Snyder</h3>
                    <p>Hoop is geen wens. Hoop is een actieve cognitieve kracht met drie componenten: een doel hebben, wegen zien om er te komen, en de motivatie om die wegen ook daadwerkelijk te bewandelen.</p>
                    <p>Snyders Hope Theory geeft ons een concreet handvat voor mensen die vastgelopen zijn: begin bij het doel, zoek dan de weg.</p>
                    <p class="inspiration-card__book">Sleutelwerk: The Psychology of Hope</p>
                </article>

                <article class="inspiration-card">
                    <h3>Barry O'Reilly</h3>
                    <p>Soms is het probleem niet dat je te weinig weet. Het probleem is wat je al weet. O'Reilly laat zien dat groei begint met afleren: bewust loslaten van gewoonten en overtuigingen die vroeger werkten maar nu in de weg staan.</p>
                    <p>Voor mensen met decennia werkervaring is dit de meest onderschatte en meest noodzakelijke stap.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Unlearn</p>
                </article>
            </div>
        </section>

        <section class="inspiration-section">
            <x-user-section-heading
                eyebrow="Leren en leiderschap"
                title="Hoe mensen en organisaties in beweging komen"
                text="Naast psychologie kijken we ook naar hoe leren, eigenaarschap en leiderschap duurzame verandering mogelijk maken."
            />

            <div class="inspiration-grid">
                <article class="inspiration-card">
                    <h3>Nick van Dam</h3>
                    <p>Leren is een actief, zelfsturend proces. Van Dam verbindt leiderschapsontwikkeling met modern leeronderzoek en laat zien hoe organisaties een cultuur van continu leren kunnen bouwen.</p>
                    <p>Zijn werk inspireert zowel ons organisatieaanbod als de manier waarop we zelfleiderschap benaderen.</p>
                </article>
            </div>
        </section>

        <section class="inspiration-section">
            <x-user-section-heading
                eyebrow="Filosofie"
                title="Denkkaders voor rust, vrijheid en richting"
                text="Filosofie helpt ons om digitale druk niet alleen praktisch, maar ook existentieel te begrijpen: waar heb je invloed op, waar kies je je reactie, en wat moet je loslaten om te kunnen groeien?"
            />

            <div class="inspiration-grid">
                <article class="inspiration-card">
                    <h3>De stoïcijnen, Marcus Aurelius, Epictetus, Seneca</h3>
                    <p>Hermes Results is mede geïnspireerd door de stoïsche filosofie. De kern: maak onderscheid tussen wat je kunt beïnvloeden en wat niet. Richt al je energie op het eerste, accepteer het tweede zonder te klagen, maar ook zonder je erbij neer te leggen.</p>
                    <p>Dat klinkt misschien abstract. Maar voor iemand die overspoeld wordt door digitale veranderingen is het een concrete en bevrijdende gedachte: je kunt niet kiezen dat AI komt. Je kunt wel kiezen hoe je erop reageert.</p>
                    <p>Ons motto vat het samen: Accepteer alles, maar leg je nergens bij neer.</p>
                </article>

                <article class="inspiration-card">
                    <h3>Viktor Frankl</h3>
                    <p>Frankl overleefde de concentratiekampen en ontdekte daarin een onverwoestbaar principe: tussen stimulus en respons zit altijd een ruimte. In die ruimte ligt je vrijheid en je verantwoordelijkheid.</p>
                    <p>Hoe zwaar de digitale druk ook is, jij bepaalt hoe je erop reageert. Dat is geen makkelijke boodschap, maar wel een ware.</p>
                    <p class="inspiration-card__book">Sleutelwerk: Man's Search for Meaning</p>
                </article>
            </div>
        </section>

        <section class="inspiration-cta">
            <h2>Dit zijn onze schouders. Jij bouwt erop verder.</h2>
            <p>Al deze denkers hebben ons geïnspireerd, maar Hermes Results is een eigen model met een eigen aanpak. We nemen wat werkt, combineren het op een manier die praktisch toepasbaar is voor jou, en testen het voortdurend in de praktijk.</p>
            <p>Want uiteindelijk gaat het niet om de theorie. Het gaat om jou, en om wat jij morgen anders doet.</p>
            <a href="{{ route('register') }}" class="pill">Ontdek waar jij staat. Doe de gratis weerbaarheidsscan.</a>
        </section>
    </div>
</x-layouts.hermes-public>
