<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin-portal</title>
    <style>
        :root {
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --forest: #20453a;
            --forest-soft: #2f5f52;
            --paper: rgba(255, 255, 255, 0.82);
            --shadow: 0 24px 60px rgba(24, 34, 30, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
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
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 64px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand__mark {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            position: relative;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-soft) 100%);
            box-shadow: var(--shadow);
            flex-shrink: 0;
        }

        .brand__mark::before,
        .brand__mark::after {
            content: "";
            position: absolute;
            inset: 50% auto auto 50%;
            transform: translate(-50%, -50%);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
        }

        .brand__mark::before {
            width: 24px;
            height: 24px;
        }

        .brand__mark::after {
            width: 8px;
            height: 8px;
            background: var(--accent);
            box-shadow: 0 -12px 0 -2px rgba(255, 255, 255, 0.78), 12px 0 0 -2px rgba(255, 255, 255, 0.78);
        }

        .brand__copy {
            display: flex;
            flex-direction: column;
            gap: 2px;
            line-height: 1.05;
        }

        .brand__title {
            font-size: 1.08rem;
            letter-spacing: 0.02em;
        }

        .brand__subtitle {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.62rem;
            letter-spacing: 0.06em;
            color: var(--muted);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .portal {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 24px;
        }

        .sidebar,
        .panel,
        .stat {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .sidebar {
            border-radius: 32px;
            padding: 28px;
        }

        .sidebar__eyebrow,
        .panel__eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sidebar h2,
        .panel h1,
        .panel h3,
        .panel p {
            margin-top: 0;
        }

        .sidebar h2 {
            margin: 18px 0 20px;
            font-size: 1.55rem;
        }

        .menu {
            display: grid;
            gap: 12px;
        }

        .menu__item {
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.56);
            font-family: Arial, Helvetica, sans-serif;
        }

        .menu__item--active {
            color: #fff;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-soft) 100%);
            border-color: transparent;
        }

        .panel {
            border-radius: 32px;
            padding: 34px;
        }

        .panel h1 {
            margin: 18px 0 12px;
            font-size: clamp(1.5rem, 2.8vw, 2.6rem);
            line-height: 1.08;
        }

        .lead {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 60ch;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin: 28px 0;
        }

        .stat {
            border-radius: 22px;
            padding: 22px;
        }

        .stat__label {
            display: block;
            margin-bottom: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .stat strong {
            font-size: 1.25rem;
        }

        .notice {
            padding: 20px 22px;
            border-radius: 22px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }

        .notice p + p {
            margin-top: 10px;
        }

        @media (max-width: 900px) {
            .portal {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <span class="brand__mark" aria-hidden="true"></span>
                <span class="brand__copy">
                    <span class="brand__title">Hermes Results</span>
                    <span class="brand__subtitle">Oplossingen die werken</span>
                </span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="pill">Logout</button>
            </form>
        </header>

        <main class="portal">
            <aside class="sidebar">
                <span class="sidebar__eyebrow">Beheer</span>
                <h2>Admin-menu</h2>

                <nav class="menu" aria-label="Admin menu">
                    <a href="{{ route('admin.portal') }}" class="menu__item menu__item--active">Admin-portal</a>
                    <a href="{{ route('admin.users.index') }}" class="menu__item">Gebruikers</a>
                    <div class="menu__item">Instellingen</div>
                </nav>
            </aside>

            <section class="panel">
                <span class="panel__eyebrow">Admin-portal</span>
                <h1>Welkom terug, beheerder.</h1>
                <p class="lead">
                    U bent ingelogd als admin met het account {{ auth()->user()->email }}. Deze omgeving is uitsluitend
                    beschikbaar voor gebruikers met de rol <strong>Admin</strong>.
                </p>

                <div class="stats">
                    <article class="stat">
                        <span class="stat__label">Actieve rol</span>
                        <strong>{{ auth()->user()->role }}</strong>
                    </article>

                    <article class="stat">
                        <span class="stat__label">Toegang</span>
                        <strong>Beveiligd</strong>
                    </article>

                    <article class="stat">
                        <span class="stat__label">Status</span>
                        <strong>Portal gereed</strong>
                    </article>
                </div>

                <div class="notice">
                    <p><strong>Beheer</strong></p>
                    <p>Deze pagina is voorbereid als startpunt voor toekomstige admin-modules en sluit visueel aan op de bestaande publieke en auth-pagina's.</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
