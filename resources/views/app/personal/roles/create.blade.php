<x-layouts.app icon="lucide-shield-check" parent="Personal" title="Roles">
    {{ Breadcrumbs::render('personal.roles.create') }}
    @livewire('app.personal.roles.form')
</x-layouts.app>
