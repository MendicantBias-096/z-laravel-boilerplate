<x-layouts.app fixed icon="lucide-shield-check" parent="Accesos" title="Roles">
    {{ Breadcrumbs::render('access.roles.edit', $role) }}
    @livewire('access::roles.form', ['record' => $role])
</x-layouts.app>
