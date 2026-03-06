@props(['title' => config('app.name', 'Laravel')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        /* Iconos del toggle — el CSS reacciona a html.dark sin depender de Alpine */
        .theme-icon-sun  { display: none;  }
        .theme-icon-moon { display: block; }
        html.dark .theme-icon-sun  { display: block; }
        html.dark .theme-icon-moon { display: none;  }
    </style>
    {{-- Aplica la clase dark ANTES del primer render (sin flash) --}}
    <script>
        (function () {
            var cookie = document.cookie.match(/darkMode=([^;]+)/);
            if (cookie) { var isDark = cookie[1] !== 'false'; }
            else {
                var stored = localStorage.getItem('darkMode');
                var isDark = stored !== null ? stored === 'true' : true;
            }
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.style.backgroundColor = isDark ? '#080c18' : '#F7F8FA';
            if (!cookie) {
                document.cookie = 'darkMode=' + isDark + ';path=/;max-age=31536000;SameSite=Lax';
            }
        })();
    </script>
</head>
<body class="antialiased overflow-x-hidden">
    {{ $slot }}
    @livewireScripts
</body>
</html>
