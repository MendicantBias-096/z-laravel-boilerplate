<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full"
             style="background: rgba(245,48,3,0.1);">
            @svg('lucide-mail-check', 'size-7', ['style' => 'color:#f53003;'])
        </div>
        <h1 class="text-2xl font-bold mb-1.5" style="color: var(--ui-content);">{{ __('platform::public.verify_email_title') }}</h1>
        <p class="text-sm" style="color: var(--ui-content-muted);">{{ __('platform::public.verify_email_subtitle') }}</p>
    </div>

    @if ($sent)
        <div class="mb-4 rounded-xl p-4 text-sm text-center"
             style="background: rgba(34,197,94,0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2);">
            {{ __('platform::public.verify_email_sent') }}
        </div>
    @endif

    <button wire:click="resend"
            wire:loading.attr="disabled"
            type="button"
            class="w-full h-12 rounded-xl text-sm font-semibold text-white relative overflow-hidden transition-all duration-200"
            style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                   box-shadow:0 4px 20px rgba(245,48,3,0.35);"
            onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
            onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
        <span wire:loading.class="opacity-0 -translate-y-2"
              class="absolute inset-0 flex items-center justify-center transition-all duration-200 ease-in-out">
            {{ __('platform::public.resend_verification') }}
        </span>
        <span wire:loading.class.remove="opacity-0 translate-y-2"
              class="absolute inset-0 flex items-center justify-center gap-2 transition-all duration-200 ease-in-out opacity-0 translate-y-2">
            @svg('lucide-loader-circle', 'w-4 h-4 animate-spin')
            {{ __('platform::public.sending') }}
        </span>
    </button>

    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit"
                    class="text-sm transition-colors duration-200"
                    style="color: var(--ui-content-subtle);"
                    onmouseover="this.style.color='var(--ui-content-muted)';"
                    onmouseout="this.style.color='var(--ui-content-subtle)';">
                {{ __('platform::public.logout') }}
            </button>
        </form>
    </div>
</div>
