<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
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

        .topbar,
        .welcome {
            width: min(1180px, calc(100% - 32px));
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

        .welcome {
            padding: 48px 0 64px;
        }

        .welcome__card {
            padding: 40px;
            border-radius: 32px;
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(1.3rem, 2.5vw, 2.4rem);
            line-height: 1.08;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 1.1rem;
        }

        @media (max-width: 720px) {
            .welcome__card {
                padding: 28px;
            }
        }
    </style>
</head>
<body>
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

    <main class="welcome">
        <section class="welcome__card">
            <h1>Welkom: {{ auth()->user()->email }}</h1>
            <p>U bent ingelogd.</p>
        </section>
    </main>
</body>
</html>
