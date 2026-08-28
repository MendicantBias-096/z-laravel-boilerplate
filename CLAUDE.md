<boilerplate-guidelines>
=== foundation rules ===

# Laravel Boilerplate Guidelines

This is a Laravel boilerplate project. These guidelines must be followed closely to ensure consistency across all development.

## Stack & Versions

- **php** — 8.5+
- **laravel/framework** — v13
- **livewire/livewire** — v4
- **tallstackui/tallstackui** — v3
- **spatie/laravel-permission** — v8
- **spatie/laravel-medialibrary** — v11
- **diglactic/laravel-breadcrumbs** — v10
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

## Conventions

- Follow existing code conventions in sibling files before writing new code.
- Check for existing components to reuse before creating new ones.
- Never use `->layout()` in Livewire `render()` methods — layout is set in the wrapper view.
- Never point routes directly to Livewire classes — always use `fn () => view(...)`.
- All Livewire component classes live inside their module: `App\Modules\{Context}\Livewire\{Resource}\{Name}.php`.
- Never use `env()` outside of config files — always use `config('key')`.

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

## Instantiating this boilerplate as a new project

Copying the folder is not enough — these values stay pointed at the boilerplate
and cause silent breakage. Do all of it before the first `ddev start`:

1. **`.ddev/config.yaml`** — `name:` _and_ every value under `web_environment`
   (`APP_URL`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `REVERB_HOST`,
   `VITE_*`). DDEV exports these as real container env vars and **they win over
   `.env`**, so a stale `APP_URL` here silently poisons every generated URL
   while `.env` looks correct.
2. **`.ddev/traefik`** — delete it, and reassign the ports in
   `config.local.yaml`, or `ddev-router` fails against the other projects.
3. **`hooks.post-stop`** — rewrites `.env` with Herd values on every
   `ddev stop`. Drop it unless the project actually runs under Herd.
4. **`.env`** — copy from `.env.example` (not from another project) so keys like
   `TALLSTACKUI_PREFIX=ts-` are present; without it every `x-ts-*` component
   throws `Unable to locate a class or view for component`.
5. **Postgres** — create the renamed database and role, plus `laravel_testing`
   for the suite.

=== laravel/core rules ===

# Laravel Conventions (project-specific)

- Use Eloquent models and relationships before suggesting raw queries.
- Avoid `DB::` — prefer `Model::query()`.
- Prevent N+1 problems with eager loading.
- Use Form Request classes for validation — never inline validation in controllers.
- Use queued jobs with `ShouldQueue` for time-consuming operations.

## Model keys: UUID for domain models

**A new domain model keys on UUID.** Three pieces that change together or not
at all:

```php
$table->uuid('id')->primary();   // migration
use HasUuids;                    // model
public ?string $id = null;       // Form object
```

`create-crud` already generates all three. Integer and ULID keys also work —
see below — but UUID is what a new module gets unless there is a reason.

**What stays on integers**, deliberately: `users`, `roles` and everything from
Spatie, plus the infrastructure tables (`jobs`, `cache`, `sessions`,
`personal_access_tokens`). Changing those buys nothing and breaks third-party
packages that expect an integer key.

### Why UUID and not the integer

The evidence is in the projects, not in taste. In dayacount, **55 of 65 tables
use UUID** — and the 10 integer ones are exactly the tables inherited from this
boilerplate. Every table anyone wrote by hand chose UUID.

A default that gets overridden on its first use is not a default, it is a toll.
So the boilerplate now ships the format its own projects already pick.

What that format buys, concretely:

- **Ids stop being enumerable.** `/facturas/1` and `/facturas/2` leak how many
  records exist and invite walking the range. A UUID does not.
- **They can be generated outside the database** — in a job, in a queued import,
  in a client — without a round trip to get the id back.
- Laravel's `HasUuids` emits **UUID v7**, which is ordered by time, so it does
  not fragment the index the way a v4 would.

The costs are real and accepted: 36 characters instead of a handful, uglier in
logs and URLs, and slightly heavier indexes and joins.

### All three formats are supported

Integer, UUID (`HasUuids`) and ULID (`HasUlids`) all work with no configuration:
the row actions in `App\Traits\Livewire\HasSoftDeletes` type the id as
`string|int`, and `<x-ui.ts-table.actions>` sends it to the browser through
`Js::from()`. Covered by `app/Modules/Access/Tests/Feature/Users/ClaveNoNumericaTest.php` —
reverting either half turns it red, which is the point: it used to be possible
to break both and keep the suite green.

**Two rules when you write your own row actions.** Integer keys satisfy both by
accident, so a mistake only surfaces on the first non-numeric table:

```php
// Type ids as string|int, never int. They arrive as text from the browser.
public function softDelete(string|int $id): void
```

```blade
{{-- Send the id through Js::from(), never interpolated bare. Livewire
     evaluates wire:click as an expression, so a bare UUID dies in a
     SyntaxError before it leaves the browser. --}}
wire:click="confirmDelete({{ \Illuminate\Support\Js::from($row->id) }})"
```

Pick ULID over UUID when the id is going to be read aloud, typed by hand or put
in a URL people copy: same properties, 26 characters, no hyphens.

## Laravel 12 Structure

- Middleware is configured in `bootstrap/app.php` via `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` registers middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains service providers.
- Console commands in `app/Console/Commands/` are auto-registered.

=== architecture rules ===

# Project Architecture

**The architecture rules live in `docs/ARCHITECTURE_RULES.md`.** 56 numbered,
citable rules (R1–R56) covering module boundaries, data ownership, migrations,
authorization, queues and code form. Read it before designing anything new, and
cite rules by ID in review ("this violates R13"). It is also served in-app at
`/docs`.

The structure below **is** the one the rules describe: the migration to
`app/Modules/` is done. What is not done yet is named at the end.

## Modules

Two platform modules that every product inherits (R5):

| Module | What lives there |
| --- | --- |
| `Access` | identity: users, roles, permissions, profiles, authentication |
| `Platform` | configuration, notifications, shared UI pieces and validation rules |

Business modules go alongside them and prefix their URLs (`/billing/invoices`);
platform modules do not (`/users`, not `/access/users`).

## Layout of a module

```
app/Modules/{Context}/
├── Contracts/              public (R8): interfaces, their DTOs and exceptions
├── Events/                 public (R8)
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

Folders are **by type, not by layer**: a notification goes in `Notifications/`
and there is nothing to decide. The list is closed — `scripts/arch-lint.sh`
checks it (R6), so a new type is added there and to R6, not invented in a
module.

## Three-Layer Convention

Unchanged, only namespaced:

```
Route        →  Route::view('/users', 'access::users.index')   never a Livewire class
Wrapper      →  Resources/views/users/index.blade.php          <x-layouts.app> + @livewire(...)
Component    →  Resources/views/users/_index.blade.php         actual HTML, no layout
Livewire     →  Livewire/Users/Table.php                       returns view('access::users._index')
```

## Naming Conventions

| Element | Convention | Example |
| --- | --- | --- |
| Livewire namespace | `App\Modules\{Context}\Livewire` | `App\Modules\Access\Livewire\Users` |
| View / component | `{context}::{resource}.{name}` | `access::users.index` |
| Route name | `{context}.{resource}.{action}` | `access.users.index` |
| Route file | `app/Modules/{Context}/Routes/web.php` | — |
| URL | kebab-case, no prefix for platform | `/users` |
| Table | `{context}_{plural}` | `access_profiles` |
| Permission | `{context}.{resource}.{action}` in English | `access.users.view` |

## What a module ServiceProvider must register

Laravel discovers by path convention, and inside a module that convention no
longer applies. Each provider declares: migrations, views, translations,
routes, breadcrumbs, config, Livewire namespace, morph map and console
commands. Policies are the one exception — `Gate::guessPolicyName()` finds
them. The provider itself goes in `bootstrap/providers.php`.

Two traps worth knowing, both silent:

- **Livewire** components are registered with `addNamespace`, not
  `addLocation`: the latter derives the name by trimming the prefix, so two
  modules with the same subfolder produce the same name and the first one
  registered wins, without an error.
- **Polymorphic columns** (`model_type`, `notifiable_type`, …) store the FQCN
  as text, so renaming a namespace silently orphans those rows. A morph map
  keeps an alias in the database instead. `php artisan access:migrate-morph-types`
  rewrites rows written before the map existed.

## Still pending

R33 — that no foreign key crosses a module boundary — has no check yet: it
needs the table→module map that R25's prefix now makes possible.

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
namespace App\Modules\{Context}\Livewire\{Resource};

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

# TallStackUI v3

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

=== permissions rules ===

# Roles & Permissions (Spatie)

- Use `spatie/laravel-permission` for all authorization.
- Gate checks use `@can('permission-name')` in Blade or `$this->authorize('permission-name')` in Livewire.
- Route-level protection uses `->middleware('permission:ver {model_es}')`.
- Standard CRUD permissions per module: `ver`, `crear`, `editar`, `eliminar`, `restaurar`.

=== laravel/ai rules (install first: ddev composer require laravel/ai) ===

# Laravel AI SDK

> **Not installed by default.** Install with `ddev composer require laravel/ai` before using any AI feature.
> No branch ships it pre-installed — `feature/ia` is stale pre-squash history (66 commits behind
> `trunk`, never had `laravel/ai` in `composer.json`); do not branch off it.
> What _is_ here by default: `laravel/mcp` plus the introspection server in `app/Mcp` (see below).

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

- Always use `#[Provider(Lab::Anthropic)]` and `#[Model('claude-opus-4-8')]` attributes unless the user specifies otherwise. Never hardcode a model ID from memory — a stale one fails at runtime with a 404, not at boot. Check the current IDs first (the `claude-api` skill lists them).
- `config/ai.php` ships with `'default' => 'openai'`. Publish it (`vendor:publish --tag=ai-config`) and switch the default to `anthropic`, or every call without an explicit provider hits the wrong lab.
- Use `RemembersConversations` trait for multi-turn conversation persistence.
- Use `HasTools` + `tools()` method to give agents access to app data.
- Use `SimilaritySearch` tool for RAG patterns — don't build custom vector search unless needed.
- Always test with `MyAgent::fake()` — never call real AI in tests. `fake()` returns the gateway; the assertions (`assertAgentWasPrompted`, …) live on the `Ai` facade.

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

**Always use Laravel Boost MCP tools first** for any database, schema, route, config, or app introspection task. Never use manual `ddev exec php artisan tinker` commands when a Boost tool can do the job — Boost avoids shell escaping issues and returns structured data.

The `.mcp.json` in this project points to `php artisan boost:mcp` via DDEV. Update with `ddev exec php artisan boost:update`.

=== boilerplate mcp ===

# Boilerplate MCP Server

The project includes its own MCP server (`boilerplate`) for architecture introspection. Use these tools to inspect the project structure before creating or modifying modules.

## Available Tools

- **ListDomains** — Lists all registered domains with route files, URL prefixes, middleware, and modules.
- **GetModuleStructure** — Returns the complete file structure of a module (model, views, components, routes, permissions, menu).
- **ListPermissions** — Lists all roles, permissions, and assignments from `app/Modules/Access/Config/permissions.php` (merged as `config('roles')`).
- **GetMenuStructure** — Returns the sidebar menu structure from `config/menu.php`.

## When to use

- Before creating a new module or CRUD — check existing domains and permissions.
- Before adding menu entries — inspect the current menu structure.
- When debugging module structure — verify which files exist vs which are missing.

</boilerplate-guidelines>

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/reverb (REVERB) - v1
- laravel/sanctum (SANCTUM) - v4
- livewire/livewire (LIVEWIRE) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12
- rector/rector (RECTOR) - v2
- alpinejs (ALPINEJS) - v3
- laravel-echo (ECHO) - v2
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `bun run build`, `bun run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
    - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `bun run build` or ask the user to run `bun run dev` or `composer run dev`.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
