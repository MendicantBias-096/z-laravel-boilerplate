<x-layouts.app icon="shield-check" parent="Personal" title="Roles">
    {{ Breadcrumbs::render('personal.roles.index') }}
    @livewire('app.personal.roles.table')
</x-layouts.app>
