---
name: create-crud
description: Generates a complete CRUD module following the boilerplate conventions.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to activate

Activate when the user asks to create a new module with listing, create and edit.
Trigger phrases: "crear crud", "nuevo módulo con tabla", "generar módulo", "create crud".

---

## Required variables — ask if missing

| Variable | Description | Example |
|---|---|---|
| `{Model}` | PascalCase singular | `Product` |
| `{model}` | camelCase singular | `product` |
| `{models}` | snake_case plural | `products` |
| `{model-slug}` | kebab-case plural (URLs) | `products` |
| `{Domain}` | PascalCase domain | `General` |
| `{domain}` | lowercase domain | `general` |
| `{model_es}` | Spanish singular | `producto` |
| `{Model_es}` | Spanish singular capitalized | `Producto` |
| `{models_es}` | Spanish plural | `productos` |
| `{Models_es}` | Spanish plural capitalized | `Productos` |
| `{Fields}` | Fields with types | `name string, price decimal` |

---

## Execution plan

Create a task list before writing any file. Mark each step as completed.

1. Artisan commands
2. Model
3. Migration
4. Factory
5. Livewire Form object
6. Livewire Form component
7. Livewire PowerGrid Table
8. Routes
9. Views (5 files)
10. Breadcrumbs
11. Menu entry
12. Permissions note
13. Commit

---

## Step 1 — Artisan commands

```bash
ddev exec php artisan make:model {Model} -mf
ddev exec php artisan livewire:form {Model}Form
ddev exec php artisan livewire:make App/{Domain}/{Model}/Form
ddev exec php artisan powergrid:create {Model}/Table --model={Model}
```

Move the PowerGrid generated file from `app/Livewire/{Model}/Table.php`
to `app/Livewire/App/{Domain}/{Model}/Table.php` and fix its namespace.

---

## Step 2 — Model `app/Models/{Model}.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {Model} extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];
}
```

---

## Step 3 — Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{models}', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{models}');
    }
};
```

---

## Step 4 — Factory `database/factories/{Model}Factory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class {Model}Factory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
        ];
    }
}
```

---

## Step 5 — Form object `app/Livewire/Forms/{Model}Form.php`

```php
<?php

namespace App\Livewire\Forms;

use App\Models\{Model};
use Livewire\Form;

class {Model}Form extends Form
{
    public ?int $id = null;

    public string $name = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function store(): {Model}
    {
        return {Model}::updateOrCreate(
            ['id' => $this->id],
            $this->only('name'),
        );
    }
}
```

---

## Step 6 — Livewire Form component `app/Livewire/App/{Domain}/{Model}/Form.php`

```php
<?php

namespace App\Livewire\App\{Domain}\{Model};

use App\Livewire\Forms\{Model}Form;
use App\Models\{Model};
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions;

    public ?{Model} $record = null;

    public {Model}Form $form;

    public function mount(): void
    {
        if ($this->record) {
            $this->form->fill([
                'id'   => $this->record->id,
                'name' => $this->record->name,
            ]);
        }
    }

    public function save(): void
    {
        $this->form->validate();

        $isEdit = $this->form->id !== null;

        $this->form->store();

        $this->toast()
            ->success('Éxito', $isEdit
                ? '{Model_es} actualizado correctamente.'
                : '{Model_es} creado correctamente.'
            )
            ->flash()
            ->send();

        $this->redirect(route('{domain}.{model-slug}.index'), navigate: true);
    }

    public function render()
    {
        return view('app.{domain}.{model-slug}._form');
    }
}
```

---

## Step 7 — PowerGrid Table `app/Livewire/App/{Domain}/{Model}/Table.php`

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

## Step 8 — Routes in `routes/{domain}.php`

Add inside the domain route group:

```php
use App\Models\{Model};

Route::prefix('{model-slug}')->name('{model-slug}.')->group(function () {

    Route::middleware('permission:ver {model_es}')
        ->get('/', fn () => view('app.{domain}.{model-slug}.index'))
        ->name('index');

    Route::middleware('permission:crear {model_es}')
        ->get('/create', fn () => view('app.{domain}.{model-slug}.create'))
        ->name('create');

    Route::middleware('permission:editar {model_es}')
        ->get('/{model}/edit', fn ({Model} ${model}) => view(
            'app.{domain}.{model-slug}.edit',
            ['{model}' => ${model}]
        ))
        ->name('edit');
});
```

---

## Step 9 — Views (5 files)

### `resources/views/app/{domain}/{model-slug}/index.blade.php`
```blade
<x-layouts.app>
    @livewire('app.{domain}.{model-slug}.table')
</x-layouts.app>
```

### `resources/views/app/{domain}/{model-slug}/create.blade.php`
```blade
<x-layouts.app>
    @livewire('app.{domain}.{model-slug}.form')
</x-layouts.app>
```

### `resources/views/app/{domain}/{model-slug}/edit.blade.php`
```blade
<x-layouts.app>
    @livewire('app.{domain}.{model-slug}.form', ['record' => ${model}])
</x-layouts.app>
```

### `resources/views/app/{domain}/{model-slug}/_form.blade.php`
```blade
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-content">
            {{ $record ? 'Editar {model_es}' : 'Nuevo {model_es}' }}
        </h1>
    </div>

    <form wire:submit="save">
        <x-ts-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <x-ts-input
                    label="Nombre"
                    wire:model="form.name"
                    placeholder="Ingresa un nombre"
                />

            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('{domain}.{model-slug}.index') }}" wire:navigate
                       class="text-sm text-content-muted hover:text-content">
                        Cancelar
                    </a>
                    <x-ts-button type="submit" wire:loading.attr="disabled">
                        Guardar
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
```

### `resources/views/app/{domain}/{model-slug}/_toolbar.blade.php`
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

## Step 10 — Breadcrumbs in `routes/breadcrumbs.php`

If the file doesn't exist, create it with the base dashboard entry first:

```php
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});
```

Then add the module entries:

```php
// {models_es}
Breadcrumbs::for('{domain}.{model-slug}.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('{Models_es}', route('{domain}.{model-slug}.index'));
});

Breadcrumbs::for('{domain}.{model-slug}.create', function (BreadcrumbTrail $trail) {
    $trail->parent('{domain}.{model-slug}.index');
    $trail->push('Nuevo {model_es}', route('{domain}.{model-slug}.create'));
});

Breadcrumbs::for('{domain}.{model-slug}.edit', function (BreadcrumbTrail $trail, ${model}) {
    $trail->parent('{domain}.{model-slug}.index');
    $trail->push(${model}->name, route('{domain}.{model-slug}.edit', ${model}));
});
```

If `routes/breadcrumbs.php` is new, register it in `bootstrap/app.php` inside `then`:

```php
then: function () {
    require base_path('routes/breadcrumbs.php');
    // ... other route files
},
```

---

## Step 11 — Menu entry in `config/menu.php`

```php
[
    'label'        => '{Models_es}',
    'route'        => '{domain}.{model-slug}.index',
    'active_route' => '{domain}.{model-slug}.*',
    'permission'   => 'ver {model_es}',
],
```

---

## Step 12 — Permissions

List the permissions that must be seeded or registered:

```
ver {model_es}
crear {model_es}
editar {model_es}
eliminar {model_es}
restaurar {model_es}
```

Inform the user they need to seed these permissions before the routes become accessible.

---

## Step 13 — Commit

```
feat: CRUD {Models_es}

- Modelo `{Model}` con SoftDeletes y factory
- Migración tabla `{models}` con campos: {Fields}
- Form object `{Model}Form` con validación y updateOrCreate
- Componente `Form` (create/edit) con toast de confirmación
- Tabla PowerGrid `Table` con búsqueda, sort, soft delete y restore
- Vistas: index, create, edit (wrappers) + _form, _toolbar (componentes)
- Rutas protegidas por permiso en routes/{domain}.php
- Breadcrumbs para el flujo completo
- Entrada en config/menu.php
- Permisos: ver, crear, editar, eliminar, restaurar {model_es}
```
