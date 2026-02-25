<div>
    {{-- Header --}}
    <div class="mb-7 text-center">
        <h1 class="text-2xl font-bold mb-1.5" style="color: var(--ui-content);">Crear cuenta</h1>
        <p class="text-sm" style="color: var(--ui-content-muted);">Completa los datos para registrarte</p>
    </div>

    <form wire:submit="register" class="flex flex-col gap-4" novalidate>

        {{-- Nombre --}}
        <x-ui.auth-input
            wire:model="name"
            label="Nombre completo"
            placeholder="Tu nombre"
            autocomplete="name"
        />

        {{-- Email --}}
        <x-ui.auth-input
            wire:model="email"
            type="email"
            label="Email"
            placeholder="tu@email.com"
            autocomplete="email"
        />

        {{-- Password --}}
        <x-ui.auth-password
            wire:model="password"
            label="Contraseña"
            placeholder="Mínimo 8 caracteres"
            autocomplete="new-password"
        />

        {{-- Confirm Password --}}
        <x-ui.auth-password
            wire:model="password_confirmation"
            label="Confirmar contraseña"
            placeholder="Repite tu contraseña"
            autocomplete="new-password"
        />

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
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
        <span class="text-xs" style="color: var(--ui-content-subtle);">¿Ya tienes cuenta?</span>
        <div class="flex-1 h-px" style="background: var(--auth-divider);"></div>
    </div>

    {{-- Link login --}}
    <a href="{{ route('login') }}" wire:navigate
       class="flex items-center justify-center w-full py-3 rounded-xl text-sm font-medium
              transition-all duration-200"
       style="color: var(--ui-content-muted); background: var(--auth-link-bg); border: 1px solid var(--auth-link-border);"
       onmouseover="this.style.background='var(--auth-link-bg-hover)';this.style.borderColor='var(--auth-link-border-hover)';this.style.color='var(--ui-content)';"
       onmouseout="this.style.background='var(--auth-link-bg)';this.style.borderColor='var(--auth-link-border)';this.style.color='var(--ui-content-muted)';">
        Iniciar sesión
    </a>
</div>
