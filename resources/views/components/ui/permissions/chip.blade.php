@props(['permission' => '', 'module' => '', 'granted' => false])

{{-- Un permiso que no comparte columna con nadie: restaurar, administrar, los
     eventos de notificación. Lleva su etiqueta encima porque no hay
     encabezado que la diga por él. --}}
@php
    $etiqueta = __('access::roles.permissions')[$permission] ?? $permission;
    $descripcion = __('access::roles.descriptions')[$permission] ?? '';
    $id = 'chip-'.\Illuminate\Support\Str::slug($permission);
@endphp

<label
    class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium transition-colors
        border-line bg-panel-alt text-content-muted hover:border-content-subtle hover:text-content
        has-[:checked]:border-primary-600/45 has-[:checked]:bg-primary-500/10 has-[:checked]:text-content
        has-[:focus-visible]:outline has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-primary-600
        dark:has-[:checked]:border-primary-500/45"
>
    <input
        type="checkbox"
        wire:model.live="permissionList"
        wire:key="{{ $id }}"
        value="{{ $permission }}"
        aria-label="{{ __('platform::app.user_perm_cell', ['permission' => $etiqueta, 'module' => __("access::roles.modules.{$module}")]) }}"
        @if ($descripcion) aria-describedby="{{ $id }}-desc" @endif
        class="peer sr-only"
    />

    <span class="size-1.5 rounded-full bg-content-subtle transition-colors peer-checked:bg-primary-600 dark:peer-checked:bg-primary-500"></span>

    {{ $etiqueta }}

    @if ($descripcion)
        {{-- La descripción, alcanzable sin ratón. --}}
        <span id="{{ $id }}-desc" class="sr-only">{{ $descripcion }}</span>
    @endif
</label>
