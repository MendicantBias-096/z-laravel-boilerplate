<x-layouts.app icon="lucide-shield-check" parent="Accesos" title="Roles">
    {{ Breadcrumbs::render('access.roles.create') }}
    @livewire('access::roles.form')
</x-layouts.app>
