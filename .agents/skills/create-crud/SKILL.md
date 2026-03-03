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

## Hard rules — never break these

- **SoftDeletes is mandatory** on every model. Always add `use SoftDeletes` and `$table->softDeletes()`.
- **Never use `forceDelete`**. No button, route, or method should call `forceDelete()`.
- The Table always includes trashed records (`withTrashed()`) and shows Restaurar for them.
- The only way to permanently remove a record is via a manual DB operation outside the app.
- Tables are **native Livewire components** — no PowerGrid or external table library.

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
| `{model_es}` | Spanish plural (used in permissions) | `productos` |
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
7. Livewire Table component (native)
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
ddev exec php artisan livewire:make App/{Domain}/{Model}/Form --no-view
ddev exec php artisan livewire:make App/{Domain}/{Model}/Table --no-view
```

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

## Step 7 — Livewire Table component `app/Livewire/App/{Domain}/{Model}/Table.php`

```php
<?php

namespace App\Livewire\App\{Domain}\{Model};

use App\Models\{Model};
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use Interactions, WithPagination;

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function softDelete(int $id): void
    {
        $this->authorize('eliminar {model_es}');

        {Model}::find($id)?->delete();

        $this->toast()->success('Éxito', '{Model_es} eliminado correctamente.')->send();
    }

    public function restore(int $id): void
    {
        $this->authorize('restaurar {model_es}');

        {Model}::withTrashed()->find($id)?->restore();

        $this->toast()->success('Éxito', '{Model_es} restaurado correctamente.')->send();
    }

    public function render()
    {
        ${models} = {Model}::withTrashed()
            ->when($this->search, fn ($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);

        return view('app.{domain}.{model-slug}._index', compact('{models}'));
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

### `resources/views/app/{domain}/{model-slug}/_index.blade.php`
```blade
<div>
    {{-- Toolbar --}}
    <div class="mb-4 flex items-center justify-between gap-4">
        <x-ts-input
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar {models_es}..."
            class="w-full max-w-sm"
        />

        @can('crear {model_es}')
            <a href="{{ route('{domain}.{model-slug}.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                <x-ui.icon name="plus" class="size-4" />
                Nuevo {model_es}
            </a>
        @endcan
    </div>

    {{-- Tabla --}}
    <x-ts-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left dark:border-white/10">
                        <th class="pb-3 pr-4 font-medium text-content-muted">
                            <button wire:click="sort('name')" class="flex items-center gap-1 hover:text-content">
                                Nombre
                                @if ($sortField === 'name')
                                    <x-ui.icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                @endif
                            </button>
                        </th>
                        <th class="pb-3 font-medium text-content-muted">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse (${models} as ${model})
                        <tr class="{{ ${model}->trashed() ? 'opacity-50' : '' }}">
                            <td class="py-3 pr-4 font-medium text-content">{{ ${model}->name }}</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    @if (! ${model}->trashed())
                                        @can('editar {model_es}')
                                            <a href="{{ route('{domain}.{model-slug}.edit', ${model}) }}" wire:navigate
                                               class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                                                Editar
                                            </a>
                                        @endcan
                                    @endif

                                    @if (${model}->trashed())
                                        @can('restaurar {model_es}')
                                            <button wire:click="restore({{ ${model}->id }})"
                                                    wire:confirm="¿Restaurar este {model_es}?"
                                                    class="text-sm text-green-600 hover:underline dark:text-green-400">
                                                Restaurar
                                            </button>
                                        @endcan
                                    @else
                                        @can('eliminar {model_es}')
                                            <button wire:click="softDelete({{ ${model}->id }})"
                                                    wire:confirm="¿Eliminar este {model_es}?"
                                                    class="text-sm text-red-600 hover:underline dark:text-red-400">
                                                Eliminar
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-8 text-center text-content-muted">
                                No se encontraron {models_es}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (${models}->hasPages())
            <x-slot:footer>
                {{ ${models}->links() }}
            </x-slot:footer>
        @endif
    </x-ts-card>
</div>
```

---

## Step 10 — Breadcrumbs in `routes/breadcrumbs.php`

The file `routes/breadcrumbs.php` is auto-loaded by the ServiceProvider — do NOT require it in `bootstrap/app.php`.

Add the module entries:

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

Breadcrumbs::for('{domain}.{model-slug}.edit', function (BreadcrumbTrail $trail, {Model} ${model}) {
    $trail->parent('{domain}.{model-slug}.index');
    $trail->push(${model}->name, route('{domain}.{model-slug}.edit', ${model}));
});
```

If `routes/breadcrumbs.php` does not exist yet, create it with the dashboard root first:

```php
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('dashboard'));
});
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

Add to `config/roles.php` under `permissions`:

```php
'{model_es}' => [
    'ver {model_es}',
    'crear {model_es}',
    'editar {model_es}',
    'eliminar {model_es}',
    'restaurar {model_es}',
],
```

Then seed: `ddev exec php artisan db:seed --class=RolesAndPermissionsSeeder`

---

## Step 13 — Commit

```
feat: CRUD {Models_es}

- Modelo `{Model}` con SoftDeletes y factory
- Migración tabla `{models}` con campos: {Fields}
- Form object `{Model}Form` con validación y updateOrCreate
- Componente `Form` (create/edit) con toast de confirmación
- Tabla Livewire nativa `Table` con búsqueda, sort, soft delete y restore
- Vistas: index, create, edit (wrappers) + _form, _index (componentes)
- Rutas protegidas por permiso en routes/{domain}.php
- Breadcrumbs para el flujo completo
- Entrada en config/menu.php
- Permisos: ver, crear, editar, eliminar, restaurar {model_es}
```
