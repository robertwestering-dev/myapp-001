<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <style>
        :root {
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

        .topbar,
        .page-shell {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            padding: 24px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
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
            gap: 10px;
            padding: 12px 22px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            box-shadow: var(--shadow);
        }

        .pill--strong {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            border-color: transparent;
            cursor: pointer;
        }

        .page-shell {
            padding: 36px 0 64px;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 28px;
            align-items: stretch;
        }

        .hero-panel,
        .form-panel {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .hero-panel {
            border-radius: var(--radius-xl);
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero-panel::after {
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
        p {
            margin-top: 0;
        }

        h1 {
            font-size: clamp(1.35rem, 2.5vw, 2.4rem);
            line-height: 1.08;
            margin: 22px 0 20px;
            max-width: 62ch;
        }

        .lead,
        .helper,
        .side-note {
            color: var(--muted);
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .hero-points {
            margin: 28px 0 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.9;
        }

        .form-panel {
            border-radius: var(--radius-xl);
            padding: 32px;
        }

        .form-panel h2 {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .status,
        .errors {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: var(--radius-md);
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

        form {
            display: grid;
            gap: 18px;
        }

        label {
            display: grid;
            gap: 8px;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font-size: 1rem;
        }

        input:focus {
            outline: 2px solid rgba(217, 106, 43, 0.22);
            outline-offset: 2px;
        }

        .secondary {
            margin-top: 18px;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--muted);
        }

        .secondary a,
        .helper a {
            color: var(--accent-deep);
        }

        @media (max-width: 980px) {
            .page-shell {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .hero-panel,
            .form-panel {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand__mark" aria-hidden="true"></span>
            <span class="brand__copy">
                <span class="brand__title">Hermes Results</span>
                <span class="brand__subtitle">Oplossingen die werken</span>
            </span>
        </a>

        <a class="pill" href="{{ route('login') }}">Terug naar login</a>
    </header>

    <main class="page-shell">
        <section class="hero-panel">
            <span class="eyebrow">Create Access</span>
            <h1>Maak een account aan in dezelfde visuele wereld.</h1>
            <p class="lead">
                De registerpagina volgt nu exact dezelfde stijl als de loginpagina: hetzelfde warme kleurenpalet, dezelfde premium panelen en dezelfde rustige, zakelijke compositie.
            </p>

            <div class="hero-actions">
                <a class="pill pill--strong" href="{{ route('login') }}">Ik heb al een account</a>
            </div>

            <ul class="hero-points">
                <li>Registreer met een geldig e-mailadres</li>
                <li>Kies een veilig wachtwoord voor je account</li>
                <li>Na registratie ga je direct naar je welkomstpagina</li>
            </ul>
        </section>

        <section class="form-panel">
            <h2>Create account</h2>
            <p class="helper">Vul hieronder je gegevens in om een nieuw account aan te maken.</p>

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

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <label>
                    <span>Email address</span>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        placeholder="email@example.com"
                        required
                        autofocus
                    >
                </label>

                <label>
                    <span>Password</span>
                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="Password"
                        required
                    >
                </label>

                <label>
                    <span>Confirm password</span>
                    <input
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        required
                    >
                </label>

                <button type="submit" class="pill pill--strong" data-test="register-user-button">
                    Create account
                </button>
            </form>

            <p class="secondary">
                Heb je al een account?
                <a href="{{ route('login') }}">Log dan hier in</a>
            </p>

            <p class="side-note">De registratie blijft gebruikmaken van dezelfde Laravel Fortify-flow en validatie als voorheen.</p>
        </section>
    </main>
</body>
</html>
