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
            <div class="w-full p-4 lg:p-8">
                {{ $slot }}
            </div>
        </main>

        <footer class="flex flex-none items-center border-t border-line">
            <div class="flex w-full items-center justify-between px-4 py-4 lg:px-8">
                <span class="text-sm text-content-subtle">
                    © {{ date('Y') }} <span class="font-medium text-content-muted">{{ config('app.name') }}</span>
                </span>
            </div>
        </footer>
    </div>
</div>

@livewireScripts
<x-toast />
</body>
</html>
