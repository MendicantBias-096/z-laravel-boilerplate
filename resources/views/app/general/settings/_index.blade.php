<div class="space-y-10">

    {{-- ── Sistema ───────────────────────────────────────────────────────── --}}
    @can('administrar sistema')
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">Sistema</h3>
            <p class="mt-1 text-sm text-content-muted">Nombre e identidad visual de la aplicación.</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.system-form')
        </div>
    </div>
    <div class="border-t border-line"></div>
    @endcan

    {{-- ── Perfil ────────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">Perfil</h3>
            <p class="mt-1 text-sm text-content-muted">Tu nombre visible para otros usuarios del sistema.</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.profile-form')
        </div>
    </div>

    {{-- ── Cuenta ────────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">Cuenta</h3>
            <p class="mt-1 text-sm text-content-muted">Tu nombre de usuario y correo electrónico de acceso.</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.account-form')
        </div>
    </div>

    {{-- ── Seguridad ─────────────────────────────────────────────────────── --}}
    <div class="md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">Seguridad</h3>
            <p class="mt-1 text-sm text-content-muted">Actualiza tu contraseña para mantener tu cuenta segura.</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.password-form')
        </div>
    </div>

    {{-- ── Información del sistema ───────────────────────────────────────── --}}
    <div class="flex justify-end">
        <p class="text-xs text-content-subtle">
            Laravel {{ $system[1]['value'] }} · PHP {{ $system[2]['value'] }} · {{ $system[0]['value'] }}
        </p>
    </div>

</div>
