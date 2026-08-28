{{-- Overlay móvil --}}
<div
    x-show="$store.sidebar?.mobileOpen"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.sidebar.mobileOpen = false"
    class="fixed inset-0 z-40 bg-gray-950/60 lg:hidden"
></div>

<nav
    id="page-sidebar"
    class="fixed start-0 top-0 bottom-0 z-50 flex h-full w-64 flex-col border-r border-line bg-panel transition-transform duration-300 ease-out"
    x-bind:class="{
        'ltr:-translate-x-full': !$store.sidebar?.mobileOpen,
        'ltr:translate-x-0':      $store.sidebar?.mobileOpen,
        'lg:ltr:-translate-x-full': !$store.sidebar?.desktopOpen,
        'lg:ltr:translate-x-0':      $store.sidebar?.desktopOpen,
    }"
    aria-label="Navegación principal"
>
    {{-- Header --}}
    @php
        $sidebarLogoPath = \App\Modules\Platform\Models\Setting::get('logo_path');
        $sidebarLogoUrl  = $sidebarLogoPath ? Storage::disk('public')->url($sidebarLogoPath) : null;
        $sidebarName     = \App\Modules\Platform\Models\Setting::get('app_short_name') ?: \App\Modules\Platform\Models\Setting::get('app_name', config('app.name'));
    @endphp
    <div
        class="flex h-16 flex-none items-center justify-between border-b border-line px-5"
        x-data="{
            logoUrl: {{ $sidebarLogoUrl ? "'" . $sidebarLogoUrl . "'" : 'null' }},
            name:    '{{ addslashes($sidebarName) }}'
        }"
        @system-updated.window="logoUrl = $event.detail.logoUrl; name = $event.detail.name"
    >
        <a href="{{ route('dashboard') }}" wire:navigate class="group inline-flex min-w-0 items-center gap-2.5 font-semibold text-content hover:opacity-80 transition-opacity">
            <template x-if="logoUrl">
                <img :src="logoUrl" alt="Logo" class="h-8 w-8 shrink-0 rounded-lg object-cover">
            </template>
            <template x-if="!logoUrl">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg shrink-0"
                     style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);
                            box-shadow: 0 0 14px rgba(245,48,3,0.45);">
                    @svg('lucide-zap', 'size-4 text-white')
                </div>
            </template>
            <span class="truncate" x-text="name"></span>
        </a>

        <div class="flex items-center gap-1">
            {{-- Dark mode toggle --}}
            <button
                type="button"
                @click="$store.theme.toggle()"
                class="inline-flex cursor-pointer items-center justify-center rounded-md p-1.5 text-content-subtle hover:bg-panel-alt hover:text-content"
                title="Cambiar tema"
            >
                @svg('lucide-sun', 'size-5', ['x-show' => '$store.theme?.dark', 'x-cloak' => true])
                @svg('lucide-moon', 'size-5', ['x-show' => '!$store.theme?.dark'])
            </button>

            {{-- Cerrar móvil --}}
            <button
                type="button"
                @click="$store.sidebar.mobileOpen = false"
                class="inline-flex items-center justify-center rounded-md p-1.5 text-content-subtle hover:bg-danger/10 hover:text-danger lg:hidden"
            >
                @svg('lucide-x', 'size-5')
            </button>
        </div>
    </div>

    {{-- Navegación --}}
    <div class="flex-1 overflow-y-auto py-3">
        <nav class="space-y-0.5">
            @foreach (config('menu.menu', []) as $item)
                @if (isset($item['items']))
                    @php
                        $visibleChildren = collect($item['items'])->filter(function ($child) {
                            if (! isset($child['permission'])) return true;
                            return auth()->user()?->can($child['permission']);
                        })->values()->all();
                    @endphp

                    @if (count($visibleChildren) > 0)
                        <x-ui.nav-group
                            :label="$item['label']"
                            :icon="$item['icon'] ?? null"
                            :activeRoute="$item['active_route'] ?? null"
                            :items="$visibleChildren"
                        />
                    @endif
                @else
                    @php
                        $canSee    = ! isset($item['permission'])  || auth()->user()?->can($item['permission']);
                        $canSeeAny = ! isset($item['permissions']) || collect($item['permissions'])->contains(fn ($p) => auth()->user()?->can($p));
                    @endphp

                    @if ($canSee && $canSeeAny)
                        <x-ui.nav-item
                            :label="$item['label']"
                            :icon="$item['icon'] ?? null"
                            :route="$item['route']"
                            :activeRoute="$item['active_route'] ?? null"
                        />
                    @endif
                @endif
            @endforeach
        </nav>
    </div>
</nav>
