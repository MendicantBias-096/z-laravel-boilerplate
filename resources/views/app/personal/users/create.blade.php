<x-layouts.app icon="users" parent="Personal" title="Usuarios">
    {{ Breadcrumbs::render('personal.usuarios.create') }}
    @livewire('app.personal.user.form')
</x-layouts.app>
