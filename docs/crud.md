# Crear un CRUD

Un CRUD en este boilerplate genera automáticamente el conjunto completo de archivos
necesarios para listar, crear y editar un modelo: tabla interactiva (PowerGrid),
formulario Livewire, rutas, vistas, breadcrumbs y permisos.

---

## Cómo pedírselo al agente

Escribe algo como:

```
Crea un CRUD para el modelo Product en el dominio General.
Campos: name (string), price (decimal), active (boolean).
En español: producto / productos.
```

El agente activará el skill `create-crud` y generará todo.

---

## Información que debes proporcionar

| Dato | Descripción | Ejemplo |
|---|---|---|
| Modelo | PascalCase singular | `Product` |
| Dominio | Dónde vive el módulo | `General` |
| Campos | Nombre y tipo de cada campo | `name string, price decimal` |
| Español singular | Nombre en español | `producto` |
| Español plural | Nombre en español plural | `productos` |

---

## Qué se genera

```
app/
├── Models/Product.php                              ← modelo con SoftDeletes
├── Livewire/
│   ├── App/General/Product/
│   │   ├── Table.php                               ← tabla PowerGrid
│   │   └── Form.php                               ← componente create/edit
│   └── Forms/
│       └── ProductForm.php                         ← Livewire Form object
database/
├── migrations/xxxx_create_products_table.php
└── factories/ProductFactory.php

resources/views/app/general/products/
├── index.blade.php                                 ← wrapper lista
├── create.blade.php                                ← wrapper crear
├── edit.blade.php                                  ← wrapper editar
├── _form.blade.php                                 ← componente formulario
└── _toolbar.blade.php                              ← botón "Nuevo"

routes/general.php                                  ← rutas del módulo
routes/breadcrumbs.php                              ← breadcrumbs
config/menu.php                                     ← entrada en menú
```

---

## Convenciones que sigue

- Las rutas apuntan a vistas (`fn () => view(...)`), nunca a clases Livewire directas.
- Cada página tiene un wrapper (`index.blade.php`) y un componente (`_form.blade.php`).
- Los Livewire components no usan `->layout()` en `render()`.
- La tabla usa PowerGrid v6 con soporte de soft delete y restore.
- Los botones de acción respetan permisos con `@can`.
- Las rutas están protegidas con `middleware('permission:...')`.

---

## Agregar campos al formulario

Después de que el agente genere el CRUD, edita `_form.blade.php` para añadir
los campos adicionales usando componentes TallStackUI:

```blade
{{-- Input de texto --}}
<x-ts-input label="Nombre" wire:model="form.name" />

{{-- Número --}}
<x-ts-input label="Precio" wire:model="form.price" type="number" />

{{-- Select --}}
<x-ts-select label="Categoría" wire:model="form.category_id" :options="$categories" />

{{-- Toggle --}}
<x-ts-toggle label="Activo" wire:model="form.active" />
```

---

## Ver también

- `docs/modulos.md` — cómo crear dominios y módulos sin CRUD
- `.agents/skills/create-crud/SKILL.md` — instrucciones internas del agente
