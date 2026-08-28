<x-layouts.app icon="lucide-users" parent="Accesos" title="Usuarios">
    {{ Breadcrumbs::render('access.users.create') }}
    @livewire('access::users.form')
</x-layouts.app>
