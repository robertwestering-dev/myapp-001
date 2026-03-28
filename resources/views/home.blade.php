<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hermes Results</title>
    <x-favicon-links />
    <style>
        :root {
            --bg: #f4ede3;
            --bg-deep: #efe5d7;
            --panel: rgba(255, 250, 244, 0.82);
            --panel-strong: rgba(255, 255, 255, 0.92);
            --ink: #172321;
            --muted: #56655f;
            --line: rgba(23, 35, 33, 0.1);
            --forest: #1e473d;
            --forest-deep: #102a23;
            --clay: #bc5b2c;
            --clay-deep: #8d3f18;
            --gold: #d6b37a;
            --shadow: 0 28px 70px rgba(16, 33, 28, 0.14);
            --radius-xl: 34px;
            --radius-lg: 24px;
            --radius-md: 18px;
            --content: 1180px;
            --block-heading-max: 2.45rem;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--ink);
            font-family: "Avenir Next", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(188, 91, 44, 0.2), transparent 34%),
                radial-gradient(circle at 88% 12%, rgba(30, 71, 61, 0.15), transparent 30%),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-deep) 52%, #ebdfcf 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(244, 237, 227, 0.78);
            border-bottom: 1px solid rgba(23, 35, 33, 0.08);
        }

        .topbar__inner,
        .site-footer__inner,
        .hero,
        .problem,
        .offers,
        .plan,
        .bridge,
        .closing {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar__left,
        .topbar__actions,
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .topbar__left {
            min-width: 0;
            flex: 1;
        }

        .topbar__menu {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-left: 16px;
            white-space: nowrap;
        }

        .topbar__menu a {
            font-size: 0.98rem;
            font-weight: 600;
            color: var(--ink);
        }

        .topbar__menu a:hover {
            color: var(--clay-deep);
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 54px;
            max-width: 100%;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.72);
            color: var(--ink);
            font-size: 0.94rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .pill--strong {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--clay) 0%, var(--clay-deep) 100%);
            box-shadow: 0 12px 28px rgba(141, 63, 24, 0.28);
        }

        .pill--ghost {
            background: transparent;
        }

        .pill--booking {
            border-color: transparent;
            color: #f8f3eb;
            background: linear-gradient(180deg, rgba(30, 71, 61, 0.96), rgba(16, 42, 35, 0.98));
        }

        main {
            flex: 1;
            padding: 34px 0 64px;
        }

        .site-footer {
            background: rgba(244, 237, 227, 0.78);
            border-top: 1px solid rgba(23, 35, 33, 0.08);
        }

        .site-footer__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 24px;
            align-items: stretch;
        }

        .hero__panel,
        .hero__sidebar,
        .problem__grid article,
        .offer-card,
        .plan__step,
        .bridge__panel,
        .closing__panel {
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
        }

        .hero__panel,
        .hero__sidebar,
        .bridge__panel,
        .closing__panel {
            overflow: hidden;
            position: relative;
        }

        .hero__panel {
            padding: 42px;
            border-radius: var(--radius-xl);
            background:
                radial-gradient(circle at 82% 24%, rgba(214, 179, 122, 0.28), transparent 24%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(250, 243, 234, 0.82));
        }

        .hero__panel::after {
            content: "";
            position: absolute;
            right: -50px;
            bottom: -100px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(188, 91, 44, 0.18), transparent 72%);
            pointer-events: none;
        }

        .hero__sidebar,
        .bridge__panel {
            padding: 30px;
            border-radius: var(--radius-xl);
            background:
                linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                var(--forest);
            color: #f8f1e7;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(30, 71, 61, 0.08);
            color: var(--forest);
            text-transform: uppercase;
            letter-spacing: 0.11em;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .eyebrow--light {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(248, 241, 231, 0.86);
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1,
        h2,
        h3 {
            font-family: "Georgia", "Times New Roman", serif;
            letter-spacing: -0.02em;
        }

        h1 {
            margin: 20px 0 18px;
            font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
            line-height: 0.96;
            max-width: none;
        }

        .hero__intro {
            max-width: 58ch;
            font-size: 1.08rem;
            line-height: 1.8;
            color: var(--muted);
            margin-bottom: 26px;
        }

        .hero__actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .hero__proof {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .hero__proof article {
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.62);
            border: 1px solid rgba(23, 35, 33, 0.08);
        }

        .hero__proof strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1.02rem;
        }

        .hero__proof span,
        .offer-card p,
        .offer-card li,
        .plan__step p,
        .closing__panel p,
        .problem__grid p {
            color: var(--muted);
            line-height: 1.7;
        }

        .hero__sidebar h2,
        .bridge__panel h2 {
            font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
            line-height: 1.05;
            margin: 14px 0 18px;
            color: #fff7ee;
        }

        .hero__sidebar p,
        .bridge__panel p,
        .hero__sidebar li {
            color: rgba(248, 241, 231, 0.84);
            line-height: 1.8;
        }

        .hero__sidebar ul,
        .offer-card ul {
            margin: 0;
            padding-left: 18px;
        }

        .hero__sidebar .sidebar-box,
        .bridge__highlight {
            margin-top: 22px;
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        section {
            margin-top: 48px;
        }

        section + section {
            padding-top: 20px;
        }

        .section-head {
            display: block;
            width: 100%;
            margin-bottom: 18px;
        }

        .section-head h2 {
            margin-bottom: 0;
            font-size: clamp(1.5rem, 2.4vw, var(--block-heading-max));
        }

        .tagline {
            margin-bottom: 10px;
            color: var(--clay-deep);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .problem__grid,
        .offer-grid,
        .plan__grid {
            display: grid;
            gap: 18px;
        }

        .problem__grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .problem__grid article,
        .offer-card,
        .plan__step,
        .closing__panel,
        .contact-form {
            border-radius: var(--radius-lg);
            background: var(--panel);
        }

        .problem__grid article {
            padding: 24px;
        }

        .problem__grid h3,
        .offer-card h3,
        .plan__step h3 {
            margin-bottom: 10px;
            font-size: 1.35rem;
        }

        .offer-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .offer-card {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .offer-card--featured {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 236, 222, 0.94));
            transform: translateY(-4px);
        }

        .offer-card__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.88rem;
            color: var(--muted);
        }

        .offer-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(188, 91, 44, 0.12);
            color: var(--clay-deep);
            font-weight: 700;
        }

        .offer-card__footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .offer-note {
            font-size: 0.92rem;
            color: var(--muted);
        }

        .plan__grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .plan__step {
            padding: 24px;
        }

        .plan__number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            margin-bottom: 16px;
            background: rgba(188, 91, 44, 0.13);
            color: var(--clay-deep);
            font-weight: 800;
        }

        .bridge__panel {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
            gap: 24px;
            align-items: center;
        }

        .bridge__list {
            display: grid;
            gap: 12px;
        }

        .bridge__list article {
            padding: 16px 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .bridge__list strong {
            display: block;
            margin-bottom: 6px;
            color: #fff7ee;
        }

        .closing__panel {
            padding: 34px;
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(320px, 1.1fr);
            gap: 24px;
            align-items: start;
        }

        .closing__panel h2 {
            margin-bottom: 10px;
            font-size: clamp(1.55rem, 2.6vw, var(--block-heading-max));
        }

        .contact-form {
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
        }

        .form-status {
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            background: rgba(30, 71, 61, 0.1);
            color: var(--forest-deep);
            font-weight: 600;
        }

        .contact-form form {
            display: grid;
            gap: 16px;
        }

        .contact-form label,
        .contact-form .checkbox-field {
            display: grid;
            gap: 8px;
        }

        .contact-form label span,
        .checkbox-field__text {
            font-size: 0.96rem;
            font-weight: 600;
            color: var(--ink);
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            border: 1px solid rgba(23, 35, 33, 0.14);
            border-radius: 16px;
            padding: 13px 15px;
            background: rgba(255, 255, 255, 0.82);
            color: var(--ink);
            font: inherit;
        }

        .contact-form textarea {
            min-height: 180px;
            resize: vertical;
        }

        .checkbox-field {
            align-items: start;
        }

        .checkbox-field label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .checkbox-field input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            padding: 0;
        }

        .field-error {
            color: #9f2f1a;
            font-size: 0.9rem;
        }

        @media (max-width: 1040px) {
            .hero,
            .bridge__panel,
            .offer-grid,
            .problem__grid,
            .plan__grid,
            .closing__panel {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 780px) {
            .topbar__inner,
            .section-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar__inner {
                height: auto;
                min-height: 80px;
                padding: 12px 0;
            }

            .topbar__left {
                width: 100%;
                flex-wrap: wrap;
                gap: 10px 14px;
            }

            .topbar__menu {
                margin-left: 0;
            }

            .topbar__actions {
                width: 100%;
                justify-content: flex-end;
            }

            .hero__panel,
            .hero__sidebar,
            .problem__grid article,
            .offer-card,
            .plan__step,
            .bridge__panel,
            .closing__panel {
                padding: 22px;
            }

            .hero__proof {
                grid-template-columns: 1fr;
            }

            h1 {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <x-slot:menu>
            <a href="#diensten">Diensten</a>
            <a href="#contact">Contact</a>
        </x-slot:menu>

        <div class="nav-actions">
            <a class="pill pill--strong" href="{{ route('login') }}">Inloggen</a>
        </div>
    </x-hermes-header>

    <main>
        <section class="hero">
            <div class="hero__panel">
                <span class="eyebrow">Hermes Results</span>
                <h1>Maak digitale transformatie begrijpelijk, meetbaar en menselijk.</h1>
                <p class="hero__intro">
                    U hoeft niet te raden waarom verandering stokt. Hermes Results helpt u eerst scherp te zien waar adaptability en digitale weerbaarheid
                    van medewerkers nu staan, en gebruikt die inzichten vervolgens als startpunt voor doelgerichte consultancy in verandermanagement.
                </p>

                <div class="hero__actions">
                    <a class="pill pill--strong" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">
                        Plan een kennismaking
                    </a>
                    <a class="pill" href="#diensten">Bekijk de quick scans</a>
                </div>

                <div class="hero__proof">
                    <article>
                        <strong>Quick scans die richting geven</strong>
                        <span>Korte, concrete assessments die zichtbaar maken waar gedrag, mindset en context verandering helpen of blokkeren.</span>
                    </article>
                    <article>
                        <strong>Consultancy met echte input</strong>
                        <span>Geen veranderplan op gevoel, maar begeleiding op basis van data uit uw teams en organisatie.</span>
                    </article>
                    <article>
                        <strong>Van inzicht naar actie</strong>
                        <span>Heldere rapportage, prioriteiten en een werkbare veranderroute voor digitale transformatie.</span>
                    </article>
                </div>
            </div>

            <aside class="hero__sidebar">
                <span class="eyebrow eyebrow--light">Focus op mensen</span>
                <h2>Uw team wil vooruit, maar onzichtbare frictie vertraagt verandering.</h2>
                <p>
                    Nieuwe systemen, veranderende processen en hogere digitale verwachtingen vragen niet alleen om technologie, maar om medewerkers die mee kunnen bewegen.
                    Wanneer adaptability laag is of digitale weerbaarheid onder druk staat, blijven adoptie, eigenaarschap en voortgang achter.
                </p>

                <ul>
                    <li>U ziet weerstand, maar mist nog scherpte over de werkelijke oorzaak.</li>
                    <li>U wilt investeren in transformatie, maar eerst weten waar de grootste risico's zitten.</li>
                    <li>U zoekt een gids die zowel meet als meebeweegt in de uitvoering.</li>
                </ul>

                <div class="sidebar-box">
                    De eerste quick scan in dit platform is de <strong>Adaptability Scan volgens het A.C.E.-model</strong>, opgebouwd rond Ability, Character en Environment.
                </div>
            </aside>
        </section>

        <section class="problem">
            <x-hermes-section-header
                tagline="Waarom dit telt"
                heading="Verandering mislukt zelden op strategie alleen"
            />

            <div class="problem__grid">
                <article>
                    <h3>Extern probleem</h3>
                    <p>Digitale transformatie versnelt, terwijl teams tegelijk moeten leren, presteren en omgaan met steeds nieuwe tools, processen en verwachtingen.</p>
                </article>

                <article>
                    <h3>Intern probleem</h3>
                    <p>Veel organisaties voelen dat medewerkers meer ondersteuning nodig hebben, maar weten nog niet precies waar adaptability of digitale weerbaarheid tekortschiet.</p>
                </article>

                <article>
                    <h3>Filosofisch probleem</h3>
                    <p>Verandering hoort mensen niet te overvallen. Teams verdienen een aanpak die eerst begrijpt wat er speelt, voordat er nieuwe druk wordt toegevoegd.</p>
                </article>
            </div>
        </section>

        <section class="offers" id="diensten">
            <x-hermes-section-header
                tagline="Diensten"
                heading="Drie proposities die elkaar logisch versterken"
            />

            <div class="offer-grid">
                <article class="offer-card offer-card--featured">
                    <div class="offer-card__meta">
                        <span class="offer-badge">Nu beschikbaar</span>
                        <span>Inloggen met account</span>
                    </div>

                    <div>
                        <h3>Quick scan adaptability</h3>
                        <p>
                            Een compacte assessment die zichtbaar maakt hoe mensen omgaan met verandering, onzekerheid, leren en context.
                        </p>
                    </div>

                    <ul>
                        <li>Analyse van Ability, Character en Environment</li>
                        <li>Praktische input voor teamgesprekken en prioriteiten</li>
                        <li>Geschikt als startmeting voor trainingen en veranderingstrajecten</li>
                    </ul>

                    <div class="offer-card__footer">
                        <span class="offer-note">Voor organisaties die eerst scherpte willen voordat ze gaan investeren.</span>
                        <a class="pill pill--strong" href="\login" target="_blank" rel="noopener noreferrer">Doe de quick scan</a>
                    </div>
                </article>

                <article class="offer-card">
                    <div class="offer-card__meta">
                        <span class="offer-badge">Nu beschikbaar</span>
                        <span>Inloggen met account</span>
                    </div>

                    <div>
                        <h3>Quick scan digitale weerbaarheid</h3>
                        <p>
                            Deze scan helpt organisaties begrijpen in hoeverre medewerkers digitaal zelfredzaam, alert en stabiel blijven in een omgeving vol nieuwe tools,
                            digitale druk en veranderende omstandigheden.
                        </p>
                    </div>

                    <ul>
                        <li>Gericht op gedrag, vertrouwen en praktische digitale veerkracht</li>
                        <li>Sluit aan op de komende questionnaire voor digitale weerbaarheid</li>
                        <li>Ontworpen als tweede diagnose naast adaptability</li>
                    </ul>

                    <div class="offer-card__footer">
                        <span class="offer-note">Ideaal wanneer digitale verandering ook cognitieve en operationele druk verhoogt.</span>
                        <a class="pill pill--strong" href="\login" target="_blank" rel="noopener noreferrer">Doe de quick scan</a>
                    </div>
                </article>

                <article class="offer-card">
                    <div class="offer-card__meta">
                        <span class="offer-badge">Consultancy</span>
                        <span>Van diagnose naar uitvoering</span>
                    </div>

                    <div>
                        <h3>Verandermanagement bij digitale transformatie</h3>
                        <p>
                            Consultancy die quick-scan-data vertaalt naar een realistisch veranderverhaal, concrete interventies en begeleiding van teams, leidinggevenden
                            en besluitvorming tijdens digitale transformatie.
                        </p>
                    </div>

                    <ul>
                        <li>Veranderanalyse op basis van echte teaminput</li>
                        <li>Heldere prioriteiten voor communicatie, adoptie en leiderschap</li>
                        <li>Begeleiding bij implementatie en bijsturing</li>
                    </ul>

                    <div class="offer-card__footer">
                        <span class="offer-note">Voor organisaties die niet alleen willen meten, maar ook duurzaam willen veranderen.</span>
                        <a class="pill" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">Start een gesprek</a>
                    </div>
                </article>
            </div>
        </section>

        <section class="plan">
            <x-hermes-section-header
                tagline="Eenvoudig plan"
                heading="Zo werken we van eerste scan naar veranderresultaat"
            />

            <div class="plan__grid">
                <article class="plan__step">
                    <div class="plan__number">1</div>
                    <h3>Meet wat er echt speelt</h3>
                    <p>We starten met een quick scan die zichtbaar maakt waar medewerkers sterk staan en waar verandering extra ondersteuning vraagt.</p>
                </article>

                <article class="plan__step">
                    <div class="plan__number">2</div>
                    <h3>Vertaal data naar keuzes</h3>
                    <p>De uitkomsten worden omgezet naar een duidelijke lezing van risico's, kansen en prioriteiten voor teams, leiderschap en uitvoering.</p>
                </article>

                <article class="plan__step">
                    <div class="plan__number">3</div>
                    <h3>Begeleid de transformatie</h3>
                    <p>Met consultancy ondersteunen we communicatie, adoptie en veranderaanpak zodat digitale transformatie ook in gedrag landt.</p>
                </article>
            </div>
        </section>

        <section class="bridge">
            <div class="bridge__panel">
                <div>
                    <span class="eyebrow eyebrow--light">Van questionnaire naar consultancy</span>
                    <h2>De scans zijn geen eindproduct, maar het startpunt van beter verandermanagement.</h2>
                    <p>
                        De questionnaires in dit platform leveren gestructureerde input op voor gesprekken, besluitvorming en interventies. Daardoor wordt consultancy minder abstract
                        en veel beter afgestemd op wat medewerkers werkelijk ervaren in verandering.
                    </p>

                    <div class="bridge__highlight">
                        Eerst inzicht in adaptability. Daarna ook zicht op digitale weerbaarheid. Vervolgens een verandertraject dat niet om mensen heen werkt, maar met hen meebeweegt.
                    </div>
                </div>

                <div class="bridge__list">
                    <article>
                        <strong>Heldere startpositie</strong>
                        <p>U ziet sneller waar weerstand, onzekerheid of overbelasting in de verandering ontstaat.</p>
                    </article>

                    <article>
                        <strong>Betere interventies</strong>
                        <p>Training, communicatie en leiderschap kunnen beter worden gericht op de echte knelpunten.</p>
                    </article>

                    <article>
                        <strong>Sterker veranderverhaal</strong>
                        <p>Uw transformatie krijgt een boodschap die medewerkers begrijpen, voelen en kunnen volgen.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="closing" id="contact">
            <div class="closing__panel">
                <div>
                    <div class="tagline">Call to action</div>
                    <h2>Wilt u eerst begrijpen waar verandering vastloopt?</h2>
                    <p>
                        Plan een kennismaking voor de Quick scan adaptability van medewerkers, bespreek de roadmap voor de Quick scan digitale weerbaarheid
                        of stuur direct een bericht. We reageren op basis van uw vraag en kijken welke quick scan of consultancyvorm het beste past.
                    </p>

                    <div class="nav-actions">
                        <a class="pill pill--strong" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">
                            Plan een kennismaking
                        </a>
                    </div>
                </div>

                <div class="contact-form">
                    @if (session('status'))
                        <div class="form-status">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <label for="contact-name">
                            <span>Naam</span>
                            <input
                                id="contact-name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                autocomplete="name"
                                required
                            >
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <label for="contact-email">
                            <span>Emailadres</span>
                            <input
                                id="contact-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                            >
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <label for="contact-message">
                            <span>Bericht</span>
                            <textarea
                                id="contact-message"
                                name="message"
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <div class="checkbox-field">
                            <label for="contact-consent">
                                <input
                                    id="contact-consent"
                                    type="checkbox"
                                    name="privacy_consent"
                                    value="1"
                                    @checked(old('privacy_consent'))
                                    required
                                >
                                <span class="checkbox-field__text">Ik ga akkoord met verwerking van mijn gegevens om contact op te nemen.</span>
                            </label>
                            @error('privacy_consent')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="nav-actions">
                            <button type="submit" class="pill pill--strong">Verstuur bericht</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
