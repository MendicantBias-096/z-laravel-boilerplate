@php
    $user = auth()->user();
    $role = $user->getRoleNames()->first() ?? null;
@endphp

<header
    id="page-header"
    class="fixed start-0 end-0 top-0 z-30 flex h-16 flex-none items-center border-b border-line bg-panel transition-all duration-300 ease-out"
>
    <div class="flex w-full items-center justify-between px-4 lg:px-8">

        {{-- Toggle sidebar --}}
        <div class="flex items-center gap-2">
            <button
                type="button"
                @click="mobileSidebarOpen = true"
                class="inline-flex cursor-pointer items-center justify-center rounded-md border border-line bg-panel p-2 text-content-subtle hover:bg-panel-alt hover:text-content focus:outline-none lg:hidden"
            >
                <x-ui.icon name="bars-3" class="size-5" />
            </button>

            <button
                type="button"
                @click="desktopSidebarOpen = !desktopSidebarOpen"
                class="hidden cursor-pointer items-center justify-center rounded-md border border-line bg-panel p-2 text-content-subtle hover:bg-panel-alt hover:text-content focus:outline-none lg:inline-flex"
            >
                <x-ui.icon name="bars-3" class="size-5" />
            </button>
        </div>

        {{-- Marca (solo móvil) --}}
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center gap-2 font-semibold text-content lg:hidden">
            <div class="flex h-7 w-7 items-center justify-center rounded-lg shrink-0"
                 style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                        box-shadow: 0 0 10px rgba(245,48,3,0.4);">
                <x-ui.icon name="bolt" class="size-4 text-white" />
            </div>
            <span class="text-sm">{{ config('app.name') }}</span>
        </a>

        {{-- Derecha: usuario --}}
        <div class="relative" x-data="{ open: false }">

            {{-- Trigger --}}
            <button
                type="button"
                @click="open = !open"
                class="inline-flex items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-panel-alt focus:outline-none"
            >
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                     style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">

                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>

                <div class="hidden text-left sm:block">
                    <p class="text-sm font-semibold leading-tight text-content">{{ $user->name }}</p>
                    @if ($role)
                        <p class="text-xs leading-tight text-primary-600 dark:text-primary-400">{{ ucfirst($role) }}</p>
                    @endif
                </div>

                <x-ui.icon name="chevron-down" class="hidden size-4 text-content-subtle sm:block" />
            </button>

            {{-- Dropdown --}}
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                @click.outside="open = false"
                class="absolute end-0 z-10 mt-2 w-56 origin-top-right rounded-lg border border-line bg-panel shadow-lg"
            >
                {{-- Info --}}
                <div class="border-b border-line px-4 py-3">
                    <p class="truncate text-sm font-semibold text-content">{{ $user->name }}</p>
                    <p class="truncate text-xs text-content-subtle">{{ $user->email }}</p>
                    @if ($role)
                        <span class="mt-1.5 inline-flex items-center rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                            {{ ucfirst($role) }}
                        </span>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="p-1">
                    <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-content-muted hover:bg-panel-alt hover:text-content">
                        <x-ui.icon name="cog-6-tooth" class="size-4 text-content-subtle" />
                        Configuración
                    </a>
                </div>

                <div class="border-t border-line p-1">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm text-danger hover:bg-danger/10">
                            <x-ui.icon name="arrow-right-on-rectangle" class="size-4" />
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
