@props(['icon' => null, 'title' => null, 'parent' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Modules\Platform\Models\Setting::get('app_name', config('app.name', 'Laravel')) }}{{ $title ? ' | ' . $title : '' }}</title>
    @php $faviconPath = \App\Modules\Platform\Models\Setting::get('favicon_path') ?? \App\Modules\Platform\Models\Setting::get('logo_path'); @endphp
    @if($faviconPath)
        <link rel="icon" href="{{ Storage::disk('public')->url($faviconPath) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <script>
        (function () {
            var cookie = document.cookie.match(/darkMode=([^;]+)/);
            var isDark = cookie ? cookie[1] !== 'false' : true;
            var bg = isDark ? '#080c18' : '#F7F8FA';
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.style.backgroundColor = bg;
            if (!cookie) {
                document.cookie = 'darkMode=true;path=/;max-age=31536000;SameSite=Lax';
            }
        })();
    </script>
    <style>
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            #page-container  { padding-left: var(--sidebar-w, 16rem); }
            #page-header     { padding-left: var(--sidebar-w, 16rem); }
            #page-subheader  { padding-left: var(--sidebar-w, 16rem); }
            #page-footer     { padding-left: var(--sidebar-w, 16rem); }
        }
    </style>
    <tallstackui:script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased">

<div
    id="app-root"
    class="{{ request()->cookie('darkMode', 'true') !== 'false' ? 'dark' : '' }}"
    x-data="{}"
    x-bind:class="{ 'dark': $store.theme?.dark }"
    x-bind:style="{ '--sidebar-w': $store.sidebar?.desktopOpen ? '16rem' : '0rem' }"
>
    <div
        id="page-container"
        class="mx-auto flex min-h-screen w-full min-w-[320px] flex-col bg-canvas text-content transition-all duration-300 ease-out"
    >
        <x-layouts.sidebar />
        @livewire('platform::layouts.navbar')

        {{-- Page subheader: fijo debajo del navbar --}}
        @if ($icon || $title)
            <div
                id="page-subheader"
                class="fixed start-0 end-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-canvas px-4 transition-all duration-300 ease-out lg:px-8" style="top: 4rem;"
            >
                @if ($icon)
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-950" style="margin-left: 2rem;">
                        @svg($icon, 'size-5 text-primary-600 dark:text-primary-400')
                    </div>
                @endif
                <h1 class="text-lg font-semibold text-content">
                    @if ($parent)
                        <span class="font-normal text-content-muted">{{ $parent }}</span>
                        <span class="mx-1.5 font-normal text-content-subtle">|</span>
                    @endif
                    {{ $title }}
                </h1>
            </div>
        @endif

        {{-- Contenido scrollable --}}
        <main
            id="page-content"
            class="flex max-w-full flex-auto flex-col"
            style="padding-top: {{ ($icon || $title) ? '8rem' : '4rem' }}; padding-bottom: 3.5rem;"
        >
            <div class="w-full p-4 lg:p-8">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer fijo abajo --}}
        <footer id="page-footer" class="fixed start-0 end-0 bottom-0 z-20 flex flex-none items-center border-t border-line bg-canvas transition-all duration-300 ease-out">
            <div class="flex w-full items-center justify-between px-4 py-4 lg:px-8">
                <span class="text-sm text-content-subtle">
                    © {{ date('Y') }} <span class="font-medium text-content-muted">{{ \App\Modules\Platform\Models\Setting::get('app_name', config('app.name')) }}</span>
                </span>
                @if(config('app.developer'))
                <span class="text-sm text-content-subtle flex items-center gap-1.5">
                    {{ __('platform::settings.developed_with') }}
                    @svg('lucide-heart', 'size-3.5 text-red-500 fill-red-500')
                    <span class="font-medium text-content-muted">{{ config('app.developer') }}</span>
                </span>
                @endif
            </div>
        </footer>
    </div>

    <x-ts-toast />
    <x-ts-dialog />

</div>

@livewireScripts
<script>
    window.addEventListener('system-updated', ({ detail }) => {
        if (detail.name) document.title = detail.name;
        if (detail.faviconUrl) {
            let link = document.querySelector("link[rel='icon']");
            if (!link) { link = document.createElement('link'); link.rel = 'icon'; document.head.appendChild(link); }
            link.href = detail.faviconUrl + '?t=' + Date.now();
        }
    });
</script>
</body>
</html>
