<div class="divide-y divide-line">

    {{-- ── Mi rol ──────────────────────────────────────────────────────── --}}
    <div class="pb-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.my_role') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.my_role_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @php $currentRole = auth()->user()->roles->first(); @endphp
            @if ($currentRole)
                <div class="flex items-center gap-3">
                    @svg('lucide-shield', 'size-5 text-primary-500 shrink-0')
                    <div>
                        <p class="text-sm font-semibold text-content">{{ $currentRole->display_name ?? ucfirst($currentRole->name) }}</p>
                        <p class="text-xs text-content-muted">{{ $currentRole->name }}</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-content-muted">{{ __('platform::app.user_role_ph') }}</p>
            @endif
        </div>
    </div>

    {{-- ── Mis permisos ────────────────────────────────────────────────── --}}
    <div class="pt-8">
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.permissions') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.permissions_desc') }}</p>
        </div>

        @php
            $userPermissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();
            $permGroups      = config('roles.module_groups', []);
            $permModules     = config('roles.permissions', []);
        @endphp

        <div class="space-y-8">
            @foreach ($permGroups as $group => $modules)
                <div>
                    <div class="mb-3 flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-widest text-content-subtle">
                            {{ __("access::roles.groups.{$group}") }}
                        </span>
                        <div class="h-px flex-1 bg-line"></div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($modules as $module)
                            @if (isset($permModules[$module]))
                                <fieldset class="rounded-lg border border-primary-400/40 dark:border-primary-600/40 px-4 pt-3">
                                    <legend class="-ml-1 px-2">
                                        <span class="text-xs font-semibold tracking-wide text-primary-600 dark:text-primary-400">
                                            {{ __("access::roles.modules.{$module}") }}
                                        </span>
                                    </legend>
                                    <div class="grid gap-x-4 gap-y-1 pb-3 {{ count($permModules[$module]) > 1 ? 'grid-cols-2' : 'grid-cols-1' }}" style="margin-top: 6px;">
                                        @foreach ($permModules[$module] as $permission)
                                            @php $has = in_array($permission, $userPermissions); @endphp
                                            <div class="flex items-center gap-2 rounded px-2 py-1.5">
                                                <span class="relative size-3.5 shrink-0">
                                                    <span @class([
                                                        'absolute inset-0 rounded border transition-colors',
                                                        'border-primary-500 bg-primary-500' => $has,
                                                        'border-line bg-panel-alt' => !$has,
                                                    ])></span>
                                                    @if ($has)
                                                        <span class="absolute inset-0 flex items-center justify-center">
                                                            @svg('lucide-check', 'size-2 text-white')
                                                        </span>
                                                    @endif
                                                </span>
                                                <span @class([
                                                    'text-xs font-medium leading-tight',
                                                    'text-content' => $has,
                                                    'text-content-subtle' => !$has,
                                                ])>
                                                    {{ __("access::roles.permissions.{$permission}") }}
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
    </div>

</div>
