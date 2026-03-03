<?php

namespace App\Livewire\App\Personal\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Footer;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use TallStackUi\Traits\Interactions;

final class Table extends PowerGridComponent
{
    use Interactions;

    public string $tableName = 'usuarios-table';

    public function setUp(): array
    {
        return [
            (new Header())
                ->showSearchInput()
                ->includeViewOnTop('app.personal.users._toolbar'),
            (new Footer())
                ->showPerPage(25, [25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->withTrashed()->with('roles');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('role', fn (User $user) => $user->roles->first()?->name ?? '—')
            ->add('deleted_at');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable()->hidden(),
            Column::make('Nombre', 'name')->searchable()->sortable(),
            Column::make('Correo', 'email')->searchable()->sortable(),
            Column::make('Rol', 'role'),
            Column::action('Acciones'),
        ];
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->route('personal.usuarios.edit', ['user' => $row->id])
                ->can(auth()->user()?->can('editar usuarios') && ! $row->trashed())
                ->class('pg-btn-white dark:ring-pg-primary-600'),

            Button::add('destroy')
                ->slot($row->trashed() ? 'Restaurar' : 'Eliminar')
                ->dispatch($row->trashed() ? 'restore' : 'softDelete', ['id' => $row->id])
                ->can(auth()->user()?->can($row->trashed() ? 'restaurar usuarios' : 'eliminar usuarios'))
                ->class('pg-btn-white dark:ring-pg-primary-600'),
        ];
    }

    #[On('softDelete')]
    public function softDelete(int $id): void
    {
        User::find($id)?->delete();
        $this->notification()->success('Éxito', 'Usuario eliminado correctamente.');
    }

    #[On('restore')]
    public function restore(int $id): void
    {
        User::withTrashed()->find($id)?->restore();
        $this->notification()->success('Éxito', 'Usuario restaurado correctamente.');
    }
}
