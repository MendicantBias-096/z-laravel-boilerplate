@php
    $languages = \App\Modules\Platform\Enums\Language::cases();
    $current = app()->getLocale();
@endphp

<div class="relative" x-data="{ open: false }">
    <button type="button"
            @click="open = !open"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg cursor-pointer transition-all duration-200 uppercase text-xs font-semibold"
            style="color: var(--ui-content-muted); border: 1px solid var(--auth-link-border);"
            onmouseover="this.style.background='var(--auth-link-bg)';this.style.color='var(--ui-content)';"
            onmouseout="this.style.background='transparent';this.style.color='var(--ui-content-muted)';">
        {{ $current }}
    </button>

    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute right-0 mt-2 w-44 rounded-xl shadow-lg z-50 {{ count($languages) > 10 ? 'max-h-80 overflow-y-auto' : '' }}"
         style="background: var(--ui-panel); border: 1px solid var(--ui-line);">
        <div class="p-1.5 space-y-1">
            @foreach ($languages as $lang)
                <form method="POST" action="{{ route('locale.update') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $lang->value }}">
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-colors duration-150
                                   {{ $current === $lang->value ? 'font-semibold' : '' }}"
                            style="color: var(--ui-content{{ $current === $lang->value ? '' : '-muted' }});
                                   background: {{ $current === $lang->value ? 'var(--ui-panel-alt)' : 'transparent' }};"
                            onmouseover="this.style.background='var(--ui-panel-alt)';this.style.color='var(--ui-content)';"
                            onmouseout="this.style.background='{{ $current === $lang->value ? 'var(--ui-panel-alt)' : 'transparent' }}';this.style.color='var(--ui-content{{ $current === $lang->value ? '' : '-muted' }})';">
                        <span class="uppercase text-[11px] font-bold w-6 text-center shrink-0 tracking-wide"
                              style="color: var(--ui-content-subtle);">{{ $lang->value }}</span>
                        <span class="text-sm truncate">{{ $lang->label() }}</span>
                        @if ($current === $lang->value)
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
