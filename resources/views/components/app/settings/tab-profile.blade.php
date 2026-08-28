<div class="divide-y divide-line">

    {{-- ── Perfil ──────────────────────────────────────────────────────── --}}
    <div class="pb-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.profile') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.profile_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('access::settings.profile-form')
        </div>
    </div>

    {{-- ── Cuenta ──────────────────────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.account') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.account_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('access::settings.account-form')
        </div>
    </div>

    {{-- ── Seguridad ───────────────────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.security') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.security_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('access::settings.password-form')
        </div>
    </div>

    {{-- ── Verificación de email ───────────────────────────────────────── --}}
    <div class="py-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.email_verification') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.email_verification_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('access::settings.email-verification-form')
        </div>
    </div>

    {{-- ── Autenticación de dos factores ───────────────────────────────── --}}
    <div class="pt-8 md:grid md:grid-cols-3 md:gap-8">
        <div class="md:col-span-1">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::settings.two_factor') }}</h3>
            <p class="mt-1 text-sm text-content-muted">{{ __('platform::settings.two_factor_desc') }}</p>
        </div>
        <div class="mt-4 md:col-span-2 md:mt-0">
            @livewire('access::settings.two-factor-form')
        </div>
    </div>

</div>
