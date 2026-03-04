<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title')</title>

    {{-- Modo oscuro inmediato (mismo patrón que app.blade.php) --}}
    <script>
        (function () {
            var cookie = document.cookie.match(/darkMode=([^;]+)/);
            var isDark = cookie ? cookie[1] !== 'false' : true;
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.style.backgroundColor = isDark ? '#080c18' : '#F7F8FA';
        })();
    </script>

    <style>
        :root {
            --bg:      #F7F8FA;
            --panel:   #FFFFFF;
            --line:    #E3E8EF;
            --text:    #0B1220;
            --muted:   #475569;
            --subtle:  #64748B;
            --primary: #f53003;
        }
        .dark {
            --bg:      #080c18;
            --panel:   #0d1222;
            --line:    #1c2a40;
            --text:    #e8eef8;
            --muted:   #a8b8cc;
            --subtle:  #6e88a4;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
                         "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            gap: 0;
        }

        .error-code {
            font-size: clamp(6rem, 20vw, 10rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.04em;
            color: var(--line);
        }
        .error-divider {
            width: 3rem;
            height: 3px;
            background: var(--primary);
            border-radius: 9999px;
            margin: 1.5rem auto;
        }
        .error-message {
            font-size: 1.125rem;
            font-weight: 500;
            color: var(--muted);
            text-align: center;
        }
        .error-back {
            margin-top: 2.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.875rem;
            color: var(--subtle);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid var(--line);
            border-radius: 0.5rem;
            transition: border-color 150ms, color 150ms;
        }
        .error-back:hover {
            color: var(--text);
            border-color: var(--subtle);
        }
        .error-back svg {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <div style="text-align: center;">
        <p class="error-code">@yield('code')</p>
        <div class="error-divider"></div>
        <p class="error-message">@yield('message')</p>

        <a href="{{ url('/') }}" class="error-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Regresar al inicio
        </a>
    </div>
</body>
</html>
