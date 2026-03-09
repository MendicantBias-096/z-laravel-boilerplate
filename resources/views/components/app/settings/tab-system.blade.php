@props(['system'])

<div class="divide-y divide-line">

    {{-- ── Identidad (solo admin) ──────────────────────────────────────── --}}
    @can('administrar sistema')
    <div class="pb-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.system_identity') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.system_identity_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.system-form')
        </div>
    </div>
    @endcan

    {{-- ── Idioma ──────────────────────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.system_language') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.system_language_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.language-form')
        </div>
    </div>

    {{-- ── Información del sistema ─────────────────────────────────────── --}}
    @can('administrar sistema')
    <div class="pt-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.system_info') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.system_info_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            <div class="space-y-3">
                @foreach ($system as $item)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-content-muted">{{ $item['label'] }}</span>
                        <span class="text-sm font-medium text-content">{{ $item['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endcan

</div>
