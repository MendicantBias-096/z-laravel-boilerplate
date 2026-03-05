<x-layouts.app icon="shield-check" parent="Personal" title="Roles">
    {{ Breadcrumbs::render('personal.roles.create') }}
    @livewire('app.personal.roles.form')
</x-layouts.app>
