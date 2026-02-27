<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – {{ __('Page Not Found') }} | Comproller</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f8fafc;
            --bg-card: #f1f5f9;
            --text: #0f172a;
            --text-muted: #64748b;
            --accent: #d97706;
            --accent-hover: #b45309;
            --accent-bg: #fef3c7;
            --accent-text: #92400e;
            --btn-bg: #e2e8f0;
            --btn-text: #334155;
            --btn-hover: #cbd5e1;
            --icon-bg: rgba(251,191,36,.12);
            --ring: rgba(251,191,36,.25);
            --footer: #94a3b8;
            --toggle-bg: #e2e8f0;
            --toggle-color: #64748b;
        }

        html.dark {
            --bg: #0f172a;
            --bg-card: #1e293b;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --accent: #f59e0b;
            --accent-hover: #fbbf24;
            --accent-bg: rgba(251,191,36,.15);
            --accent-text: #fbbf24;
            --btn-bg: #1e293b;
            --btn-text: #e2e8f0;
            --btn-hover: #334155;
            --icon-bg: rgba(251,191,36,.18);
            --ring: rgba(251,191,36,.2);
            --footer: #334155;
            --toggle-bg: #1e293b;
            --toggle-color: #94a3b8;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 3rem 1.5rem;
            transition: background .3s, color .3s;
        }

        /* Theme toggle */
        .toggle-btn {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            padding: .5rem;
            border-radius: .5rem;
            background: var(--toggle-bg);
            color: var(--toggle-color);
            border: none;
            cursor: pointer;
            transition: background .2s;
            display: flex;
        }
        .toggle-btn:hover { filter: brightness(.9); }
        .toggle-btn svg { width: 1.25rem; height: 1.25rem; }
        .icon-sun { display: none; }
        html.dark .icon-moon { display: none; }
        html.dark .icon-sun { display: block; }

        /* Card */
        .card {
            text-align: center;
            max-width: 32rem;
            width: 100%;
            animation: fade-in-up .5s ease-out both;
        }

        /* Floating icon */
        .icon-wrap {
            margin: 0 auto 2rem;
            width: 6rem; height: 6rem;
            border-radius: 1rem;
            background: var(--icon-bg);
            box-shadow: 0 0 0 1px var(--ring);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: float 4s ease-in-out infinite;
        }
        .icon-wrap svg { width: 3rem; height: 3rem; color: var(--accent); stroke: var(--accent); }

        /* Badge */
        .badge {
            display: inline-block;
            margin-bottom: 1rem;
            padding: .25rem .75rem;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent-text);
            background: var(--accent-bg);
            border-radius: 9999px;
        }

        h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            letter-spacing: -.02em;
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .desc {
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .625rem 1.25rem;
            font-size: .875rem;
            font-weight: 600;
            font-family: inherit;
            border-radius: .5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s, filter .15s, background .15s;
        }
        .btn:hover { transform: scale(1.02); }
        .btn:active { transform: scale(.97); }
        .btn svg { width: 1rem; height: 1rem; }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }
        .btn-primary:hover { filter: brightness(1.1); }

        .btn-secondary {
            background: var(--btn-bg);
            color: var(--btn-text);
        }
        .btn-secondary:hover { background: var(--btn-hover); }

        /* Footer */
        footer {
            margin-top: 4rem;
            font-size: .75rem;
            color: var(--footer);
        }

        /* Animations */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
    </style>
</head>

<body>
    <button class="toggle-btn" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="icon-sun" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M3 12h2.25m.386-6.364-1.591 1.591M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
        </svg>
        <svg class="icon-moon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
        </svg>
    </button>

    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/>
            </svg>
        </div>

        <span class="badge">404</span>

        <h1>{{ __('Page not found') }}</h1>

        <p class="desc">{{ __("The page you are looking for doesn't exist or has been moved.") }}</p>

        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                {{ __('Back to home') }}
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                {{ __('Go back') }}
            </a>
        </div>
    </div>

    <footer>&copy; <x-year /> Comproller System v1.0</footer>

    <script>
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</body>

</html>