<div class="space-y-10">

    {{-- ── [TEST SCROLL] Secciones de relleno para probar preservación de scroll ── --}}
    @foreach (['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon'] as $section)
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">Sección {{ $section }}</h3>
            <p class="mt-1 text-sm text-content-muted">Contenido de prueba para generar scroll.</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            <x-ts-card>
                <div class="h-24 rounded-lg bg-panel-alt flex items-center justify-center text-content-subtle text-sm">
                    Placeholder {{ $section }}
                </div>
            </x-ts-card>
        </div>
    </div>
    <div class="border-t border-line"></div>
    @endforeach
    {{-- ── [/TEST SCROLL] ──────────────────────────────────────────────────── --}}

    {{-- ── Sistema ───────────────────────────────────────────────────────── --}}
    @can('administrar sistema')
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.system') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.system_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.system-form')
        </div>
    </div>
    <div class="border-t border-line"></div>
    @endcan

    {{-- ── Perfil ────────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.profile') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.profile_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.profile-form')
        </div>
    </div>

    {{-- ── Cuenta ────────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.account') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.account_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.account-form')
        </div>
    </div>

    {{-- ── Seguridad ─────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.security') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.security_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.password-form')
        </div>
    </div>

    {{-- ── Idioma ────────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.language') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.language_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.language-form')
        </div>
    </div>

    <div class="border-t border-line"></div>

    {{-- ── Mis permisos ──────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.permissions') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.permissions_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @php
                $userPermissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();
                $permGroups      = config('roles.module_groups', []);
                $permModules     = config('roles.permissions', []);
            @endphp
            <x-ts-card>
                <div class="space-y-6">
                    @foreach ($permGroups as $group => $modules)
                        <div>
                            <div class="mb-3 flex items-center gap-3">
                                <span class="text-xs font-bold uppercase tracking-widest text-content-subtle">
                                    {{ __("roles.groups.{$group}") }}
                                </span>
                                <div class="h-px flex-1 bg-line"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                                @foreach ($modules as $module)
                                    @if (isset($permModules[$module]))
                                        <fieldset class="rounded-lg border border-primary-400/40 dark:border-primary-600/40 px-4 pt-3">
                                            <legend class="-ml-1 px-2">
                                                <span class="text-xs font-semibold tracking-wide text-primary-600 dark:text-primary-400">
                                                    {{ __("roles.modules.{$module}") }}
                                                </span>
                                            </legend>
                                            <div class="grid gap-x-2 gap-y-0.5 pb-2 {{ count($permModules[$module]) > 1 ? 'grid-cols-2' : 'grid-cols-1' }}" style="margin-top: 6px;">
                                                @foreach ($permModules[$module] as $permission)
                                                    @php $has = in_array($permission, $userPermissions); @endphp
                                                    <div class="flex items-center gap-2 rounded px-2 py-1 ml-1 mt-[4px]">
                                                        <span class="relative size-3.5 shrink-0">
                                                            <span @class([
                                                                'absolute inset-0 rounded border transition-colors',
                                                                'border-primary-500 bg-primary-500' => $has,
                                                                'border-line bg-panel-alt' => !$has,
                                                            ])></span>
                                                            @if ($has)
                                                                <span class="absolute inset-0 flex items-center justify-center">
                                                                    <x-ui.icon name="check" class="size-2 text-white" />
                                                                </span>
                                                            @endif
                                                        </span>
                                                        <span @class([
                                                            'text-xs font-semibold leading-tight line-clamp-2',
                                                            'text-content-muted' => $has,
                                                            'text-content-subtle' => !$has,
                                                        ])>
                                                            {{ __("roles.permissions.{$permission}") }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </fieldset>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ts-card>
        </div>
    </div>

    {{-- ── Información del sistema ───────────────────────────────────────── --}}
    <div class="flex justify-end">
        <p class="text-xs text-content-subtle">
            Laravel {{ $system[1]['value'] }} · PHP {{ $system[2]['value'] }} · {{ $system[0]['value'] }}
        </p>
    </div>

</div>
