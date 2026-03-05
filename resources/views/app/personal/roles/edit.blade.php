<x-layouts.app icon="shield-check" parent="Personal" title="Roles">
    {{ Breadcrumbs::render('personal.roles.edit', $role) }}
    @livewire('app.personal.roles.form', ['record' => $role])
</x-layouts.app>
