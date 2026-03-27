<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin gebruikers</title>
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
            width: min(1240px, calc(100% - 32px));
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

        .pill,
        .ghost-pill,
        .danger-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .pill {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
        }

        .ghost-pill {
            background: rgba(255, 255, 255, 0.7);
            border-color: var(--line);
            color: var(--ink);
        }

        .danger-pill {
            background: rgba(168, 74, 25, 0.12);
            border-color: rgba(168, 74, 25, 0.2);
            color: var(--accent-deep);
            box-shadow: none;
        }

        .portal {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 24px;
        }

        .sidebar,
        .panel {
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
            margin: 18px 0 10px;
            font-size: clamp(1.5rem, 2.8vw, 2.5rem);
            line-height: 1.08;
        }

        .lead {
            color: var(--muted);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 65ch;
        }

        .status,
        .errors {
            margin: 22px 0 0;
            padding: 14px 16px;
            border-radius: 18px;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        .status {
            background: rgba(32, 69, 58, 0.1);
            color: var(--forest);
        }

        .errors {
            background: rgba(168, 74, 25, 0.12);
            color: var(--accent-deep);
        }

        .toolbar {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin: 28px 0 24px;
        }

        .search {
            min-width: min(100%, 360px);
            display: grid;
            gap: 10px;
        }

        .search label {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .search__row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search input {
            flex: 1 1 220px;
            min-width: 0;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 24px;
            border: 1px solid rgba(22, 33, 29, 0.08);
            background: rgba(255, 255, 255, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        th {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.84rem;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .icon-button {
            width: 42px;
            height: 42px;
            padding: 0;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.7);
            color: var(--ink);
            box-shadow: none;
        }

        .icon-button--danger {
            color: var(--accent-deep);
            background: rgba(168, 74, 25, 0.12);
            border-color: rgba(168, 74, 25, 0.2);
        }

        .icon-button svg {
            width: 18px;
            height: 18px;
        }

        .actions form {
            margin: 0;
        }

        .muted {
            color: var(--muted);
        }

        .meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination__link,
        .pagination__current {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0 14px;
            border-radius: 14px;
            font-family: Arial, Helvetica, sans-serif;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
        }

        .pagination__current {
            color: #fff;
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-soft) 100%);
            border-color: transparent;
        }

        .empty {
            padding: 26px;
            border-radius: 24px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }

        @media (max-width: 980px) {
            .portal {
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
                    <a href="{{ route('admin.portal') }}" class="menu__item">Admin-portal</a>
                    <a href="{{ route('admin.users.index') }}" class="menu__item menu__item--active">Gebruikers</a>
                    <div class="menu__item">Instellingen</div>
                </nav>
            </aside>

            <section class="panel">
                <span class="panel__eyebrow">Gebruikers</span>
                <h1>Gebruikersoverzicht</h1>
                <p class="lead">
                    Doorzoek en exporteer alle gebruikers. De lijst is gesorteerd op naam en toont telkens 15 records per pagina.
                </p>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="errors">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="toolbar">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="search">
                        <label for="search">Zoek op naam of emailadres</label>
                        <div class="search__row">
                            <input
                                id="search"
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Bijvoorbeeld anna of anna@example.com"
                            >
                            <button type="submit" class="pill">Zoeken</button>
                            <a href="{{ route('admin.users.index') }}" class="ghost-pill">Reset</a>
                        </div>
                    </form>

                    <div class="actions">
                        <a href="{{ route('admin.users.create') }}" class="pill">Nieuwe gebruiker</a>
                        <a href="{{ route('admin.users.export', ['search' => $search]) }}" class="ghost-pill">Export CSV</a>
                    </div>
                </div>

                @if ($users->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Naam</th>
                                    <th>Emailadres</th>
                                    <th>Rol</th>
                                    <th>Email verified</th>
                                    <th>Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge">{{ $user->role }}</span></td>
                                        <td class="muted">{{ $user->email_verified_at?->format('d-m-Y H:i') ?? 'Niet geverifieerd' }}</td>
                                        <td>
                                            <div class="actions">
                                                <a
                                                    href="{{ route('admin.users.edit', $user) }}"
                                                    class="ghost-pill icon-button"
                                                    aria-label="Wijzig {{ $user->name }}"
                                                    title="Wijzigen"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M12 20h9"/>
                                                        <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>
                                                    </svg>
                                                </a>

                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="danger-pill icon-button icon-button--danger"
                                                        aria-label="Verwijder {{ $user->name }}"
                                                        title="Verwijderen"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M3 6h18"/>
                                                            <path d="M8 6V4h8v2"/>
                                                            <path d="M19 6l-1 14H6L5 6"/>
                                                            <path d="M10 11v6"/>
                                                            <path d="M14 11v6"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="meta">
                        <p class="muted">
                            Resultaten {{ $users->firstItem() }} t/m {{ $users->lastItem() }} van {{ $users->total() }}
                        </p>

                        <nav class="pagination" aria-label="Paginatie">
                            @if ($users->onFirstPage())
                                <span class="pagination__link muted">Vorige</span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" class="pagination__link">Vorige</a>
                            @endif

                            @foreach (range(max(1, $users->currentPage() - 1), min($users->lastPage(), $users->currentPage() + 1)) as $page)
                                @if ($page === $users->currentPage())
                                    <span class="pagination__current">{{ $page }}</span>
                                @else
                                    <a href="{{ $users->url($page) }}" class="pagination__link">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" class="pagination__link">Volgende</a>
                            @else
                                <span class="pagination__link muted">Volgende</span>
                            @endif
                        </nav>
                    </div>
                @else
                    <div class="empty">
                        <strong>Geen gebruikers gevonden.</strong>
                        <p class="muted">Pas de zoekterm aan of reset het zoekveld om de volledige lijst te zien.</p>
                    </div>
                @endif
            </section>
        </main>
    </div>
</body>
</html>
