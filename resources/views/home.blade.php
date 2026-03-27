<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <style>
        :root {
            --bg: #f4efe6;
            --paper: rgba(255, 255, 255, 0.78);
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --forest: #20453a;
            --forest-soft: #2f5f52;
            --shadow: 0 24px 60px rgba(24, 34, 30, 0.14);
            --radius-xl: 32px;
            --radius-lg: 24px;
            --radius-md: 18px;
            --content: 1180px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Georgia", "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(217, 106, 43, 0.18), transparent 32%),
                radial-gradient(circle at 85% 20%, rgba(32, 69, 58, 0.14), transparent 28%),
                linear-gradient(180deg, #f8f2e8 0%, #f2ece2 48%, #ebe3d8 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        .topbar__inner,
        .site-footer__inner,
        .hero,
        .services,
        .approach,
        .impact,
        .closing {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80px;
            gap: 16px;
        }

        .topbar__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 60px;
            max-width: 100%;
            border-radius: 12px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 11px 18px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .pill--strong {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            border-color: transparent;
        }

        .pill--booking {
            background: linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97));
            color: #fff;
            border-color: transparent;
        }

        main {
            flex: 1;
            padding: 34px 0 60px;
        }

        .site-footer__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .site-footer {
            background: rgba(244, 239, 230, 0.78);
            border-top: 1px solid rgba(22, 33, 29, 0.08);
        }

        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 28px;
            align-items: stretch;
        }

        .hero__panel,
        .hero__side,
        .services,
        .approach,
        .impact,
        .closing {
            margin-top: 24px;
        }

        .hero__panel,
        .hero__side,
        .service-card,
        .step,
        .impact__card,
        .closing__panel {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .hero__panel {
            border-radius: var(--radius-xl);
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero__panel::after {
            content: "";
            position: absolute;
            inset: auto -80px -120px auto;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 106, 43, 0.2), transparent 70%);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1 {
            font-size: clamp(1.45rem, 3vw, 2.65rem);
            line-height: 1.08;
            margin: 22px 0 20px;
            max-width: 62ch;
        }

        .lead {
            max-width: 62ch;
            font-size: 1.08rem;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 30px;
        }

        .hero__facts {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .fact {
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.52);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }

        .fact strong,
        .impact__number {
            display: block;
            font-size: 2rem;
            margin-bottom: 4px;
        }

        .fact span,
        .impact__label {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
        }

        .hero__side {
            border-radius: var(--radius-xl);
            padding: 28px;
            background:
                linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                var(--forest);
            color: #f6f2eb;
        }

        .hero__side h2 {
            font-size: 1.6rem;
            margin-bottom: 20px;
        }

        .hero__side ul {
            padding-left: 18px;
            margin: 0 0 28px;
            line-height: 1.8;
            color: rgba(246, 242, 235, 0.82);
        }

        .hero__quote {
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(246, 242, 235, 0.86);
            line-height: 1.7;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-head h2 {
            font-size: clamp(2rem, 3vw, 3rem);
            margin-bottom: 8px;
        }

        .section-head p {
            max-width: 58ch;
            color: var(--muted);
            line-height: 1.7;
        }

        .service-grid,
        .impact__grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .service-card,
        .impact__card {
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .service-card h3 {
            font-size: 1.35rem;
            margin-bottom: 12px;
        }

        .service-card p,
        .service-card li {
            color: var(--muted);
            line-height: 1.7;
        }

        .service-card ul {
            padding-left: 18px;
            margin: 16px 0 0;
        }

        .step-list {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .step {
            border-radius: var(--radius-lg);
            padding: 22px;
        }

        .step__number {
            display: inline-flex;
            width: 38px;
            height: 38px;
            border-radius: 999px;
            align-items: center;
            justify-content: center;
            background: rgba(217, 106, 43, 0.14);
            color: var(--accent-deep);
            font-family: Arial, Helvetica, sans-serif;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .step p,
        .closing__panel p {
            color: var(--muted);
            line-height: 1.7;
        }

        .closing__panel {
            border-radius: var(--radius-xl);
            padding: 34px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: center;
        }

        .closing__panel h2 {
            font-size: clamp(2rem, 4vw, 3.4rem);
            margin-bottom: 10px;
        }

        .tagline {
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--accent-deep);
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        @media (max-width: 980px) {
            .hero,
            .closing__panel,
            .service-grid,
            .step-list,
            .impact__grid {
                grid-template-columns: 1fr;
            }

            .hero__facts {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .brand__logo {
                height: 60px;
            }

            .topbar__inner,
            .section-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero__panel,
            .hero__side,
            .service-card,
            .step,
            .impact__card,
            .closing__panel {
                padding: 22px;
            }

            h1 {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <div class="nav-actions">
            <a class="pill pill--strong" href="{{ route('login') }}">Inloggen</a>
        </div>
    </x-hermes-header>

    <main>
        <section class="hero">
            <div class="hero__panel">
                <span class="eyebrow">High Performance Platform</span>
                <h1>We verbinden mensen, leren en technologie voor tastbare groei.</h1>
                <p class="lead">
                    Geinspireerd door de opbouw van hermesresults.com: een rustige premium uitstraling, duidelijke waardepropositie en een
                    zakelijke flow van diensten naar resultaat. Deze homepage is zichtbaar voor iedereen die nog niet is ingelogd.
                </p>

                <div class="hero__actions">
                    <a class="pill pill--strong" href="{{ route('login') }}">Log direct in</a>
                </div>

                <div class="hero__facts">
                    <x-hermes-fact
                        title="AI"
                        description="Ondersteuning voor slimmer leren, onboarding en adoptie."
                    />
                    <x-hermes-fact
                        title="Data"
                        description="Inzicht via dashboards, voortgang en concrete actiepunten."
                    />
                    <x-hermes-fact
                        title="Impact"
                        description="Een platform gericht op gebruik, groei en meetbaar resultaat."
                    />
                </div>
            </div>

            <aside class="hero__side">
                <h2>Van eerste login tot blijvende adoptie</h2>
                <ul>
                    <li>Persoonlijke leerpaden en structuur voor gebruikers</li>
                    <li>Heldere dashboards voor voortgang en betrokkenheid</li>
                    <li>Praktische implementatie met focus op gebruiksgemak</li>
                    <li>Directe toegang via inloggen of account aanmaken</li>
                </ul>

                <div class="hero__quote">
                    Geen overvolle homepage, maar een landingspagina die vertrouwen opbouwt, helder uitlegt wat het platform doet en
                    bezoekers logisch naar registratie of login leidt.
                </div>
            </aside>
        </section>

        <section class="services">
            <x-hermes-section-header
                tagline="Diensten"
                heading="Wat deze omgeving ondersteunt"
                description="De secties hieronder volgen de structuur van de referentiepagina: eerst oriëntatie, dan implementatie, daarna inzicht en resultaat."
            />

            <div class="service-grid">
                <article class="service-card">
                    <h3>Assessment & Roadmap</h3>
                    <p>Een snelle analyse van kansen, prioriteiten en risico's zodat een gebruiker of organisatie gericht kan starten.</p>
                    <ul>
                        <li>Huidige situatie scherp maken</li>
                        <li>Doelen en KPI's bepalen</li>
                        <li>Heldere eerste 90 dagen</li>
                    </ul>
                </article>

                <article class="service-card">
                    <h3>Implementatie & Adoptie</h3>
                    <p>Praktische begeleiding bij de overgang van idee naar dagelijks gebruik, met aandacht voor mensen en resultaat.</p>
                    <ul>
                        <li>Communicatie en onboarding</li>
                        <li>Training en ondersteuning</li>
                        <li>Doorlopende verbetering</li>
                    </ul>
                </article>

                <article class="service-card">
                    <h3>Dashboards & Inzicht</h3>
                    <p>Duidelijke rapportage voor voortgang, activiteit en beslissingen, zonder de homepage nodeloos zwaar te maken.</p>
                    <ul>
                        <li>Heldere definities en metrics</li>
                        <li>Overzichtelijke dashboards</li>
                        <li>Actiegerichte inzichten</li>
                    </ul>
                </article>
            </div>
        </section>

        <section class="approach">
            <x-hermes-section-header
                tagline="Aanpak"
                heading="Een flow in vier stappen"
                description="Ook dit volgt de referentie-opbouw: ontdekken, bewijzen, uitrollen en structureel verbeteren."
            />

            <div class="step-list">
                <article class="step">
                    <div class="step__number">1</div>
                    <h3>Ontdek</h3>
                    <p>Bepaal waar de gebruiker of organisatie nu staat en welke uitkomst echt telt.</p>
                </article>

                <article class="step">
                    <div class="step__number">2</div>
                    <h3>Test</h3>
                    <p>Begin compact, leer snel en bewijs wat waarde oplevert voordat je opschaalt.</p>
                </article>

                <article class="step">
                    <div class="step__number">3</div>
                    <h3>Rol uit</h3>
                    <p>Maak het groter met duidelijke begeleiding, een logische structuur en adoptie als prioriteit.</p>
                </article>

                <article class="step">
                    <div class="step__number">4</div>
                    <h3>Verbeter</h3>
                    <p>Gebruik data en feedback om blijvend te verfijnen in plaats van een eenmalige lancering te doen.</p>
                </article>
            </div>
        </section>

        <section class="impact">
            <x-hermes-section-header
                tagline="Impact"
                heading="Ontworpen om resultaat zichtbaar te maken"
                description="De getallen hieronder zijn positioneringsblokken om dezelfde zakelijke ritmiek als de referentiepagina neer te zetten."
            />

            <div class="impact__grid">
                <article class="impact__card">
                    <span class="impact__number">-30%</span>
                    <span class="impact__label">sneller naar eerste waarde door een duidelijker startmoment</span>
                </article>

                <article class="impact__card">
                    <span class="impact__number">+45%</span>
                    <span class="impact__label">meer adoptie wanneer onboarding en gebruik samen ontworpen zijn</span>
                </article>

                <article class="impact__card">
                    <span class="impact__number">+25%</span>
                    <span class="impact__label">meer toepasbaarheid door beter inzicht in voortgang en behoeften</span>
                </article>
            </div>
        </section>

        <section class="closing">
            <div class="closing__panel">
                <div>
                    <div class="tagline">Aan de slag</div>
                    <h2>Maak een account aan of log direct in.</h2>
                    <p>
                        Zodra een gebruiker is ingelogd, verlaat hij deze gasten-homepage en komt hij op de aparte pagina met het welkomstbericht en het ingelogde e-mailadres.
                    </p>
                </div>

                <div class="nav-actions">
                    <a class="pill pill--strong" href="{{ route('login') }}">Inloggen</a>
                </div>
            </div>
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
