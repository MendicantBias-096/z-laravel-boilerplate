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
- Tables are **native Livewire components** using `x-ts-table` from TallStackUI with native Livewire state.
- The `x-ts-table` is used **without** built-in filter/quantity/paginate props — all handled manually.
- Every floating panel (dropdowns, selects inside tables) must use `x-teleport="#app-root"` + `position:fixed` to escape overflow clipping.
- **Card footers** (where save/cancel buttons live) have a subtle primary-brand tint applied globally via `AppServiceProvider`. Never override this background manually — just use `<x-slot:footer>` normally.
- **Row action ids are typed `string|int`, never `int`**, and reach the browser through `Js::from()`, never interpolated bare. Integer keys satisfy both by accident; a UUID or ULID key breaks on both — see «Model keys» in `CLAUDE.md`. The stubs below already comply.

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
| `{Domain_es}` | Spanish domain name capitalized | `Personal` |
| `{model_es}` | Spanish plural (used in permissions) | `productos` |
| `{Model_es}` | Spanish singular capitalized | `Producto` |
| `{models_es}` | Spanish plural | `productos` |
| `{Models_es}` | Spanish plural capitalized | `Productos` |
| `{icon}` | Lucide icon name for the module | `package` |
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
            // La clave: `$table->id()`, o `$table->uuid('id')->primary()` con
            // `HasUuids`, o `$table->ulid('id')->primary()` con `HasUlids`. Los
            // tres funcionan. Cuál es el default para módulos nuevos está por
            // decidir (ZBLP-10); si eliges una de texto, el Form object de más
            // abajo tipa `?string $id`.
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
    // `?string` si el modelo lleva clave UUID o ULID.
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

The table uses native Livewire state for search, sort, quantity, and filters.
Add filter properties only for the fields the module requires.

```php
<?php

namespace App\Livewire\App\{Domain}\{Model};

use App\Models\{Model};
use App\Traits\Livewire\HasSoftDeletes;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use Interactions, WithPagination, HasSoftDeletes;

    protected string $modelClass = {Model}::class;
    protected string $deletePermission = 'eliminar {model_es}';
    protected string $restorePermission = 'restaurar {model_es}';
    protected string $modelLabel = '{Model_es}';

    public string $search = '';
    // Add filter properties as needed:
    // public string $filterName = '';

    public int $quantity = 25;

    public array $sort = ['column' => 'name', 'direction' => 'asc'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    // Add updatingFilter* methods for each filter:
    // public function updatingFilterName(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        // $this->reset('filterName');
        $this->resetPage();
    }

    public function render()
    {
        $headers = [
            ['index' => 'name',   'label' => 'Nombre'],
            ['index' => 'status', 'label' => 'Estado',   'sortable' => false],
            ['index' => 'action', 'label' => 'Acciones', 'sortable' => false],
        ];

        ${models} = {Model}::withTrashed()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->search}%");
            }))
            // ->when($this->filterName, fn ($q) => $q->where('name', 'ilike', "%{$this->filterName}%"))
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity);

        ${models}->getCollection()->transform(function ({Model} $item) {
            $item->status = $item->trashed() ? 'Eliminado' : 'Activo';
            return $item;
        });

        // Pass filter option arrays as needed:
        // $options = SomeModel::orderBy('name')->pluck('name', 'name')->toArray();

        return view('app.{domain}.{model-slug}._index', compact('headers', '{models}'));
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

### Wrapper views

All wrappers pass `icon`, `parent` (domain label), and `title` to the layout.

#### `resources/views/app/{domain}/{model-slug}/index.blade.php`
```blade
<x-layouts.app icon="{icon}" parent="{Domain_es}" title="{Models_es}">
    {{ Breadcrumbs::render('{domain}.{model-slug}.index') }}
    @livewire('app.{domain}.{model-slug}.table')
</x-layouts.app>
```

#### `resources/views/app/{domain}/{model-slug}/create.blade.php`
```blade
<x-layouts.app icon="{icon}" parent="{Domain_es}" title="Nuevo {model_es}">
    {{ Breadcrumbs::render('{domain}.{model-slug}.create') }}
    @livewire('app.{domain}.{model-slug}.form')
</x-layouts.app>
```

#### `resources/views/app/{domain}/{model-slug}/edit.blade.php`
```blade
<x-layouts.app icon="{icon}" parent="{Domain_es}" title="Editar {model_es}">
    {{ Breadcrumbs::render('{domain}.{model-slug}.edit', ${model}) }}
    @livewire('app.{domain}.{model-slug}.form', ['record' => ${model}])
</x-layouts.app>
```

---

### `resources/views/app/{domain}/{model-slug}/_form.blade.php`
```blade
<div>
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

---

### `resources/views/app/{domain}/{model-slug}/_index.blade.php`

This is the main table view. It follows this layout:
1. **Top bar**: search input | filter toggle button | spacer | action buttons
2. **Filter panel**: collapsible, with header showing active filter count + limpiar button
3. **Table**: `x-ts-table` without built-in filter/quantity/paginate
4. **Footer**: quantity selector (left) + paginator (right)

```blade
<div x-data="{ showFilters: false }">

    {{-- ── Barra superior ──────────────────────────────────────────────── --}}
    <div class="mb-3 flex items-center gap-2">

        {{-- Búsqueda --}}
        <div class="relative w-64">
            <x-ui.icon name="search"
                class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-content-subtle" />
            <input
                wire:model.live.debounce.400ms="search"
                type="search"
                placeholder="Buscar..."
                class="w-full rounded-lg border border-line bg-panel py-2 pl-9 pr-4 text-sm text-content placeholder-content-subtle focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:bg-panel"
            />
        </div>

        {{-- Botón filtros (mostrar solo si hay filtros definidos) --}}
        <button
            type="button"
            @click="showFilters = !showFilters"
            :class="showFilters || @js($filterName)
                ? 'border-primary-500 bg-primary-50 text-primary-600 dark:bg-primary-950 dark:text-primary-400'
                : 'border-line bg-panel text-content-muted hover:bg-panel-alt hover:text-content dark:bg-panel'"
            class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-3 py-2 text-sm transition-colors"
        >
            <x-ui.icon name="sliders-horizontal" class="size-4" />
            Filtros
            @if ($filterName)
                <span class="flex size-2 rounded-full bg-primary-500"></span>
            @endif
        </button>

        {{-- Separador --}}
        <div class="flex-1"></div>

        {{-- Botón nueva acción --}}
        @can('crear {model_es}')
            <a href="{{ route('{domain}.{model-slug}.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                <x-ui.icon name="plus" class="size-4" />
                Nuevo {model_es}
            </a>
        @endcan
    </div>

    {{-- ── Panel de filtros ────────────────────────────────────────────── --}}
    <div
        x-show="showFilters"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="mb-4 overflow-hidden rounded-lg border border-line bg-panel shadow-sm"
    >
        {{-- Cabecera del panel --}}
        <div class="flex items-center justify-between border-b border-line bg-panel-alt px-4 py-2.5">
            <div class="flex items-center gap-2 text-sm font-medium text-content-muted">
                <x-ui.icon name="sliders-horizontal" class="size-3.5" />
                Filtros
            </div>
            @php $activeCount = (int) (bool) $filterName; @endphp
            @if ($activeCount)
                <span class="inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                    {{ $activeCount }} {{ Str::plural('activo', $activeCount) }}
                </span>
            @endif
        </div>

        {{-- Campos --}}
        <div class="flex flex-wrap items-end gap-3 p-4">

            <x-ui.ts-table.filter-input
                label="Nombre"
                icon="search"
                wire:model.live.debounce.400ms="filterName"
                placeholder="Filtrar por nombre..."
            />

            {{-- Para filtros de selección (ej: estado, rol, categoría): --}}
            {{-- <x-ui.ts-table.filter-select
                label="Estado"
                wire:model.live="filterStatus"
                placeholder="Todos"
                :options="$statusOptions"
            /> --}}

            @if ($filterName)
                <div class="group relative self-end">
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="flex cursor-pointer items-center justify-center rounded-md border border-red-200 bg-red-50 p-2 text-red-600 transition-colors hover:border-red-300 hover:bg-red-100 dark:border-red-800 dark:bg-red-950 dark:text-red-400 dark:hover:border-red-700 dark:hover:bg-red-900"
                    >
                        <x-ui.icon name="rotate-ccw" class="size-3.5" />
                    </button>
                    <div class="pointer-events-none absolute bottom-full left-1/2 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-2 py-1 text-xs text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100 dark:bg-dark-600">
                        Limpiar filtros
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Tabla ────────────────────────────────────────────────────────── --}}
    <x-ts-table
        :headers="$headers"
        :rows="${models}"
        :sort="$sort"
        striped
    >
        @interact('column_status', $row)
            @if ($row->trashed())
                <x-ts-badge text="Eliminado" color="red" />
            @else
                <x-ts-badge text="Activo" color="green" />
            @endif
        @endinteract

        @interact('column_action', $row)
            <x-ui.ts-table.actions
                :row="$row"
                edit-route="{domain}.{model-slug}.edit"
                edit-permission="editar {model_es}"
                delete-permission="eliminar {model_es}"
                restore-permission="restaurar {model_es}"
                model="{model_es}"
            />
        @endinteract
    </x-ts-table>

    {{-- ── Footer: conteo + paginador ──────────────────────────────────── --}}
    @if (${models}->total() > 0)
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">

            {{-- Selector de cantidad --}}
            <div class="flex items-center gap-1.5 text-sm text-content-muted">
                Mostrando
                <select
                    wire:model.live="quantity"
                    class="rounded-md border border-line bg-panel px-2 py-1 text-sm text-content focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                resultados de {{ ${models}->total() }}
            </div>

            {{-- Paginador --}}
            @if (${models}->hasPages())
                {{ ${models}->links('tallstack-ui::components.table.paginators') }}
            @endif

        </div>
    @endif

</div>
```

---

## Filter components reference

These reusable components live in `resources/views/components/ui/ts-table/`.

### `x-ui.ts-table.filter-input`

Text filter with optional leading icon.

```blade
<x-ui.ts-table.filter-input
    label="Correo"
    icon="search"
    wire:model.live.debounce.400ms="filterEmail"
    placeholder="Filtrar por correo..."
/>
```

Props: `label`, `placeholder` (default `''`), `icon` (optional, Lucide name).

### `x-ui.ts-table.filter-select`

Custom select with search, teleported panel (escapes overflow), and `$wire.entangle` binding.

```blade
<x-ui.ts-table.filter-select
    label="Rol"
    wire:model.live="filterRole"
    placeholder="Todos los roles"
    :options="$roles"
/>
```

Props: `label`, `placeholder` (default `'Todos'`), `options` (associative array `[value => label]`).

The `options` array is typically built in the Livewire component:
```php
$roles = Role::orderBy('name')->pluck('name', 'name')->toArray();
```

### `x-ui.ts-table.actions`

Dropdown action menu (edit / restore / delete) with dialog confirmation, teleported panel.

```blade
<x-ui.ts-table.actions
    :row="$row"
    edit-route="{domain}.{model-slug}.edit"
    edit-permission="editar {model_es}"
    delete-permission="eliminar {model_es}"
    restore-permission="restaurar {model_es}"
    model="{model_es}"
/>
```

The Livewire Table component must use `HasSoftDeletes` trait which provides `softDelete()`, `restore()`, `confirmDelete()`, and `confirmRestore()`.

---

## Step 10 — Breadcrumbs in `routes/breadcrumbs.php`

The domain level is a **non-linked label** (no route). `dashboard` is NOT a parent for domain modules.

```php
// {models_es}
Breadcrumbs::for('{domain}.{model-slug}.index', function (BreadcrumbTrail $trail) {
    $trail->push('{Domain_es}');
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

If `routes/breadcrumbs.php` does not exist yet, create it:

```php
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
```

---

## Step 11 — Menu entry in `config/menu.php`

```php
[
    'label'        => '{Models_es}',
    'icon'         => '{icon}',
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
- Tabla nativa Livewire `Table` con búsqueda, filtros, sort, paginación, soft delete y restore
- Vistas: index, create, edit (wrappers con icon/parent/title) + _form, _index
- Panel de filtros colapsable con filter-input y filter-select
- Footer con selector de cantidad y paginador TallStackUI
- Rutas protegidas por permiso en routes/{domain}.php
- Breadcrumbs con dominio como etiqueta no enlazada
- Entrada en config/menu.php
- Permisos: ver, crear, editar, eliminar, restaurar {model_es}
```
