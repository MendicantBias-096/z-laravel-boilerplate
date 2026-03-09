<div class="divide-y divide-line">

    {{-- ── Perfil ──────────────────────────────────────────────────────── --}}
    <div class="pb-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.profile') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.profile_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.profile-form')
        </div>
    </div>

    {{-- ── Cuenta ──────────────────────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.account') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.account_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.account-form')
        </div>
    </div>

    {{-- ── Seguridad ───────────────────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.security') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.security_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.password-form')
        </div>
    </div>

    {{-- ── Verificación de email ───────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.email_verification') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.email_verification_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.email-verification-form')
        </div>
    </div>

    {{-- ── Autenticación de dos factores ───────────────────────────────── --}}
    <div class="pt-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('settings.two_factor') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('settings.two_factor_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('app.general.settings.two-factor-form')
        </div>
    </div>

</div>
