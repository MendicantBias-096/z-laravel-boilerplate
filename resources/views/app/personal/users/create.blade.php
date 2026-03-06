<x-layouts.app icon="lucide-users" parent="Personal" title="Usuarios">
    {{ Breadcrumbs::render('personal.usuarios.create') }}
    @livewire('app.personal.user.form')
</x-layouts.app>
