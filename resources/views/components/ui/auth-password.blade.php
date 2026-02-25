@props([
    'label'    => 'Contraseña',
    'required' => true,
])

@php
    $modelName = $attributes->get('wire:model', '');
    $hasError  = $modelName && $errors->has($modelName);
@endphp

<div x-data="{ show: false }">
    @if(isset($hint))
        <div class="flex items-center justify-between mb-2">
            <label class="text-xs font-medium" style="color: var(--ui-content-muted);">
                {{ $label }}
                @if($required)
                    <span style="color:#f53003;">*</span>
                @endif
            </label>
            {{ $hint }}
        </div>
    @else
        <label class="block text-xs font-medium mb-2" style="color: var(--ui-content-muted);">
            {{ $label }}
            @if($required)
                <span style="color:#f53003;">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            {{ $attributes->class([
                'w-full px-4 py-3 pr-11 rounded-xl text-sm placeholder:text-content-subtle outline-none transition-all duration-200',
                'ring-1 ring-red-500/60' => $hasError,
            ]) }}
            style="background: var(--auth-input-bg); border: 1px solid var(--auth-input-border); color: var(--ui-content);"
            onfocus="this.style.borderColor='rgba(245,48,3,0.5)';this.style.boxShadow='0 0 0 3px rgba(245,48,3,0.1)';"
            onblur="this.style.borderColor='var(--auth-input-border)';this.style.boxShadow='';"
        >
        <button type="button" @click="show = !show"
                class="absolute inset-y-0 right-3.5 flex items-center transition-colors duration-200"
                style="color: var(--ui-content-subtle);"
                onmouseover="this.style.color='var(--ui-content)';"
                onmouseout="this.style.color='var(--ui-content-subtle)';">
            <svg x-show="!show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <svg x-show="show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>
        </button>
    </div>

    @if($hasError)
        <p class="mt-1.5 text-xs" style="color:#f97055;">{{ $errors->first($modelName) }}</p>
    @endif
</div>
