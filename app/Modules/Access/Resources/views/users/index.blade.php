<x-layouts.app icon="lucide-users" parent="Accesos" title="Usuarios">
    {{ Breadcrumbs::render('access.users.index') }}
    @livewire('access::users.table')
</x-layouts.app>
