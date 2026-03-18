<boilerplate-guidelines>
=== foundation rules ===

# Laravel Boilerplate Guidelines

This is a Laravel boilerplate project. These guidelines must be followed closely to ensure consistency across all development.

## Stack & Versions

- **php** — 8.4+
- **laravel/framework** — v12
- **livewire/livewire** — v4
- **tallstackui/tallstackui** — v2
- **power-components/livewire-powergrid** — v6
- **spatie/laravel-permission** — v7
- **spatie/laravel-medialibrary** — v11
- **diglactic/laravel-breadcrumbs** — v10
- **dedoc/scramble** — v0.13
- **sentry/sentry-laravel** — v4
- **laravel/boost** — latest (dev)
- **tailwindcss** — v4
- **alpinejs** — v3

## Skills Activation

Activate the relevant skill whenever you work in that domain — don't wait until you're stuck.

- `git-commits` — Enforces Spanish Conventional Commits and branch naming. Activate always before creating a commit or a new branch.
- `create-mcp` — Creates an MCP server with tools, resources and prompts using `laravel/mcp`. Activate when the user wants to expose app functionality to AI clients; when they mention "mcp", "servidor mcp", "exponer al agente", or "mcp tool".
- `create-module` — Creates a domain or a simple module (no table, no form). Activate when the user asks to add a new domain or a plain module; when they mention "nuevo módulo", "nuevo dominio", "crear módulo", or "agregar módulo". If listing/create/edit is also needed, use `create-crud` instead.
- `create-crud` — Generates a complete CRUD module. Activate when the user asks to create a new module with listing, create, and edit; when they mention "crud", "módulo nuevo", "generar módulo", or "tabla con formulario".
- `livewire-development` — Develops reactive Livewire 4 components. Activate when creating or modifying Livewire components, using wire: directives, adding real-time behavior, or debugging component reactivity.
- `powergrid-tables` — Builds PowerGrid v6 data tables. Activate when creating or modifying tables, adding columns, filters, actions, or bulk operations.

## Conventions

- Follow existing code conventions in sibling files before writing new code.
- Check for existing components to reuse before creating new ones.
- Never use `->layout()` in Livewire `render()` methods — layout is set in the wrapper view.
- Never point routes directly to Livewire classes — always use `fn () => view(...)`.
- All Livewire component classes live in a subfolder named after the module: `App\Livewire\App\{Domain}\{Module}\Index.php`.

## Documentation Files

- Only create documentation files if explicitly requested.

## Replies

- Be concise. Focus on what matters, not obvious details.

=== ddev rules ===

# Development Environment — DDEV

This project runs on DDEV. All PHP/Composer/Artisan commands must be prefixed with `ddev`.

## Common Commands

```bash
ddev start                        # Iniciar entorno
ddev stop                         # Detener entorno
ddev ssh                          # Entrar al contenedor
ddev exec php artisan {cmd}       # Artisan command
ddev composer require {pkg}       # Instalar paquete PHP
ddev exec php artisan migrate     # Migraciones
ddev exec php artisan db:seed     # Seeders
```

## Frontend

```bash
ddev bun run dev      # Vite en modo desarrollo
ddev bun run build    # Compilar assets para producción
```

If the user doesn't see frontend changes, they may need to run `ddev bun run build` or `ddev bun run dev`.

## URLs

The project is available at the DDEV-configured URL. Check `.ddev/config.yaml` for the exact domain.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion in `__construct()`.
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for all method parameters.
- Prefer PHPDoc blocks over inline comments.
- Never use `env()` outside of config files — always use `config('key')`.

=== laravel/core rules ===

# Laravel Conventions

- Use `php artisan make:` commands to create new files.
- Use Eloquent models and relationships before suggesting raw queries.
- Avoid `DB::` — prefer `Model::query()`.
- Prevent N+1 problems with eager loading.
- Use Form Request classes for validation — never inline validation in controllers.
- Use named routes and the `route()` function for URL generation.
- Use queued jobs with `ShouldQueue` for time-consuming operations.
- When creating models, always create their factory too.

## Laravel 12 Structure

- Middleware is configured in `bootstrap/app.php` via `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` registers middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains service providers.
- Console commands in `app/Console/Commands/` are auto-registered.

=== architecture rules ===

# Project Architecture

This project organizes the authenticated area into **domains** (e.g. General, Operations) and each domain has **modules** (e.g. Dashboard, Users, Settings).

## Three-Layer Convention

Every page follows this exact pattern (same as auth module):

```
Route        →  fn () => view('{wrapper}')          never points to Livewire class
Wrapper      →  {module}/index.blade.php             <x-layouts.app> + @livewire(...)
Component    →  {module}/_index.blade.php            actual HTML, no layout
Livewire     →  {Module}/Index.php                   return view('...._index'), no ->layout()
```

## File Locations

```
app/Livewire/App/{Domain}/{Module}/Index.php
resources/views/app/{domain}/{module}/index.blade.php   ← wrapper
resources/views/app/{domain}/{module}/_index.blade.php  ← component
routes/{domain}.php
```

## Naming Conventions

| Element | Convention | Example |
|---|---|---|
| Livewire namespace | `App\Livewire\App\{Domain}\{Module}` | `App\Livewire\App\General\Users` |
| Livewire class | Always `Index` inside its folder | `Users/Index.php` |
| Wrapper view | `app.{domain}.{module}.index` | `app.general.users.index` |
| Component view | `app.{domain}.{module}._index` | `app.general.users._index` |
| Route | `fn () => view('{wrapper}')` | `fn () => view('app.general.users.index')` |
| Route name | `{domain}.{module}.{action}` | `general.users.index` |
| Route file | `routes/{domain}.php` | `routes/general.php` |
| URL prefix | kebab-case | `/users` |

## Registered Domains

| Domain | URL prefix | Route file | Modules |
|---|---|---|---|
| General | `/` | `routes/general.php` | Dashboard |
| Personal | `/personal` | `routes/personal.php` | Roles, Usuarios |

Use the `create-module` skill for full instructions on creating new domains and modules.

=== crud generation ===

# CRUD Generation

When the user asks to create a CRUD or a new module with a table, **activate the `create-crud` skill**.

Before generating anything, ask for:
- Model name (PascalCase singular)
- Domain where it lives
- Fields (name and type)
- Spanish name (singular and plural)

Use the `create-crud` skill for the complete step-by-step generation guide adapted to this boilerplate.

=== livewire/core rules ===

# Livewire 4

- Livewire components are reactive PHP classes — no JavaScript needed for server-driven interactions.
- Use Alpine.js only for pure client-side behavior (toggles, local state, animations).
- State lives on the server; validate and authorize in actions.
- Use `wire:navigate` for SPA-like navigation between pages.
- Use `wire:loading` and `wire:loading.class` for loading states — never `display:none` hacks.
- Livewire Form objects (`extends Form`) handle validation and persistence for forms.
- Use `$this->redirect(route('...'), navigate: true)` after save actions.

## Component Structure

```php
namespace App\Livewire\App\{Domain}\{Module};

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.{domain}.{module}._index');
    }
}
```

=== tallstackui/core rules ===

# TallStackUI v2

TallStackUI provides pre-built Blade components for this project. Use them before building custom UI.

## Available Components (prefix: x-ts-)

- `x-ts-input` — text inputs with label and error display
- `x-ts-card` — content cards with optional header/footer slots
- `x-ts-button` — buttons with loading states
- `x-ts-select.styled` — select con búsqueda y opciones array
- `x-ts-select.native` — select HTML nativo
- `x-ts-modal` — modal dialogs
- `x-ts-toast` — toast notifications (via `$this->toast()->success(...)`)
- `x-ts-badge` — status badges

## Toast Notifications

```php
// In Livewire component (requires Interactions trait)
$this->toast()->success('Título', 'Mensaje')->send();
$this->toast()->success('Éxito', 'Guardado')->flash()->send(); // persists after redirect
$this->toast()->error('Error', 'Mensaje')->send();
```

=== powergrid/core rules ===

# PowerGrid v6

PowerGrid is used for all data tables. Activate the `powergrid-tables` skill when working with tables.

## Key Rules

- Tables extend `PowerGridComponent` — not Livewire\Component directly.
- Always define `public string $tableName` with a unique kebab-case name.
- Use `datasource()` returning an Eloquent Builder, not a Collection.
- Use `withTrashed()` when the model uses SoftDeletes.
- Use `setUp()` to configure Header (search) and Footer (pagination).
- Use `fields()` + `columns()` + `actions()` for column and action definition.
- Use `#[On('eventName')]` for handling dispatched events (soft delete, restore).

Read `.agents/skills/powergrid-tables/SKILL.md` for detailed examples.

=== permissions rules ===

# Roles & Permissions (Spatie)

- Use `spatie/laravel-permission` for all authorization.
- Gate checks use `@can('permission-name')` in Blade or `$this->authorize('permission-name')` in Livewire.
- Route-level protection uses `->middleware('permission:ver {model_es}')`.
- Standard CRUD permissions per module: `ver`, `crear`, `editar`, `eliminar`, `restaurar`.

=== laravel/ai rules (install first: ddev composer require laravel/ai) ===

# Laravel AI SDK

> **Not installed by default.** Install with `ddev composer require laravel/ai` before using any AI feature.
> This boilerplate's IA branch (`feature/ia`) includes this package pre-installed.

Use `laravel/ai` when the user needs AI features: agents, embeddings, image generation, audio, or RAG.

## Agents

```php
use function Laravel\Ai\{agent};

// Anonymous agent (quick)
$response = agent(instructions: 'You are a helpful assistant.')->prompt($input);

// Class-based agent
php artisan make:agent MyAgent
php artisan make:tool MyTool
```

## Key patterns

```php
// Prompt
$response = (new MyAgent)->prompt('...');
return (string) $response;

// Stream (for HTTP responses)
return (new MyAgent)->stream('...')->usingVercelDataProtocol();

// Queue (background)
(new MyAgent)->queue('...')->then(fn ($r) => ...)->catch(fn ($e) => ...);

// Embeddings
$embeddings = Str::of('text')->toEmbeddings();

// Image
$path = Image::of('description')->landscape()->generate()->store();
```

## Rules

- Always use `#[Provider(Lab::Anthropic)]` and `#[Model('claude-sonnet-4-6')]` attributes unless the user specifies otherwise.
- Use `RemembersConversations` trait for multi-turn conversation persistence.
- Use `HasTools` + `tools()` method to give agents access to app data.
- Use `SimilaritySearch` tool for RAG patterns — don't build custom vector search unless needed.
- Always test with `MyAgent::fake()` — never call real AI in tests.

=== laravel/mcp rules ===

# Laravel MCP

Use `laravel/mcp` when exposing app functionality to external AI clients.
Activate `create-mcp` skill for guided generation.

## Key commands

```bash
ddev exec php artisan make:mcp-server {Name}
ddev exec php artisan make:mcp-tool {Name}
ddev exec php artisan make:mcp-resource {Name}
ddev exec php artisan make:mcp-prompt {Name}
ddev exec php artisan vendor:publish --tag=ai-routes   # creates routes/ai.php
```

## Rules

- Register servers in `routes/ai.php` (published via vendor:publish).
- Use `Mcp::local()` for CLI agents (Claude Code), `Mcp::web()` for remote HTTP clients.
- Always annotate tools with `#[Description('...')]`.
- Validate all inputs inside `handle()` with `$request->validate([...])`.
- Prefer `Response::structured()` over text when returning data.

=== laravel/boost rules ===

# Laravel Boost

`laravel/boost` (dev dependency) provides the MCP server that gives the AI agent real introspection tools.

## Priority Rule

**Always use Laravel Boost MCP tools first** for any database, schema, route, config, or app introspection task. Never use manual `ddev exec php artisan tinker` commands when a Boost tool can do the job — Boost avoids shell escaping issues and returns structured data.

## MCP server tools available

- **Application Info** — PHP/Laravel versions, installed packages, Eloquent models
- **Database Schema** — table structure, columns, indexes
- **Database Query** — run read queries against the DB
- **List Routes** — all registered routes with middleware
- **List Artisan Commands** — available commands
- **Search Docs** — semantic search over Laravel 12 docs
- **Tinker** — execute PHP inside the app
- **Get Config** — read config values by dot notation
- **Read Log Entries** — last N log entries
- **Browser Logs** — browser console errors

## MCP configuration

The `.mcp.json` in this project points to `php artisan boost:mcp` via DDEV.
Claude Code picks this up automatically at session start.

## Updating

```bash
ddev exec php artisan boost:update
```

=== boilerplate mcp ===

# Boilerplate MCP Server

The project includes its own MCP server (`boilerplate`) for architecture introspection. Use these tools to inspect the project structure before creating or modifying modules.

## Available Tools

- **ListDomains** — Lists all registered domains with route files, URL prefixes, middleware, and modules.
- **GetModuleStructure** — Returns the complete file structure of a module (model, views, components, routes, permissions, menu).
- **ListPermissions** — Lists all roles, permissions, and assignments from `config/roles.php`.
- **GetMenuStructure** — Returns the sidebar menu structure from `config/menu.php`.

## When to use

- Before creating a new module or CRUD — check existing domains and permissions.
- Before adding menu entries — inspect the current menu structure.
- When debugging module structure — verify which files exist vs which are missing.

</boilerplate-guidelines>
