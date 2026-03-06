<x-layouts.app icon="lucide-users" parent="Personal" title="Usuarios">
    {{ Breadcrumbs::render('personal.usuarios.index') }}
    @livewire('app.personal.user.table')
</x-layouts.app>
