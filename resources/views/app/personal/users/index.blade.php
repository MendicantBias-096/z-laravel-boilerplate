<x-layouts.app icon="users" parent="Personal" title="Usuarios">
    {{ Breadcrumbs::render('personal.usuarios.index') }}
    @livewire('app.personal.user.table')
</x-layouts.app>
