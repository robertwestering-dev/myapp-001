<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
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

        .pill,
        .ghost-pill {
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
            max-width: 62ch;
        }

        .errors {
            margin: 22px 0 0;
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(168, 74, 25, 0.12);
            color: var(--accent-deep);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        form {
            display: grid;
            gap: 18px;
            margin-top: 28px;
        }

        label {
            display: grid;
            gap: 8px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        input,
        select {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font: inherit;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
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
                <h1>{{ $title }}</h1>
                <p class="lead">{{ $intro }}</p>

                @if ($errors->any())
                    <div class="errors">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ $isEditing ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <label>
                        <span>Naam</span>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    </label>

                    <label>
                        <span>Emailadres</span>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </label>

                    <label>
                        <span>Rol</span>
                        <select name="role" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ $role }}</option>
                            @endforeach
                        </select>
                    </label>

                    @unless ($isEditing)
                        <label>
                            <span>Wachtwoord</span>
                            <input type="password" name="password" required>
                        </label>

                        <label>
                            <span>Bevestig wachtwoord</span>
                            <input type="password" name="password_confirmation" required>
                        </label>
                    @endunless

                    <div class="form-actions">
                        <button type="submit" class="pill">{{ $submitLabel }}</button>
                        <a href="{{ route('admin.users.index') }}" class="ghost-pill">Terug naar overzicht</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
