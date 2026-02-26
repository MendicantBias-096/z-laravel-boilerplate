---
name: create-module
description: Creates a domain or a module within an existing domain following the boilerplate conventions.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to activate

Activate when the user asks to create a new domain or a simple module (no table, no form).
Trigger phrases: "nuevo módulo", "nuevo dominio", "crear módulo", "agregar módulo", "create module".

If the user also wants listing, create and edit → use `create-crud` instead.

---

## Required variables — ask if missing

| Variable | Description | Example |
|---|---|---|
| `{Domain}` | PascalCase domain | `Inventory` |
| `{domain}` | lowercase domain | `inventory` |
| `{Module}` | PascalCase module | `Products` |
| `{module}` | lowercase module | `products` |
| `{module-slug}` | kebab-case (URL) | `products` |
| `{isNewDomain}` | Is this a brand-new domain? | `yes / no` |

---

## Execution plan

Create a task list before writing any file. Mark each step as completed.

1. Livewire class
2. Wrapper view
3. Component view
4. Route in `routes/{domain}.php`
5. Register in `bootstrap/app.php` (only if new domain)
6. Menu entry in `config/menu.php`
7. Commit

---

## Step 1 — Livewire class `app/Livewire/App/{Domain}/{Module}/Index.php`

```php
<?php

namespace App\Livewire\App\{Domain}\{Module};

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.{domain}.{module-slug}._index');
    }
}
```

---

## Step 2 — Wrapper view `resources/views/app/{domain}/{module-slug}/index.blade.php`

```blade
<x-layouts.app>
    @livewire('app.{domain}.{module-slug}.index')
</x-layouts.app>
```

---

## Step 3 — Component view `resources/views/app/{domain}/{module-slug}/_index.blade.php`

```blade
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-content">{Module}</h1>
        <p class="mt-1 text-sm text-content-muted">Gestión de {module-slug}</p>
    </div>
</div>
```

---

## Step 4 — Route in `routes/{domain}.php`

If the file already exists, add inside the existing group:

```php
Route::get('/{module-slug}', fn () => view('app.{domain}.{module-slug}.index'))
    ->name('{module-slug}.index');
```

If this is a **new domain**, create `routes/{domain}.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{domain}')->name('{domain}.')->group(function () {

    Route::get('/dashboard', fn () => view('app.{domain}.dashboard.index'))
        ->name('dashboard');

});
```

And also create the dashboard module files (Steps 1–3) with `{Module}` = `Dashboard`.

---

## Step 5 — Register in `bootstrap/app.php` (new domain only)

Inside the `then` callback, add:

```php
Route::middleware(['web', 'auth'])
    ->group(base_path('routes/{domain}.php'));
```

---

## Step 6 — Menu entry in `config/menu.php`

Single module (no children):

```php
[
    'label'        => '{Module}',
    'route'        => '{domain}.{module-slug}.index',
    'active_route' => '{domain}.{module-slug}.*',
    'permission'   => 'ver {module-slug}', // remove if no permission needed
],
```

New domain with children:

```php
[
    'label'        => '{Domain}',
    'icon'         => 'layout-grid',
    'active_route' => '{domain}.*',
    'items' => [
        [
            'label' => 'Dashboard',
            'route' => '{domain}.dashboard',
        ],
        [
            'label'        => '{Module}',
            'route'        => '{domain}.{module-slug}.index',
            'active_route' => '{domain}.{module-slug}.*',
        ],
    ],
],
```

---

## Step 7 — Commit

```
feat: módulo {Domain}/{Module}

- Livewire `App\{Domain}\{Module}\Index`
- Vistas wrapper + componente en app/{domain}/{module-slug}/
- Ruta `{domain}.{module-slug}.index` en routes/{domain}.php
[- Nuevo dominio {domain} registrado en bootstrap/app.php]
[- Entrada en config/menu.php]
```
