<x-layouts.app fixed icon="lucide-users" parent="Accesos" title="Usuarios">
    {{ Breadcrumbs::render('access.users.edit', $user) }}
    @livewire('access::users.form', ['record' => $user])
</x-layouts.app>
