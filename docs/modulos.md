# Cómo crear dominios y módulos

Este proyecto organiza el área autenticada en **dominios** (ej. Operaciones, Ventas) y cada dominio tiene **módulos** (ej. Usuarios, Clientes, Reportes).

---

## Estructura de referencia

```
app/Livewire/App/
├── Dashboard.php              ← home general tras login
├── Operations/
│   ├── Dashboard.php
│   ├── Users/
│   └── Roles/
└── Sales/
    ├── Dashboard.php
    └── Customers/

resources/views/app/
├── dashboard/
├── operations/
│   ├── dashboard/
│   └── users/
└── sales/
    ├── dashboard/
    └── customers/

routes/
├── web.php
├── operations.php
└── sales.php
```

---

## Agregar un nuevo dominio

Ejemplo: agregar el dominio **Inventario**.

### 1. Carpetas Livewire

```bash
mkdir -p app/Livewire/App/Inventory
```

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

### 2. Carpetas de vistas

```bash
mkdir -p resources/views/app/inventory/dashboard
```

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
    // Route::get('/stock',    \App\Livewire\App\Inventory\Stock\Index::class)->name('stock.index');

});
```

### 4. Registrar las rutas en bootstrap/app.php

Dentro del callback `then`, añadir:

```php
Route::middleware(['web', 'auth'])
    ->group(base_path('routes/inventory.php'));
```

### 5. Agregar al menú (config/menu.php)

```php
[
    'label'        => 'Inventario',
    'icon'         => 'layout-grid',
    'active_route' => 'inventory.*',
    'items' => [
        [
            'label'        => 'Dashboard',
            'route'        => 'inventory.dashboard',
            'active_route' => 'inventory.dashboard',
        ],
    ],
],
```

---

## Agregar un módulo dentro de un dominio existente

Ejemplo: agregar **Productos** dentro de Inventario.

### 1. Crear la carpeta y el componente Livewire

```bash
mkdir -p app/Livewire/App/Inventory/Products
```

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

### 2. Crear la vista

```bash
mkdir -p resources/views/app/inventory/products
```

Crear `resources/views/app/inventory/products/index.blade.php`:

```blade
<div>
    <h1 class="text-2xl font-bold text-content">Productos</h1>
</div>
```

### 3. Añadir la ruta en routes/inventory.php

```php
Route::get('/products', \App\Livewire\App\Inventory\Products\Index::class)
    ->name('products.index');
```

### 4. Añadir al menú en config/menu.php

Dentro del grupo Inventario:

```php
[
    'label'        => 'Productos',
    'route'        => 'inventory.products.index',
    'active_route' => 'inventory.products.*',
    'permission'   => 'ver productos',  // opcional
],
```

---

## Convenciones de nombres

| Elemento | Convención | Ejemplo |
|---|---|---|
| Namespace Livewire | `App\Livewire\App\{Dominio}\{Módulo}` | `App\Livewire\App\Inventory\Products` |
| Clase Livewire | PascalCase | `Index`, `Create`, `Edit` |
| Vista | `app.{dominio}.{modulo}.{acción}` | `app.inventory.products.index` |
| Ruta prefijo | kebab-case | `/inventory/products` |
| Nombre de ruta | `{dominio}.{modulo}.{acción}` | `inventory.products.index` |
| Archivo de rutas | `routes/{dominio}.php` | `routes/inventory.php` |

---

## Dominios actuales

| Dominio | Prefijo URL | Nombre base | Archivo de rutas |
|---|---|---|---|
| General | `/dashboard` | `dashboard` | `web.php` |
| Operaciones | `/operations` | `operations.` | `routes/operations.php` |
| Ventas | `/sales` | `sales.` | `routes/sales.php` |
