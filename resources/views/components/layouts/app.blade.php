@props(['icon' => null, 'title' => null, 'parent' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            #page-container { padding-left: var(--sidebar-w, 16rem); }
            #page-header    { padding-left: var(--sidebar-w, 16rem); }
        }
    </style>
    <tallstackui:script />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased">

<div
    id="app-root"
    x-data="{
        mobileSidebarOpen: false,
        desktopSidebarOpen: true,
        darkMode: localStorage.getItem('darkMode') !== null
            ? localStorage.getItem('darkMode') === 'true'
            : window.matchMedia('(prefers-color-scheme: dark)').matches,
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        },
    }"
    x-bind:class="{ 'dark': darkMode }"
    x-bind:style="{ '--sidebar-w': desktopSidebarOpen ? '16rem' : '0rem' }"
>
    <div
        id="page-container"
        class="mx-auto flex min-h-screen w-full min-w-[320px] flex-col bg-canvas text-content transition-all duration-300 ease-out"
    >
        @persist('shell')
            <x-layouts.sidebar />
            <x-layouts.navbar />
        @endpersist

        <main id="page-content" class="flex max-w-full flex-auto flex-col pt-16">
            @if ($icon || $title)
                <div class="flex items-center gap-3 border-b border-line px-4 py-4 lg:px-8">
                    @if ($icon)
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-950">
                            <x-ui.icon :name="$icon" class="size-5 text-primary-600 dark:text-primary-400" />
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
            <div class="w-full p-4 lg:p-8">
                {{ $slot }}
            </div>
        </main>

        <footer class="flex flex-none items-center border-t border-line">
            <div class="flex w-full items-center justify-between px-4 py-4 lg:px-8">
                <span class="text-sm text-content-subtle">
                    © {{ date('Y') }} <span class="font-medium text-content-muted">{{ config('app.name') }}</span>
                </span>
                @if(config('app.developer'))
                <span class="text-sm text-content-subtle flex items-center gap-1.5">
                    Desarrollado con
                    <x-ui.icon name="heart" class="size-3.5 text-red-500 fill-red-500" />
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
</body>
</html>
