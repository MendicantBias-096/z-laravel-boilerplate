@props([
    'label',
    'required' => true,
    'type'     => 'text',
])

@php
    $modelName = $attributes->get('wire:model', '');
    $hasError  = $modelName && $errors->has($modelName);
@endphp

<div>
    <label class="block text-xs font-medium mb-2" style="color: var(--ui-content-muted);">
        {{ $label }}
        @if($required)
            <span style="color:#f53003;">*</span>
        @endif
    </label>
    <input
        type="{{ $type }}"
        {{ $attributes->class([
            'w-full px-4 py-3 rounded-xl text-sm placeholder:text-content-subtle outline-none transition-all duration-200',
            'ring-1 ring-red-500/60' => $hasError,
        ]) }}
        style="background: var(--auth-input-bg); border: 1px solid var(--auth-input-border); color: var(--ui-content);"
        onfocus="this.style.borderColor='rgba(245,48,3,0.5)';this.style.boxShadow='0 0 0 3px rgba(245,48,3,0.1)';"
        onblur="this.style.borderColor='var(--auth-input-border)';this.style.boxShadow='';"
    >
    @if($hasError)
        <p class="mt-1.5 text-xs" style="color:#f97055;">{{ $errors->first($modelName) }}</p>
    @endif
</div>
