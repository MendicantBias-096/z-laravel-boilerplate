<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-bold text-white mb-1.5">Crear cuenta</h1>
        <p class="text-sm text-white/45">Completa los datos para registrarte</p>
    </div>

    <form wire:submit="register" class="flex flex-col gap-4" novalidate>

        {{-- Nombre --}}
        <div>
            <label for="name" class="block text-xs font-medium text-white/50 mb-2">
                Nombre completo <span style="color:#f53003;">*</span>
            </label>
            <input type="text" id="name" wire:model="name"
                   placeholder="Tu nombre" autocomplete="name"
                   class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/25
                          outline-none transition-all duration-200
                          @error('name') ring-1 ring-red-500/60 @enderror"
                   style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);"
                   onfocus="this.style.borderColor='rgba(245,48,3,0.5)';this.style.boxShadow='0 0 0 3px rgba(245,48,3,0.1)';"
                   onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none';">
            @error('name')
                <p class="mt-1.5 text-xs" style="color:#f97055;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-medium text-white/50 mb-2">
                Email <span style="color:#f53003;">*</span>
            </label>
            <input type="email" id="email" wire:model="email"
                   placeholder="tu@email.com" autocomplete="email"
                   class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-white/25
                          outline-none transition-all duration-200
                          @error('email') ring-1 ring-red-500/60 @enderror"
                   style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);"
                   onfocus="this.style.borderColor='rgba(245,48,3,0.5)';this.style.boxShadow='0 0 0 3px rgba(245,48,3,0.1)';"
                   onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none';">
            @error('email')
                <p class="mt-1.5 text-xs" style="color:#f97055;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <label for="password" class="block text-xs font-medium text-white/50 mb-2">
                Contraseña <span style="color:#f53003;">*</span>
            </label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'"
                       id="password" wire:model="password"
                       placeholder="Mínimo 8 caracteres" autocomplete="new-password"
                       class="w-full px-4 py-3 pr-11 rounded-xl text-sm text-white placeholder-white/25
                              outline-none transition-all duration-200
                              @error('password') ring-1 ring-red-500/60 @enderror"
                       style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);"
                       onfocus="this.style.borderColor='rgba(245,48,3,0.5)';this.style.boxShadow='0 0 0 3px rgba(245,48,3,0.1)';"
                       onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none';">
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3.5 flex items-center text-white/30 hover:text-white/60 transition-colors">
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
            @error('password')
                <p class="mt-1.5 text-xs" style="color:#f97055;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div x-data="{ show: false }">
            <label for="password_confirmation" class="block text-xs font-medium text-white/50 mb-2">
                Confirmar contraseña <span style="color:#f53003;">*</span>
            </label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'"
                       id="password_confirmation" wire:model="password_confirmation"
                       placeholder="Repite tu contraseña" autocomplete="new-password"
                       class="w-full px-4 py-3 pr-11 rounded-xl text-sm text-white placeholder-white/25
                              outline-none transition-all duration-200
                              @error('password_confirmation') ring-1 ring-red-500/60 @enderror"
                       style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);"
                       onfocus="this.style.borderColor='rgba(245,48,3,0.5)';this.style.boxShadow='0 0 0 3px rgba(245,48,3,0.1)';"
                       onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none';">
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-3.5 flex items-center text-white/30 hover:text-white/60 transition-colors">
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
            @error('password_confirmation')
                <p class="mt-1.5 text-xs" style="color:#f97055;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3.5 rounded-xl text-sm font-semibold text-white mt-1
                       flex items-center justify-center gap-2 transition-all duration-200"
                style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                       box-shadow:0 4px 20px rgba(245,48,3,0.35);"
                onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
            <span wire:loading.remove wire:target="register">Crear cuenta</span>
            <span wire:loading wire:target="register" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Registrando...
            </span>
        </button>

    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px" style="background:rgba(255,255,255,0.08);"></div>
        <span class="text-xs text-white/25">¿Ya tienes cuenta?</span>
        <div class="flex-1 h-px" style="background:rgba(255,255,255,0.08);"></div>
    </div>

    {{-- Link login --}}
    <a href="{{ route('login') }}" wire:navigate
       class="flex items-center justify-center w-full py-3 rounded-xl text-sm font-medium
              text-white/60 hover:text-white transition-all duration-200"
       style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);"
       onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.borderColor='rgba(255,255,255,0.18)';"
       onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.borderColor='rgba(255,255,255,0.1)';">
        Iniciar sesión
    </a>
</div>
