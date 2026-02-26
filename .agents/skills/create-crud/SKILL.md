---
name: create-crud
description: Generates a complete CRUD module following the boilerplate conventions (PowerGrid tables, Livewire forms, wrapper/_index views, domain-based routing).
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to use me

Use this skill when the user needs a complete CRUD module: a listing table, a create form, and an edit form.

Trigger phrases: "crear crud", "nuevo módulo", "generar módulo", "necesito una tabla con formulario", "create crud".

---

## Required information

Before generating anything, confirm you have:

- `{Model}` — PascalCase singular (e.g. `Product`)
- `{model}` — camelCase singular (e.g. `product`)
- `{models}` — snake_case plural (e.g. `products`)
- `{model-slug}` — kebab-case plural for URLs (e.g. `products`)
- `{Domain}` — PascalCase domain (e.g. `General`)
- `{domain}` — lowercase domain (e.g. `general`)
- `{model_es}` — Spanish singular (e.g. `producto`)
- `{Model_es}` — Spanish singular capitalized (e.g. `Producto`)
- `{models_es}` — Spanish plural (e.g. `productos`)
- `{Models_es}` — Spanish plural capitalized (e.g. `Productos`)
- `{Fields}` — field list with types (e.g. `name string, price decimal`)

If any value is missing, ask the user before proceeding.

---

## What I do

Read `docs/crud.md` in full before starting. That file is the authoritative step-by-step guide for this boilerplate.

### Plan before coding

Create a task list with every step before writing any file:

1. Artisan commands
2. Model
3. Migration
4. Factory
5. Livewire Form object
6. Livewire FormComponent
7. Livewire PowerGrid Table
8. Routes in `routes/{domain}.php`
9. Views (index wrapper, create wrapper, edit wrapper, _form component, _toolbar)
10. Breadcrumbs in `routes/breadcrumbs.php`
11. Menu entry in `config/menu.php`
12. Permissions list
13. Commit

### Key conventions to follow

- Activate `livewire-development` when writing Livewire components.
- Activate `powergrid-tables` when writing the Table component.
- Never use `->layout()` in `render()` — layout lives in the wrapper view.
- Routes use `fn () => view(...)`, never point to Livewire classes directly.
- Livewire classes go in `app/Livewire/App/{Domain}/{Model}/` folder.
- Views follow wrapper + _component pattern inside `resources/views/app/{domain}/{model-slug}/`.
- The FormComponent uses `$record` (not `$modelname`) as the optional model prop.
- Toast messages use `$this->toast()->success()->flash()->send()` before redirect.
- After save, redirect with `$this->redirect(route('...'), navigate: true)`.

### Commit message format

```
feat: CRUD {Models_es}

- Modelo `{Model}` con SoftDeletes y factory
- Migración tabla `{models}` con campos: {Fields}
- Form object `{Model}Form` con validación
- Componente `Form` (create/edit) con toast de confirmación
- Tabla PowerGrid `Table` con búsqueda, sort, soft delete y restore
- Vistas: index, create, edit (wrappers) + _form, _toolbar (componentes)
- Rutas protegidas por permiso en routes/{domain}.php
- Breadcrumbs para el flujo completo
- Entrada en config/menu.php
- Permisos: ver, crear, editar, eliminar, restaurar {model_es}
```
