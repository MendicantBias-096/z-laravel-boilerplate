<x-layouts.app icon="lucide-shield-check" parent="Accesos" title="Roles">
    {{ Breadcrumbs::render('access.roles.index') }}
    @livewire('access::roles.table')
</x-layouts.app>
