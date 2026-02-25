<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-bold mb-1.5" style="color: var(--ui-content);">Bienvenido de vuelta</h1>
        <p class="text-sm" style="color: var(--ui-content-muted);">Ingresa tus credenciales para continuar</p>
    </div>

    <form wire:submit="login" class="flex flex-col gap-4" novalidate>

        {{-- Email --}}
        <x-ui.auth-input
            wire:model="email"
            type="email"
            label="Email"
            placeholder="tu@email.com"
            autocomplete="email"
        />

        {{-- Password con show/hide y link "olvidé" --}}
        <x-ui.auth-password
            wire:model="password"
            label="Contraseña"
            placeholder="••••••••"
            autocomplete="current-password"
        >
            <x-slot:hint>
                <a href="#" class="text-xs transition-colors duration-200"
                   style="color:rgba(245,48,3,0.8);"
                   onmouseover="this.style.color='#f53003';"
                   onmouseout="this.style.color='rgba(245,48,3,0.8)';">
                    ¿Olvidaste tu contraseña?
                </a>
            </x-slot:hint>
        </x-ui.auth-password>

        {{-- Remember me --}}
        <label class="flex items-center gap-2.5 cursor-pointer select-none" x-data>
            <div class="relative">
                <input type="checkbox" wire:model="remember" class="sr-only peer">
                <div class="w-4 h-4 rounded peer-checked:opacity-0 opacity-100 transition-opacity"
                     style="background: var(--auth-input-bg); border: 1px solid var(--auth-input-border);"></div>
                <div class="absolute inset-0 w-4 h-4 rounded flex items-center justify-center
                            peer-checked:opacity-100 opacity-0 transition-opacity"
                     style="background:linear-gradient(135deg,#f53003,#c0392b);">
                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <span class="text-xs" style="color: var(--ui-content-muted);">Mantener sesión iniciada</span>
        </label>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3.5 rounded-xl text-sm font-semibold text-white mt-1
                       flex items-center justify-center gap-2 transition-all duration-200"
                style="background:linear-gradient(135deg,#f53003 0%,#c0392b 100%);
                       box-shadow:0 4px 20px rgba(245,48,3,0.35);"
                onmouseover="this.style.boxShadow='0 8px 28px rgba(245,48,3,0.52)';this.style.transform='translateY(-1px)';"
                onmouseout="this.style.boxShadow='0 4px 20px rgba(245,48,3,0.35)';this.style.transform='translateY(0)';">
            <span wire:loading.remove wire:target="login">Iniciar sesión</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Verificando...
            </span>
        </button>

    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
        <span class="text-xs" style="color: var(--ui-content-subtle);">¿No tienes cuenta?</span>
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
    </div>

    {{-- Link registro --}}
    <a href="{{ route('register') }}" wire:navigate
       class="flex items-center justify-center w-full py-3 rounded-xl text-sm font-medium
              transition-all duration-200"
       style="color: var(--ui-content-muted); background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);"
       onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
       onmouseout="this.style.background='var(--auth-link-bg)';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
        Crear cuenta nueva
    </a>
</div>
