# Guía para generar un CRUD

Este documento sirve como instrucciones para que un agente (Claude Code u otro AI)
genere un CRUD completo adaptado a las convenciones de este boilerplate.

---

## Cómo usar este documento con un agente

Dale al agente el siguiente mensaje, reemplazando los valores:

```
Genera un CRUD completo siguiendo docs/crud.md con estos valores:
- Model:     Product          (PascalCase singular)
- model:     product          (camelCase singular)
- models:    products         (snake_case plural)
- model-slug: products        (kebab-case plural, para URLs)
- Domain:    General          (dominio donde vive el módulo)
- domain:    general          (kebab-case del dominio)
- model_es:  producto         (nombre en español singular)
- models_es: productos        (nombre en español plural)
- Fields:    name (string), price (decimal)  (campos del modelo)
```

---

## Variables de referencia

| Variable | Descripción | Ejemplo |
|---|---|---|
| `{Model}` | PascalCase singular | `Product` |
| `{model}` | camelCase singular | `product` |
| `{models}` | snake_case plural | `products` |
| `{model-slug}` | kebab-case plural (URLs) | `products` |
| `{Domain}` | PascalCase dominio | `General` |
| `{domain}` | kebab-case dominio | `general` |
| `{model_es}` | Español singular | `producto` |
| `{Model_es}` | Español singular mayúscula | `Producto` |
| `{models_es}` | Español plural | `productos` |
| `{Models_es}` | Español plural mayúscula | `Productos` |
| `{Fields}` | Campos del modelo | `name (string), price (decimal)` |

---

## Paso 1 — Artisan commands

```bash
php artisan make:model {Model} -mf
php artisan livewire:form {Model}Form
php artisan livewire:make App/{Domain}/{Model}/Form
php artisan powergrid:create {Model}/Table --model={Model}
```

> `powergrid:create` genera en `app/Livewire/{Model}/Table.php` — moverlo manualmente
> a `app/Livewire/App/{Domain}/{Model}/Table.php` y ajustar namespace.

---

## Paso 2 — Modelo `app/Models/{Model}.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {Model} extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['{field1}', '{field2}'];
}
```

---

## Paso 3 — Migración

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
            // ... campos adicionales
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

## Paso 4 — Factory `database/factories/{Model}Factory.php`

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

## Paso 5 — Form `app/Livewire/Forms/{Model}Form.php`

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

## Paso 6 — Livewire FormComponent `app/Livewire/App/{Domain}/{Model}/Form.php`

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
            ->success(
                'Éxito',
                $isEdit ? '{Model_es} actualizado correctamente.' : '{Model_es} creado correctamente.'
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

## Paso 7 — Livewire Table `app/Livewire/App/{Domain}/{Model}/Table.php`

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

## Paso 8 — Rutas en `routes/{domain}.php`

Añadir dentro del grupo de rutas del dominio:

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
        ->get('/{model}/edit', fn ({Model} ${model}) => view('app.{domain}.{model-slug}.edit', ['{model}' => ${model}]))
        ->name('edit');
});
```

---

## Paso 9 — Vistas

### `resources/views/app/{domain}/{model-slug}/index.blade.php` (wrapper)

```blade
<x-layouts.app>
    @livewire('app.{domain}.{model-slug}.table')
</x-layouts.app>
```

### `resources/views/app/{domain}/{model-slug}/create.blade.php` (wrapper)

```blade
<x-layouts.app>
    @livewire('app.{domain}.{model-slug}.form')
</x-layouts.app>
```

### `resources/views/app/{domain}/{model-slug}/edit.blade.php` (wrapper)

```blade
<x-layouts.app>
    @livewire('app.{domain}.{model-slug}.form', ['record' => ${model}])
</x-layouts.app>
```

### `resources/views/app/{domain}/{model-slug}/_form.blade.php` (componente)

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

### `resources/views/app/{domain}/{model-slug}/_toolbar.blade.php` (botón crear)

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

## Paso 10 — Breadcrumbs en `routes/breadcrumbs.php`

Si el archivo no existe, crearlo:

```php
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});
```

Añadir las entradas del módulo:

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

Registrar el archivo en `bootstrap/app.php` si no está ya incluido:

```php
->withRouting(
    // ...
    then: function () {
        require base_path('routes/breadcrumbs.php');
        // ...
    },
)
```

---

## Paso 11 — Menú en `config/menu.php`

```php
[
    'label'        => '{Models_es}',
    'route'        => '{domain}.{model-slug}.index',
    'active_route' => '{domain}.{model-slug}.*',
    'permission'   => 'ver {model_es}',
],
```

---

## Paso 12 — Permisos en `config/permission.php` (o seeder)

Si el proyecto tiene un seeder o config de permisos, añadir:

```php
'ver {model_es}',
'crear {model_es}',
'editar {model_es}',
'eliminar {model_es}',
'restaurar {model_es}',
```

---

## Paso 13 — Commit final

```
feat: CRUD {Models_es}

- Modelo `{Model}` con SoftDeletes y factory
- Migración de tabla `{models}` con campos: {Fields}
- Form `{Model}Form` con validación y updateOrCreate
- Componente `Form` (create/edit) con toast de confirmación
- Tabla PowerGrid `Table` con búsqueda, sort, soft delete y restore
- Vistas: index (wrapper), create (wrapper), edit (wrapper), _form (componente), _toolbar
- Rutas protegidas por permiso en routes/{domain}.php
- Breadcrumbs para el flujo completo
- Entrada en config/menu.php
- Permisos registrados: ver, crear, editar, eliminar, restaurar {model_es}
```

---

## Estructura final de archivos

```
app/
├── Livewire/
│   ├── App/{Domain}/{Model}/
│   │   ├── Form.php          ← componente create/edit
│   │   └── Table.php         ← tabla PowerGrid
│   └── Forms/
│       └── {Model}Form.php   ← Livewire Form object
├── Models/
│   └── {Model}.php
└── database/
    ├── factories/{Model}Factory.php
    └── migrations/xxxx_create_{models}_table.php

resources/views/app/{domain}/{model-slug}/
├── index.blade.php       ← wrapper lista
├── create.blade.php      ← wrapper crear
├── edit.blade.php        ← wrapper editar
├── _form.blade.php       ← componente formulario
└── _toolbar.blade.php    ← botón "Nuevo" del header de tabla

routes/{domain}.php       ← rutas del módulo
routes/breadcrumbs.php    ← breadcrumbs (crear si no existe)
config/menu.php           ← entrada en el menú
```
