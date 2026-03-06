<x-layouts.app icon="lucide-users" parent="Personal" title="Usuarios">
    {{ Breadcrumbs::render('personal.usuarios.edit', $user) }}
    @livewire('app.personal.user.form', ['record' => $user])
</x-layouts.app>
