<x-layouts.app icon="lucide-shield-check" parent="Personal" title="Roles">
    {{ Breadcrumbs::render('personal.roles.index') }}
    @livewire('app.personal.roles.table')
</x-layouts.app>
