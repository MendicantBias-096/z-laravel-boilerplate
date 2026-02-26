---
name: powergrid-tables
description: >-
  Builds PowerGrid v6 data tables with Livewire 4. Activates when creating or modifying
  tables, adding columns, filters, actions, bulk operations, soft delete/restore actions,
  or when the user mentions tabla, datatable, PowerGrid, listing, or grid.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to Apply

Activate this skill when:
- Creating a new data table
- Adding columns, filters, or actions to an existing table
- Implementing soft delete / restore in a table
- Adding bulk actions or export

---

## Full Table Template

```php
<?php

namespace App\Livewire\App\{Domain}\{Model};

use App\Models\{Model};
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use TallStackUi\Traits\Interactions;

final class Table extends PowerGridComponent
{
    use Interactions;

    public string $tableName = '{model-slug}-table';

    public function setUp(): array
    {
        return [
            Header::make()
                ->showSearchInput()
                ->includeViewOnTop('app.{domain}.{model-slug}._toolbar'),
            Footer::make()
                ->showPerPage(25, [25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return {Model}::query()->withTrashed();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('deleted_at');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable()->hidden(),
            Column::make('Nombre', 'name')->searchable()->sortable(),
            Column::action('Acciones'),
        ];
    }

    public function actions({Model} $row): array
    {
        return [
            Button::add('edit')
                ->slot('Editar')
                ->route('{domain}.{model-slug}.edit', ['{model}' => $row->id])
                ->can(auth()->user()?->can('editar {model_es}'))
                ->class('pg-btn-white dark:ring-pg-primary-600'),

            Button::add('destroy')
                ->slot($row->trashed() ? 'Restaurar' : 'Eliminar')
                ->dispatch($row->trashed() ? 'restore' : 'softDelete', ['id' => $row->id])
                ->can(auth()->user()?->can($row->trashed() ? 'restaurar {model_es}' : 'eliminar {model_es}'))
                ->class('pg-btn-white dark:ring-pg-primary-600'),
        ];
    }

    #[On('softDelete')]
    public function softDelete(int $id): void
    {
        {Model}::find($id)?->delete();
        $this->notification()->success('Éxito', '{Model_es} eliminado correctamente.');
    }

    #[On('restore')]
    public function restore(int $id): void
    {
        {Model}::withTrashed()->find($id)?->restore();
        $this->notification()->success('Éxito', '{Model_es} restaurado correctamente.');
    }
}
```

---

## Column Types

```php
Column::make('Label', 'field')->sortable()
Column::make('Label', 'field')->searchable()->sortable()
Column::make('Label', 'field')->hidden()             // hidden but available for search/sort
Column::make('Fecha', 'created_at')->sortable()
Column::action('Acciones')                           // actions column
```

---

## Toolbar View `_toolbar.blade.php`

```blade
<div>
    @can('crear {model_es}')
        <a href="{{ route('{domain}.{model-slug}.create') }}" wire:navigate
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
           style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
            <x-ui.icon name="plus" class="size-4" />
            Nuevo {model_es}
        </a>
    @endcan
</div>
```

---

## Rules

- Always use `final class` for PowerGrid components.
- Always use `withTrashed()` in datasource if the model uses SoftDeletes.
- Use `#[On('eventName')]` attribute for event listeners (Livewire 4 syntax).
- `public string $tableName` must be unique across all tables in the app.
- Use `includeViewOnTop()` in Header for toolbar/action buttons above the table.
- Use `$this->notification()` (not `$this->toast()`) for in-table feedback.
