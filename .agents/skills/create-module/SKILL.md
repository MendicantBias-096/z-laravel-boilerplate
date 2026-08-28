---
name: create-module
description: Creates a module (bounded context) under app/Modules, or adds a plain resource to an existing one.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to activate

Two distinct branches — establish which one before writing anything:

- **New module** — a bounded context of its own: `Billing`, `Inventory`, `Crm`.
  Triggers: "nuevo módulo", "nuevo contexto", "crear módulo", "create module".
- **New resource** — a screen inside a module that already exists.
  Triggers: "agregar pantalla a", "nueva sección en", "add resource to".

If the resource needs listing, create and edit → use `create-crud` instead.

The architecture behind these names is `docs/ARCHITECTURE_RULES.md` (R1–R56) and
the «Modules» section of `CLAUDE.md`. Read them before inventing a folder; cite
rules by id when a choice needs defending.

---

## Vocabulary

The old `{Domain}/{Module}` pair is gone. The structure is now:

| Term         | What it is                                  | Example    |
| ------------ | ------------------------------------------- | ---------- |
| `{Context}`  | PascalCase module — a bounded context       | `Billing`  |
| `{context}`  | lowercase, the view/lang/Livewire namespace | `billing`  |
| `{Resource}` | PascalCase resource inside the module       | `Invoices` |
| `{resource}` | kebab-case resource (URL and view folder)   | `invoices` |

Derived names, all mechanical — `CLAUDE.md` has the full table:

```
class      App\Modules\{Context}\Livewire\{Resource}\Index
view       {context}::{resource}.index
route      {context}.{resource}.index
url        /{context}/{resource}      business module
           /{resource}                platform module (Access, Platform)
```

---

# Branch A — new module

## Required variables — ask if missing

| Variable       | Description                     | Example             |
| -------------- | ------------------------------- | ------------------- |
| `{Context}`    | PascalCase module               | `Billing`           |
| `{context}`    | lowercase                       | `billing`           |
| `{Context_es}` | Spanish label shown to humans   | `Facturación`       |
| `{purpose}`    | One line: what this module owns | `facturas y cobros` |

## Execution plan

Create a task list before writing any file. Mark each step as completed.

1. Folder skeleton
2. ServiceProvider
3. Register in `bootstrap/providers.php`
4. `Routes/web.php`
5. `Routes/breadcrumbs.php`
6. `README.md`
7. Verify with `arch-lint.sh`
8. Commit

## Step 1 — Folder skeleton

The folder list is **closed**: `scripts/arch-lint.sh` checks it (R6). Create only
the folders the module actually uses — an empty folder is noise — but never
invent a type outside this list. Adding a type means editing R6 and the linter,
not improvising here.

```
app/Modules/{Context}/
├── Contracts/              public surface (R8): interfaces, their DTOs and exceptions
├── Events/                 public surface (R8)
├── Models/  Actions/  Data/  Enums/  Exceptions/  Http/
├── Listeners/  Observers/  Rules/  Services/  Traits/
├── Notifications/  Policies/  Jobs/  Console/  Livewire/
├── Database/{Migrations,Seeders,Factories}
├── Resources/{views,lang}
├── Config/  Routes/{web,breadcrumbs}.php
├── Tests/{Unit,Feature}
├── {Context}ServiceProvider.php
└── README.md
```

## Step 2 — ServiceProvider

Laravel discovers by path convention, and inside a module that convention no
longer applies. Every line below exists because something breaks silently
without it. Copy `app/Modules/Platform/PlatformServiceProvider.php` and adapt —
it is the reference implementation.

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context};

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Registra lo que Laravel encontraría solo si el módulo viviera en la raíz.
 *
 * Las Policies son la excepción: `Gate::guessPolicyName()` sustituye
 * `\Models\` por `\Policies\` y las encuentra sola.
 */
class {Context}ServiceProvider extends ServiceProvider
{
    /** El prefijo con el que se citan las vistas y los componentes. */
    private const NAMESPACE = '{context}';

    public function boot(): void
    {
        // Una columna polimórfica guarda el FQCN como texto, así que renombrar
        // un namespace deja esas filas apuntando a una clase que ya no existe
        // y el dato se pierde sin un error. El alias lo evita.
        Relation::morphMap([
            // 'invoice' => Models\Invoice::class,
        ]);

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/Resources/views', self::NAMESPACE);
        $this->loadTranslationsFrom(__DIR__.'/Resources/lang', self::NAMESPACE);

        // addNamespace y no addLocation: el segundo deriva el nombre recortando
        // el prefijo, así que dos módulos con la misma subcarpeta producen el
        // mismo nombre y gana el registrado primero, sin decir nada.
        Livewire::addNamespace(
            self::NAMESPACE,
            viewPath: __DIR__.'/Resources/views',
            classNamespace: __NAMESPACE__.'\\Livewire',
        );

        Blade::anonymousComponentNamespace(__DIR__.'/Resources/views/components', self::NAMESPACE);

        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        require __DIR__.'/Routes/breadcrumbs.php';
    }
}
```

Add only what the module uses:

```php
public function register(): void
{
    // Cada módulo mergea en SU PROPIA clave. `mergeConfigFrom` hace
    // `array_merge` de primer nivel, no recursivo: dos módulos sobre la misma
    // clave y el segundo pierde sus valores en silencio.
    $this->mergeConfigFrom(__DIR__.'/Config/{context}.php', '{context}');
}

public function boot(): void
{
    // …
    // Un comando dentro de un módulo no se autorregistra.
    if ($this->app->runningInConsole()) {
        $this->commands([Console\SomeCommand::class]);
    }
}
```

## Step 3 — Register in `bootstrap/providers.php`

A module provider does **not** autodiscover — that only happens for packages
declaring `extra.laravel.providers`. Order matters: a module goes after the
modules it depends on, and `Platform` is the base of the graph (R9).

```php
use App\Modules\{Context}\{Context}ServiceProvider;

return [
    // …
    PlatformServiceProvider::class,
    AccessServiceProvider::class,
    {Context}ServiceProvider::class,
];
```

## Step 4 — `Routes/web.php`

A business module prefixes its URLs; a platform module does not (R5).

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * {Context} · {purpose}.
 */
Route::middleware(['web', 'auth', 'verified'])
    ->prefix('{context}')
    ->name('{context}.')
    ->group(function (): void {

        Route::view('/', '{context}::dashboard.index')->name('dashboard');
    });
```

`Route::view()` over `fn () => view(...)` when nothing is passed — same rule
(never point a route at a Livewire class), less noise.

## Step 5 — `Routes/breadcrumbs.php`

The root node carries no URL: it is the group label in the navigation, and it
reads the name a human uses, not the module's.

```php
<?php

declare(strict_types=1);

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('{context}', function (BreadcrumbTrail $trail): void {
    $trail->push(__('platform::menu.{context}'));
});
```

Add that key to `app/Modules/Platform/Resources/lang/{es,en}/menu.php` in the
same step. A missing one renders as `platform::menu.{context}` at the head of
every breadcrumb in the module.

## Step 6 — `README.md`

One screen, answering what the next person actually asks:

```markdown
# {Context}

{purpose}

## Qué posee

Las tablas `{context}_*` y las decisiones sobre ellas.

## Superficie pública

Lo que otro módulo puede importar vive en `Contracts/` y `Events/` (R8).
Todo lo demás es interno, aunque sea `public` en PHP.

## Depende de

Platform. Nada más.
```

## Step 7 — Verify

```bash
bash scripts/arch-lint.sh
ddev exec php artisan route:list --except-vendor
```

`arch-lint.sh` checks the folder list (R6) and the module boundaries. A new
module that passes it is wired correctly; one that does not tells you which
rule it broke.

## Step 8 — Commit

Use the `git-commits` skill.

```
feat: modulo {Context}

- Esqueleto en app/Modules/{Context}/ con las carpetas que usa
- {Context}ServiceProvider registra vistas, lang, rutas y Livewire
- Registrado en bootstrap/providers.php despues de Platform
- Rutas bajo /{context} y nodo raiz de breadcrumbs
```

---

# Branch B — new resource in an existing module

Three layers, unchanged from before — only the namespaces moved:

```
Route      Route::view('/{resource}', '{context}::{resource}.index')
Wrapper    Resources/views/{resource}/index.blade.php     <x-layouts.app> + @livewire
Component  Resources/views/{resource}/_index.blade.php    HTML, no layout
Livewire   Livewire/{Resource}/Index.php                  returns view('{context}::{resource}._index')
```

## Step 1 — Livewire class

`app/Modules/{Context}/Livewire/{Resource}/Index.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Livewire\{Resource};

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render(): Factory|View
    {
        return view('{context}::{resource}._index');
    }
}
```

## Step 2 — Wrapper view

`app/Modules/{Context}/Resources/views/{resource}/index.blade.php`

```blade
<x-layouts.app icon="lucide-{icon}" parent="{Context_es}" title="{Resource_es}">
    {{ Breadcrumbs::render('{context}.{resource}.index') }}
    @livewire('{context}::{resource}.index')
</x-layouts.app>
```

The Livewire name carries the `::` namespace — `@livewire('billing::invoices.index')`,
not `@livewire('billing.invoices.index')`.

## Step 3 — Component view

`app/Modules/{Context}/Resources/views/{resource}/_index.blade.php`

```blade
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-content">{{ __('{context}::{resource}.title') }}</h1>
        <p class="mt-1 text-sm text-content-muted">{{ __('{context}::{resource}.subtitle') }}</p>
    </div>
</div>
```

Text goes through `__()` against the module's own lang files, never hardcoded —
the boilerplate ships `es` and `en`, and a hardcoded string breaks the second.

## Step 4 — Lang files

`app/Modules/{Context}/Resources/lang/{es,en}/{resource}.php`

```php
<?php

declare(strict_types=1);

return [
    'title' => '{Resource_es}',
    'subtitle' => 'Gestión de {resource}',
];
```

## Step 5 — Route

Inside the existing group in `app/Modules/{Context}/Routes/web.php`:

```php
Route::view('/{resource}', '{context}::{resource}.index')
    ->middleware('permission:ver {resource_es}')
    ->name('{resource}.index');
```

## Step 6 — Breadcrumb

In `app/Modules/{Context}/Routes/breadcrumbs.php`:

```php
Breadcrumbs::for('{context}.{resource}.index', function (BreadcrumbTrail $trail): void {
    $trail->parent('{context}');
    $trail->push(__('{context}::{resource}.title'), route('{context}.{resource}.index'));
});
```

## Step 7 — Menu entry

`config/menu.php` stays global — the menu is one list across every module.
Labels are lang keys.

```php
[
    'label'        => 'platform::menu.{resource}',
    'icon'         => '{icon}',
    'route'        => '{context}.{resource}.index',
    'active_route' => '{context}.{resource}.*',
    'permission'   => 'ver {resource_es}',
],
```

Add the key to `app/Modules/Platform/Resources/lang/{es,en}/menu.php`.

## Step 8 — Verify and commit

```bash
bash scripts/arch-lint.sh
ddev exec php artisan route:list --except-vendor --path={resource}
```

Then use the `git-commits` skill.
