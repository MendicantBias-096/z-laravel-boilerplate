<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-content">Dashboard</h1>
        <p class="mt-1 text-sm text-content-muted">
            Bienvenido, <strong>{{ auth()->user()->email }}</strong>
        </p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-lg border border-line bg-panel p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-950/50">
                    @svg('lucide-users', 'size-6 text-primary-600 dark:text-primary-400')
                </div>
                <div>
                    <p class="text-sm text-content-muted">Rol</p>
                    <p class="text-lg font-semibold text-content">
                        {{ auth()->user()->roles->first()?->display_name ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-line bg-panel p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                    @svg('lucide-shield-check', 'size-6 text-success')
                </div>
                <div>
                    <p class="text-sm text-content-muted">Permisos</p>
                    <p class="text-lg font-semibold text-content">
                        {{ auth()->user()->getAllPermissions()->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-line bg-panel p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
                    @svg('lucide-bell', 'size-6 text-warning')
                </div>
                <div>
                    <p class="text-sm text-content-muted">Entorno</p>
                    <p class="text-lg font-semibold text-content">
                        {{ ucfirst(app()->environment()) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
