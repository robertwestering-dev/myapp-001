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
            display: flex;
            flex-direction: column;
            font-family: "Georgia", "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(217, 106, 43, 0.18), transparent 32%),
                radial-gradient(circle at 85% 20%, rgba(32, 69, 58, 0.14), transparent 28%),
                linear-gradient(180deg, #f8f2e8 0%, #f2ece2 48%, #ebe3d8 100%);
        }

        .topbar__inner,
        .site-footer__inner,
        .welcome {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
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
            flex: 1;
            padding: 48px 0 64px;
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
            .brand__logo {
                height: 60px;
            }

            .welcome__card {
                padding: 28px;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pill">Logout</button>
        </form>
    </x-hermes-header>

    <main class="welcome">
        <section class="welcome__card">
            <h1>Welkom: {{ auth()->user()->email }}</h1>
            <p>U bent ingelogd.</p>
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
