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
            var stored = localStorage.getItem('darkMode');
            var dark = stored !== null
                ? stored === 'true'
                : window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (dark) document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="antialiased overflow-x-hidden">
    {{ $slot }}
    @livewireScripts
    <script>
        // Re-aplica el tema tras cada wire:navigate (Livewire reemplaza el DOM)
        document.addEventListener('livewire:navigated', function () {
            var stored = localStorage.getItem('darkMode');
            var dark = stored !== null
                ? stored === 'true'
                : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        });

        // Sigue la preferencia del sistema si no hay override manual
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (localStorage.getItem('darkMode') === null) {
                document.documentElement.classList.toggle('dark', e.matches);
            }
        });

        // Función global usada por los botones toggle
        window.toggleTheme = function () {
            var dark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', dark);
        };
    </script>
</body>
</html>
