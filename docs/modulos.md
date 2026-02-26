# Cómo crear dominios y módulos

Este proyecto organiza el área autenticada en **dominios** (ej. General, Operaciones, Ventas) y cada dominio tiene **módulos** (ej. Dashboard, Usuarios, Clientes).

---

## Estructura actual

```
app/Livewire/App/
├── General/
│   └── Dashboard.php
├── Operations/
│   └── Dashboard.php
└── Sales/
    └── Dashboard.php

resources/views/app/
├── general/
│   └── dashboard/
│       └── index.blade.php
├── operations/
│   └── dashboard/
│       └── index.blade.php
└── sales/
    └── dashboard/
        └── index.blade.php

routes/
├── web.php           ← público + auth (login, register)
├── general.php       ← /dashboard y rutas globales del app
├── operations.php    ← /operations/*
└── sales.php         ← /sales/*
```

---

## Dominios registrados

| Dominio | Prefijo URL | Nombre base | Archivo de rutas |
|---|---|---|---|
| General | `/` | `dashboard`, `profile`... | `routes/general.php` |
| Operaciones | `/operations` | `operations.` | `routes/operations.php` |
| Ventas | `/sales` | `sales.` | `routes/sales.php` |

---

## Agregar un nuevo dominio

Ejemplo: agregar el dominio **Inventario** (`/inventory`).

### 1. Livewire — Dashboard del dominio

Crear `app/Livewire/App/Inventory/Dashboard.php`:

```php
<?php

namespace App\Livewire\App\Inventory;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Dashboard extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.inventory.dashboard.index')
            ->layout('components.layouts.app');
    }
}
```

### 2. Vista — Dashboard del dominio

Crear `resources/views/app/inventory/dashboard/index.blade.php`:

```blade
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-content">Inventario</h1>
        <p class="mt-1 text-sm text-content-muted">Panel de gestión de inventario</p>
    </div>
</div>
```

### 3. Archivo de rutas

Crear `routes/inventory.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('inventory')->name('inventory.')->group(function () {

    Route::get('/dashboard', \App\Livewire\App\Inventory\Dashboard::class)
        ->name('dashboard');

    // Route::get('/products', \App\Livewire\App\Inventory\Products\Index::class)->name('products.index');

});
```

### 4. Registrar en bootstrap/app.php

Dentro del callback `then`, añadir:

```php
Route::middleware(['web', 'auth'])
    ->group(base_path('routes/inventory.php'));
```

### 5. Añadir al menú (config/menu.php)

```php
[
    'label'        => 'Inventario',
    'icon'         => 'layout-grid',
    'active_route' => 'inventory.*',
    'items' => [
        [
            'label' => 'Dashboard',
            'route' => 'inventory.dashboard',
        ],
    ],
],
```

---

## Agregar un módulo dentro de un dominio

Ejemplo: añadir **Productos** dentro de Inventario.

### 1. Livewire

Crear `app/Livewire/App/Inventory/Products/Index.php`:

```php
<?php

namespace App\Livewire\App\Inventory\Products;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.inventory.products.index')
            ->layout('components.layouts.app');
    }
}
```

### 2. Vista

Crear `resources/views/app/inventory/products/index.blade.php`:

```blade
<div>
    <h1 class="text-2xl font-bold text-content">Productos</h1>
</div>
```

### 3. Ruta en routes/inventory.php

```php
Route::get('/products', \App\Livewire\App\Inventory\Products\Index::class)
    ->name('products.index');
```

### 4. Menú en config/menu.php

```php
[
    'label'        => 'Productos',
    'route'        => 'inventory.products.index',
    'active_route' => 'inventory.products.*',
    'permission'   => 'ver productos', // opcional
],
```

---

## Convenciones de nombres

| Elemento | Convención | Ejemplo |
|---|---|---|
| Namespace Livewire | `App\Livewire\App\{Dominio}\{Módulo}` | `App\Livewire\App\Inventory\Products` |
| Clase Livewire | PascalCase | `Index`, `Create`, `Edit`, `Show` |
| Vista | `app.{dominio}.{modulo}.{acción}` | `app.inventory.products.index` |
| Prefijo URL | kebab-case | `/inventory/products` |
| Nombre de ruta | `{dominio}.{modulo}.{acción}` | `inventory.products.index` |
| Archivo de rutas | `routes/{dominio}.php` | `routes/inventory.php` |
