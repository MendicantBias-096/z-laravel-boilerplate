---
name: create-crud
description: Generates a complete CRUD resource inside a module under app/Modules, with listing, create and edit.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to activate

Activate when the user asks for a resource with listing, create and edit.
Trigger phrases: "crear crud", "nuevo módulo con tabla", "generar módulo", "create crud".

A CRUD always lives **inside a module**. If the module does not exist yet, run
`create-module` first — this skill assumes `app/Modules/{Context}/` is already
registered and routable.

The architecture is `docs/ARCHITECTURE_RULES.md` (R1–R58) and the «Modules»
section of `CLAUDE.md`. Cite rules by id when a choice needs defending.

---

## Hard rules — never break these

- **SoftDeletes is mandatory** on every model: `use SoftDeletes` and `$table->softDeletes()`.
- **Never call `forceDelete`.** No button, route or method. Permanent removal is a manual DB operation outside the app.
- The Table includes trashed records (`withTrashed()`) and offers Restaurar for them.
- Tables are native Livewire components using `x-ts-table`, **without** its built-in filter/quantity/paginate props — `HasTable` handles that state.
- Every floating panel (dropdowns, selects inside tables) uses `x-teleport="#app-root"` + `position:fixed` to escape overflow clipping.
- **Card footers** get a brand tint globally from `AppServiceProvider`. Use `<x-slot:footer>` and never override the background.
- **Domain models key on UUID.** `$table->uuid('id')->primary()` + `HasUuids` + `?string $id` in the Form object — the three change together or not at all. Auth and infrastructure tables (`users`, `roles`, `jobs`, `cache`) stay on integers. Rationale in «Model keys» in `CLAUDE.md`.
- **Row action ids are typed `string|int`, never `int`**, and reach the browser through `Js::from()`, never interpolated bare. Integer keys satisfy both by accident, which is why a UUID key is what exposes a mistake. `app/Modules/Access/Tests/Feature/Users/ClaveNoNumericaTest.php` guards both halves.
- **A model inside a module needs `newFactory()`.** `Factory::guessFactoryNamesUsing()` is a single global with no per-module version, so without it Laravel looks in `Database\Factories\` and does not find yours.
- **User-facing text goes through `__()`**, against the module's own lang files. The boilerplate ships `es` and `en`; a hardcoded string breaks the second one silently.

---

## Required variables — ask if missing

| Variable       | Description                                 | Example                      |
| -------------- | ------------------------------------------- | ---------------------------- |
| `{Context}`    | PascalCase module it belongs to             | `Billing`                    |
| `{context}`    | lowercase module                            | `billing`                    |
| `{Context_es}` | Spanish module label                        | `Facturación`                |
| `{Model}`      | PascalCase singular                         | `Invoice`                    |
| `{model}`      | camelCase singular                          | `invoice`                    |
| `{table}`      | snake_case table, **module-prefixed** (R25) | `billing_invoices`           |
| `{resource}`   | kebab-case plural, for URL and views        | `invoices`                   |
| `{Resource}`   | PascalCase plural, the Livewire folder      | `Invoices`                   |
| `{model_es}`   | Spanish plural, used in permissions         | `facturas`                   |
| `{Model_es}`   | Spanish singular capitalized                | `Factura`                    |
| `{Models_es}`  | Spanish plural capitalized                  | `Facturas`                   |
| `{icon}`       | Lucide icon name                            | `receipt`                    |
| `{Fields}`     | Fields with types                           | `name string, total decimal` |

Names derive mechanically from these:

```
class      App\Modules\{Context}\Livewire\{Resource}\Table
view       {context}::{resource}._index
livewire   @livewire('{context}::{resource}.table')
route      {context}.{resource}.index
url        /{context}/{resource}     business module
           /{resource}               platform module
```

---

## Execution plan

Create a task list before writing any file. Mark each step as completed.

1. Model
2. Migration
3. Factory
4. Form object
5. Livewire Form component
6. Livewire Table component
7. Routes
8. Views (5 files)
9. Lang files
10. Breadcrumbs
11. Menu entry
12. Permissions
13. Verify
14. Commit

**Write these files directly.** `make:model` and `livewire:make` have no
`--path`, so they generate into `app/Models` and `app/Livewire` and every file
then has to be moved and its namespace rewritten — more steps and more ways to
get it wrong than writing the file where it belongs.

---

## Step 1 — Model

`app/Modules/{Context}/Models/{Model}.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Models;

use App\Modules\{Context}\Database\Factories\{Model}Factory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * PHPStan no conoce las columnas de Eloquent: sin `@property` cada lectura de
 * `$invoice->name` es un error, en el componente y en los breadcrumbs.
 *
 * @property string $id
 * @property string $name
 */
class {Model} extends Model
{
    /** @use HasFactory<{Model}Factory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $table = '{table}';

    protected $fillable = ['name'];

    /**
     * `Factory::guessFactoryNamesUsing()` es un único global sin versión por
     * módulo: sin esto, Laravel busca en `Database\Factories\` y no encuentra
     * la del módulo.
     */
    protected static function newFactory(): {Model}Factory
    {
        return {Model}Factory::new();
    }
}
```

Register the morph alias in the module's ServiceProvider if the model is ever
the target of a polymorphic column — the FQCN is stored as text, so renaming a
namespace later orphans those rows silently.

## Step 2 — Migration

`app/Modules/{Context}/Database/Migrations/{timestamp}_create_{table}_table.php`

The table name carries the module prefix (R25, R33). Migration **file names must
be unique across every module**: `Migrator::getMigrationFiles()` keys by
basename before sorting, so two modules with the same basename silently drop
one.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{table}', function (Blueprint $table): void {
            // UUID por convención en los módulos de dominio — ver «Model keys»
            // en `CLAUDE.md`. Va con `HasUuids` en el modelo y `?string $id` en
            // el Form object; las tres piezas cambian juntas o ninguna.
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{table}');
    }
};
```

Every migration needs a working `down()` (R34) — `migrate:fresh` is not a
rollback, and `tests/Feature/MigrationsAreReversibleTest.php` exercises it.

## Step 3 — Factory

`app/Modules/{Context}/Database/Factories/{Model}Factory.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Database\Factories;

use App\Modules\{Context}\Models\{Model};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<{Model}>
 */
class {Model}Factory extends Factory
{
    protected $model = {Model}::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
        ];
    }
}
```

## Step 4 — Form object

`app/Modules/{Context}/Livewire/Forms/{Model}Form.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Livewire\Forms;

use App\Modules\{Context}\Models\{Model};
use Livewire\Form;

class {Model}Form extends Form
{
    // `?string` porque la clave es UUID. Con `$table->id()` sería `?int`.
    public ?string $id = null;

    public string $name = '';

    /** @return array<string, list<string>> */
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

A `Rule::exists`/`unique` here may only target this module's own tables (R28).
Validating against another module's table crosses the boundary — ask that
module through its contract instead.

## Step 5 — Livewire Form component

`app/Modules/{Context}/Livewire/{Resource}/Form.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Livewire\{Resource};

use App\Modules\{Context}\Livewire\Forms\{Model}Form;
use App\Modules\{Context}\Models\{Model};
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Form extends Component
{
    use Interactions;

    public ?{Model} $record = null;

    public {Model}Form $form;

    public function mount(): void
    {
        if ($this->record instanceof {Model}) {
            $this->form->fill([
                'id' => $this->record->id,
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
            ->success(__('platform::app.success'), $isEdit
                ? __('platform::app.updated', ['model' => __('{context}::{resource}.singular')])
                : __('platform::app.created', ['model' => __('{context}::{resource}.singular')])
            )
            ->flash()
            ->send();

        $this->redirect(route('{context}.{resource}.index'), navigate: true);
    }

    public function render(): Factory|View
    {
        return view('{context}::{resource}._form');
    }
}
```

## Step 6 — Livewire Table component

`app/Modules/{Context}/Livewire/{Resource}/Table.php`

`HasTable` owns `$search`, `$quantity`, `$sort` and `$filters`, plus the
`updating*` hooks, `sortBy()` and `clearFilters()`. Declare `searchable()`,
`filterable()` and `defaultSort()` and it builds the query — do not re-declare
those properties by hand.

`HasSoftDeletes` provides `confirmDelete()`, `softDelete()`, `confirmRestore()`
and `restore()`, and reads the four `protected` strings below.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Livewire\{Resource};

use App\Modules\{Context}\Models\{Model};
use App\Modules\Platform\Traits\Livewire\HasSoftDeletes;
use App\Modules\Platform\Traits\Livewire\HasTable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Table extends Component
{
    use HasSoftDeletes, HasTable, Interactions, WithPagination;

    protected string $modelClass = {Model}::class;

    protected string $deletePermission = 'eliminar {model_es}';

    protected string $restorePermission = 'restaurar {model_es}';

    protected string $modelLabel = '{Model_es}';

    /** @return list<string> */
    protected function searchable(): array
    {
        return ['name'];
    }

    /** @return array<string, string> columna => operador */
    protected function filterable(): array
    {
        return [];
    }

    /** @return array{column: string, direction: string} */
    protected function defaultSort(): array
    {
        return ['column' => 'name', 'direction' => 'asc'];
    }

    public function render(): Factory|View
    {
        $headers = [
            ['index' => 'name', 'label' => __('{context}::{resource}.headers.name')],
            ['index' => 'status', 'label' => __('platform::table.headers.status'), 'sortable' => false],
            ['index' => 'action', 'label' => __('platform::table.headers.actions'), 'sortable' => false],
        ];

        $query = auth()->user()->can('restaurar {model_es}')
            ? {Model}::withTrashed()
            : {Model}::query();

        ${resource} = $this->applyTableQuery($query)->paginate($this->quantity);

        return view('{context}::{resource}._index', [
            'headers' => $headers,
            '{resource}' => ${resource},
        ]);
    }
}
```

## Step 7 — Routes

Inside the existing group in `app/Modules/{Context}/Routes/web.php`:

```php
use App\Modules\{Context}\Models\{Model};
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

Route::prefix('{resource}')->name('{resource}.')->group(function (): void {

    Route::view('/', '{context}::{resource}.index')
        ->middleware('permission:ver {model_es}')
        ->name('index');

    Route::view('/create', '{context}::{resource}.create')
        ->middleware('permission:crear {model_es}')
        ->name('create');

    Route::middleware('permission:editar {model_es}')
        ->get('/{{model}}/edit', fn ({Model} ${model}): Factory|View => view(
            '{context}::{resource}.edit',
            ['{model}' => ${model}]
        ))
        ->name('edit');
});
```

`Route::view()` where nothing is passed, a closure where a bound model is. A
route never points at a Livewire class.

An authorization decision belongs in a Policy (R39) — the `permission:` middleware
here is the coarse gate, not a substitute for one.

## Step 8 — Views

All five live in `app/Modules/{Context}/Resources/views/{resource}/`.

### Wrappers

`index.blade.php`

```blade
<x-layouts.app icon="lucide-{icon}" parent="{Context_es}" title="{Models_es}">
    {{ Breadcrumbs::render('{context}.{resource}.index') }}
    @livewire('{context}::{resource}.table')
</x-layouts.app>
```

`create.blade.php`

```blade
<x-layouts.app icon="lucide-{icon}" parent="{Context_es}" title="{Models_es}">
    {{ Breadcrumbs::render('{context}.{resource}.create') }}
    @livewire('{context}::{resource}.form')
</x-layouts.app>
```

`edit.blade.php`

```blade
<x-layouts.app icon="lucide-{icon}" parent="{Context_es}" title="{Models_es}">
    {{ Breadcrumbs::render('{context}.{resource}.edit', ${model}) }}
    @livewire('{context}::{resource}.form', ['record' => ${model}])
</x-layouts.app>
```

The Livewire name carries the `::` namespace. `@livewire('billing::invoices.table')`,
never `@livewire('billing.invoices.table')` — the second resolves to a different
component or to nothing.

### `_form.blade.php`

```blade
<div>
    <form wire:submit="save">
        <x-ts-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <x-ts-input
                    label="{{ __('{context}::{resource}.fields.name') }}"
                    wire:model="form.name"
                    placeholder="{{ __('{context}::{resource}.placeholders.name') }}"
                />

            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('{context}.{resource}.index') }}" wire:navigate
                       class="text-sm text-content-muted hover:text-content">
                        {{ __('platform::app.cancel') }}
                    </a>
                    <x-ts-button type="submit" wire:loading.attr="disabled">
                        {{ __('platform::app.save') }}
                    </x-ts-button>
                </div>
            </x-slot:footer>
        </x-ts-card>
    </form>
</div>
```

### `_index.blade.php`

Four bands: top bar (search · filter toggle · spacer · new button), collapsible
filter panel, table, footer (quantity · paginator).

Drop the filter button and panel when `filterable()` is empty — a toggle that
opens an empty panel is worse than no toggle. `$filters` is the array from
`HasTable`, keyed by column.

```blade
<div x-data="{ showFilters: false }">

    {{-- Barra superior --}}
    <div class="mb-3 flex items-center gap-2">

        {{-- Búsqueda --}}
        <div class="relative w-64">
            @svg('lucide-search', 'pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-content-subtle')
            <input
                wire:model.live.debounce.400ms="search"
                type="search"
                placeholder="{{ __('platform::table.search') }}"
                class="w-full rounded-lg border border-line bg-panel py-2 pl-9 pr-4 text-sm text-content placeholder-content-subtle focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:bg-panel"
            />
        </div>

        {{-- Botón filtros — solo si filterable() no está vacío --}}
        <button
            type="button"
            @click="showFilters = !showFilters"
            :class="showFilters || @js($filters['name'])
                ? 'border-primary-500 bg-primary-50 text-primary-600 dark:bg-primary-950 dark:text-primary-400'
                : 'border-line bg-panel text-content-muted hover:bg-panel-alt hover:text-content dark:bg-panel'"
            class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-3 py-2 text-sm transition-colors"
        >
            @svg('lucide-sliders-horizontal', 'size-4')
            {{ __('platform::table.filters') }}
            @if ($filters['name'])
                <span class="flex size-2 rounded-full bg-primary-500"></span>
            @endif
        </button>

        <div class="flex-1"></div>

        {{-- Botón nueva acción --}}
        @can('crear {model_es}')
            <a href="{{ route('{context}.{resource}.create') }}" wire:navigate
               class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
                @svg('lucide-plus', 'size-4')
                {{ __('platform::table.new', ['model' => __('{context}::{resource}.singular')]) }}
            </a>
        @endcan
    </div>

    {{-- Panel de filtros --}}
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
        <div class="flex items-center justify-between border-b border-line bg-panel-alt px-4 py-2.5">
            <div class="flex items-center gap-2 text-sm font-medium text-content-muted">
                @svg('lucide-sliders-horizontal', 'size-3.5')
                {{ __('platform::table.filters') }}
            </div>
            @php $activeCount = (int) (bool) $filters['name']; @endphp
            @if ($activeCount)
                <span class="inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                    {{ $activeCount }} {{ trans_choice('platform::table.active', $activeCount) }}
                </span>
            @endif
        </div>

        <div class="flex flex-wrap items-end gap-3 p-4">

            <x-ui.ts-table.filter-input
                label="{{ __('{context}::{resource}.fields.name') }}"
                icon="search"
                wire:model.live.debounce.400ms="filters.name"
                placeholder="{{ __('{context}::{resource}.placeholders.name') }}"
            />

            @if ($filters['name'])
                <div class="group relative self-end">
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="flex cursor-pointer items-center justify-center rounded-md border border-red-200 bg-red-50 p-2 text-red-600 transition-colors hover:border-red-300 hover:bg-red-100 dark:border-red-800 dark:bg-red-950 dark:text-red-400 dark:hover:border-red-700 dark:hover:bg-red-900"
                    >
                        @svg('lucide-rotate-ccw', 'size-3.5')
                    </button>
                    <div class="pointer-events-none absolute bottom-full left-1/2 mb-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-800 px-2 py-1 text-xs text-white opacity-0 transition-opacity duration-150 group-hover:opacity-100 dark:bg-dark-600">
                        {{ __('platform::table.clear') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabla --}}
    <x-ts-table
        :headers="$headers"
        :rows="${resource}"
        :sort="$sort"
        striped
    >
        @interact('column_status', $row)
            @if ($row->trashed())
                <x-ts-badge :text="__('platform::table.status_deleted')" color="red" />
            @else
                <x-ts-badge :text="__('platform::table.status_active')" color="green" />
            @endif
        @endinteract

        @interact('column_action', $row)
            <x-ui.ts-table.actions
                :row="$row"
                edit-route="{context}.{resource}.edit"
                edit-permission="editar {model_es}"
                delete-permission="eliminar {model_es}"
                restore-permission="restaurar {model_es}"
                model="{model_es}"
            />
        @endinteract
    </x-ts-table>

    {{-- Footer: conteo + paginador --}}
    @if (${resource}->total() > 0)
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">

            <div class="flex items-center gap-1.5 text-sm text-content-muted">
                {{ __('platform::table.showing') }}
                <select
                    wire:model.live="quantity"
                    class="rounded-md border border-line bg-panel px-2 py-1 text-sm text-content focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                {{ __('platform::table.results') }} {{ ${resource}->total() }}
            </div>

            @if (${resource}->hasPages())
                {{ ${resource}->links('tallstack-ui::components.table.paginators') }}
            @endif

        </div>
    @endif

</div>
```

## Step 9 — Lang files

`app/Modules/{Context}/Resources/lang/{es,en}/{resource}.php`

```php
<?php

declare(strict_types=1);

return [
    'title' => '{Models_es}',
    'singular' => '{Model_es}',
    'headers' => [
        'name' => 'Nombre',
    ],
    'fields' => [
        'name' => 'Nombre',
    ],
    'placeholders' => [
        'name' => 'Ingresa un nombre',
    ],
];
```

Write the `en` file too, with the same keys. A missing key renders as its own
path — `billing::invoices.title` in the middle of the page.

## Step 10 — Breadcrumbs

In `app/Modules/{Context}/Routes/breadcrumbs.php`. The module node is a
non-linked label; `dashboard` is not a parent.

```php
Breadcrumbs::for('{context}.{resource}.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('{context}');
    $trail->push(__('{context}::{resource}.title'), route('{context}.{resource}.index'));
});

Breadcrumbs::for('{context}.{resource}.create', function (BreadcrumbTrail $trail): void {
    $trail->parent('{context}.{resource}.index');
    $trail->push(__('platform::app.new'), route('{context}.{resource}.create'));
});

Breadcrumbs::for('{context}.{resource}.edit', function (BreadcrumbTrail $trail, {Model} ${model}): void {
    $trail->parent('{context}.{resource}.index');
    $trail->push(${model}->name, route('{context}.{resource}.edit', ${model}));
});
```

## Step 11 — Menu entry

`config/menu.php` stays global — the menu is one list across every module.

```php
[
    'label'        => 'platform::menu.{resource}',
    'icon'         => '{icon}',
    'route'        => '{context}.{resource}.index',
    'active_route' => '{context}.{resource}.*',
    'permission'   => 'ver {model_es}',
],
```

Add the key to `app/Modules/Platform/Resources/lang/{es,en}/menu.php`.

## Step 12 — Permissions

Permissions live in the **module's own config**, merged under its own key.
`mergeConfigFrom` does a first-level `array_merge`, not a recursive one: two
modules merging into the same key and the second one loses its values silently.

`app/Modules/{Context}/Config/permissions.php`

```php
<?php

declare(strict_types=1);

return [
    'permissions' => [
        '{model_es}' => [
            'ver {model_es}',
            'crear {model_es}',
            'editar {model_es}',
            'eliminar {model_es}',
            'restaurar {model_es}',
        ],
    ],
];
```

In the module's ServiceProvider:

```php
$this->mergeConfigFrom(__DIR__.'/Config/permissions.php', '{context}');
```

Then a seeder that reads its own key, registered in `database/seeders/DatabaseSeeder.php`
with an explicit `use` — an implicit namespace is what broke the clean install
when the seeders moved.

**Creating a permission gives it to nobody.** Assign them, or the module works
for the admin — `Gate::before` short-circuits before permissions are ever
consulted — and returns 403 to everyone else. That failure only shows up once a
real user opens the screen, which is long after you tested it as admin.

`app/Modules/{Context}/Database/Seeders/{Context}PermissionsSeeder.php`
```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Database\Seeders;

use App\Modules\Access\Enums\Roles;
use App\Modules\Access\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class {Context}PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array<string, list<string>> $groups */
        $groups = config('{context}.permissions', []);
        $names = array_merge(...array_values($groups));

        foreach ($names as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Un módulo cuyos permisos nadie tiene es un módulo que nadie puede
        // administrar. Depender de Access aquí es legítimo: es plataforma (R9).
        Role::findByName(Roles::ADMIN->value)->givePermissionTo($names);
    }
}
```

Give them to the roles that should have them beyond `admin` in the same place —
it is the module's own seeder, so deciding who sees its screens is its call.

Permissions are still Spanish strings. R40 moves them to `{context}.{resource}.{action}`
in English, which is a data migration of its own — follow the existing form
until then, and do not invent a third convention here.

## Step 13 — Verify

```bash
bash scripts/arch-lint.sh
ddev exec php artisan arch:check
ddev exec vendor/bin/pint --dirty
ddev exec vendor/bin/phpstan analyse --no-progress
ddev exec php artisan migrate --force
ddev exec php artisan test --compact
```

Then load `/{context}/{resource}` in the browser and click through create, edit
and delete. The suite does not exercise the wrapper views or the route
bindings — a green suite with a broken screen is the normal failure here.

**Check it as a non-admin too.** `Gate::before` returns true for admin before
any permission is read, so the screens work for you whether or not the
permissions were ever assigned. Log in with the DEV «Usuario» button, or:

```bash
ddev exec php artisan tinker --execute 'echo json_encode(\App\Modules\Access\Models\Role::findByName("admin")->hasPermissionTo("ver {model_es}"));'
```

`false` means step 12 did not run or did not assign.

## Step 14 — Commit

Use the `git-commits` skill.

```
feat: CRUD {Models_es} en {Context}

- Modelo `{Model}` con UUID, SoftDeletes, newFactory y factory
- Migracion de `{table}` con: {Fields}
- Form object y componentes Form y Table en Livewire/{Resource}/
- Vistas index, create, edit, _form y _index en Resources/views/{resource}/
- Traducciones es y en
- Rutas protegidas por permiso y breadcrumbs del modulo
- Entrada en config/menu.php y permisos en Config/permissions.php
```
