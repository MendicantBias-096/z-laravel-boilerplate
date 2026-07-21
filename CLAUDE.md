<boilerplate-guidelines>
=== foundation rules ===

# Laravel Boilerplate Guidelines

This is a Laravel boilerplate project. These guidelines must be followed closely to ensure consistency across all development.

## Stack & Versions

- **php** — 8.5+
- **laravel/framework** — v13
- **livewire/livewire** — v4
- **tallstackui/tallstackui** — v3
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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/reverb (REVERB) - v1
- laravel/sanctum (SANCTUM) - v4
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v11
- alpinejs (ALPINEJS) - v3
- laravel-echo (ECHO) - v2
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `livewire-development` — Use for any task or question involving Livewire. Activate if user mentions Livewire, wire: directives, or Livewire-specific concepts like wire:model, wire:click, wire:sort, or islands, invoke this skill. Covers building new components, debugging reactivity issues, real-time form validation, drag-and-drop, loading states, migrating from Livewire 3 to 4, converting component formats (SFC/MFC/class-based), and performance optimization. Do not use for non-Livewire reactive UI (React, Vue, Alpine-only, Inertia.js) or standard Laravel forms without Livewire.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.
- `accessibility` — Quality assurance for web accessibility and usability, particularly for users with disabilities. Use when involved in any web project.
- `address-sanitizer` — AddressSanitizer detects memory errors during fuzzing. Use when fuzzing C/C++ code to find buffer overflows and use-after-free bugs.
- `aflpp` — AFL++ is a fork of AFL with better fuzzing performance and advanced features. Use for multi-core fuzzing of C/C++ projects.
- `agentic-actions-auditor` — Audits GitHub Actions workflows for security vulnerabilities in AI agent integrations including Claude Code Action, Gemini CLI, OpenAI Codex, and GitHub AI Inference. Detects attack vectors where attacker-controlled input reaches AI agents running in CI/CD pipelines, including env var intermediary patterns, direct expression injection, dangerous sandbox configurations, and wildcard user allowlists. Use when reviewing workflow files that invoke AI coding agents, auditing CI/CD pipeline security for prompt injection risks, or evaluating agentic action configurations.
- `algorand-vulnerability-scanner` — Scans Algorand smart contracts for 11 common vulnerabilities including rekeying attacks, unchecked transaction fees, missing field validations, and access control issues. Use when auditing Algorand projects (TEAL/PyTeal).
- `ask-questions-if-underspecified` — Clarify requirements before implementing. Use when serious doubts arise.
- `atheris` — Atheris is a coverage-guided Python fuzzer based on libFuzzer. Use for fuzzing pure Python code and Python C extensions.
- `audit-context-building` — Enables ultra-granular, line-by-line code analysis to build deep architectural context before vulnerability or bug finding.
- `audit-prep-assistant` — Prepares codebases for security review using Trail of Bits' checklist. Helps set review goals, runs static analysis tools, increases test coverage, removes dead code, ensures accessibility, and generates documentation (flowcharts, user stories, inline comments).
- `ckm:banner-design` — Design banners for social media, ads, website heroes, creative assets, and print. Multiple art direction options with AI-generated visuals. Actions: design, create, generate banner. Platforms: Facebook, Twitter/X, LinkedIn, YouTube, Instagram, Google Display, website hero, print. Styles: minimalist, gradient, bold typography, photo-based, illustrated, geometric, retro, glassmorphism, 3D, neon, duotone, editorial, collage. Uses ui-ux-pro-max, frontend-design, ai-artist, ai-multimodal skills.
- `ckm:brand` — Brand voice, visual identity, messaging frameworks, asset management, brand consistency. Activate for branded content, tone of voice, marketing assets, brand compliance, style guides.
- `burpsuite-project-parser` — Searches and explores Burp Suite project files (.burp) from the command line. Use when searching response headers or bodies with regex patterns, extracting security audit findings, dumping proxy history or site map data, or analyzing HTTP traffic captured in a Burp project.
- `cairo-vulnerability-scanner` — Scans Cairo/StarkNet smart contracts for 6 critical vulnerabilities including felt252 arithmetic overflow, L1-L2 messaging issues, address conversion problems, and signature replay. Use when auditing StarkNet projects.
- `cargo-fuzz` — cargo-fuzz is the de facto fuzzing tool for Rust projects using Cargo. Use for fuzzing Rust code with libFuzzer backend.
- `ckm:design` — Comprehensive design skill: brand identity, design tokens, UI styling, logo generation (55 styles, Gemini AI), corporate identity program (50 deliverables, CIP mockups), HTML presentations (Chart.js), banner design (22 styles, social/ads/web/print), icon design (15 styles, SVG, Gemini 3.1 Pro), social photos (HTML→screenshot, multi-platform). Actions: design logo, create CIP, generate mockups, build slides, design banner, generate icon, create social photos, social media images, brand identity, design system. Platforms: Facebook, Twitter, LinkedIn, YouTube, Instagram, Pinterest, TikTok, Threads, Google Ads.
- `ckm:design-system` — Token architecture, component specifications, and slide generation. Three-layer tokens (primitive→semantic→component), CSS variables, spacing/typography scales, component specs, strategic slide creation. Use for design tokens, systematic design, brand-compliant presentations.
- `ckm:slides` — Create strategic HTML presentations with Chart.js, design tokens, responsive layouts, copywriting formulas, and contextual slide strategies.
- `ckm:ui-styling` — Create beautiful, accessible user interfaces with shadcn/ui components (built on Radix UI + Tailwind), Tailwind CSS utility-first styling, and canvas-based visual designs. Use when building user interfaces, implementing design systems, creating responsive layouts, adding accessible components (dialogs, dropdowns, forms, tables), customizing themes and colors, implementing dark mode, generating visual designs and posters, or establishing consistent styling patterns across applications.
- `claude-in-chrome-troubleshooting` — Diagnose and fix Claude in Chrome MCP extension connectivity issues. Use when mcp__claude-in-chrome__* tools fail, return "Browser extension is not connected", or behave erratically.
- `code-maturity-assessor` — Systematic code maturity assessment using Trail of Bits' 9-category framework. Analyzes codebase for arithmetic safety, auditing practices, access controls, complexity, decentralization, documentation, MEV risks, low-level code, and testing. Produces professional scorecard with evidence-based ratings and actionable recommendations.
- `codeql` — Scans a codebase for security vulnerabilities using CodeQL's interprocedural data flow and taint tracking analysis. Triggers on "run codeql", "codeql scan", "codeql analysis", "build codeql database", or "find vulnerabilities with codeql". Supports "run all" (security-and-quality suite) and "important only" (high-precision security findings) scan modes. Also handles creating data extension models and processing CodeQL SARIF output.
- `constant-time-analysis` — Detects timing side-channel vulnerabilities in cryptographic code. Use when implementing or reviewing crypto code, encountering division on secrets, secret-dependent branches, or constant-time programming questions in C, C++, Go, Rust, Swift, Java, Kotlin, C#, PHP, JavaScript, TypeScript, Python, or Ruby.
- `constant-time-testing` — Constant-time testing detects timing side channels in cryptographic code. Use when auditing crypto implementations for timing vulnerabilities.
- `cosmos-vulnerability-scanner` — Scans Cosmos SDK blockchains for 9 consensus-critical vulnerabilities including non-determinism, incorrect signers, ABCI panics, and rounding errors. Use when auditing Cosmos chains or CosmWasm contracts.
- `coverage-analysis` — Coverage analysis measures code exercised during fuzzing. Use when assessing harness effectiveness or identifying fuzzing blockers.
- `debug-buttercup` — Debugs the Buttercup CRS (Cyber Reasoning System) running on Kubernetes. Use when diagnosing pod crashes, restart loops, Redis failures, resource pressure, disk saturation, DinD issues, or any service misbehavior in the crs namespace. Covers triage, log analysis, queue inspection, and common failure patterns for: redis, fuzzer-bot, coverage-bot, seed-gen, patcher, build-bot, scheduler, task-server, task-downloader, program-model, litellm, dind, tracer-bot, merger-bot, competition-api, pov-reproducer, scratch-cleaner, registry-cache, image-preloader, ui.
- `designing-workflow-skills` — Guides the design and structuring of workflow-based Claude Code skills with multi-step phases, decision trees, subagent delegation, and progressive disclosure. Use when creating skills that involve sequential pipelines, routing patterns, safety gates, task tracking, phased execution, or any multi-step workflow. Also applies when reviewing or refactoring existing workflow skills for quality.
- `devcontainer-setup` — Creates devcontainers with Claude Code, language-specific tooling (Python/Node/Rust/Go), and persistent volumes. Use when adding devcontainer support to a project, setting up isolated development environments, or configuring sandboxed Claude Code workspaces.
- `differential-review` — Performs security-focused differential review of code changes (PRs, commits, diffs). Adapts analysis depth to codebase size, uses git history for context, calculates blast radius, checks test coverage, and generates comprehensive markdown reports. Automatically detects and prevents security regressions.

- `dimensional-analysis` — Annotates codebases with dimensional analysis comments documenting units, dimensions, and decimal scaling. Use when someone asks to annotate units in a codebase, perform a dimensional analysis, or find vulnerabilities in a DeFi protocol, offchain code, or other blockchain-related codebase with arithmetic. Prevents dimensional mismatches and catches formula bugs early.
- `dwarf-expert` — Provides expertise for analyzing DWARF debug files and understanding the DWARF debug format/standard (v3-v5). Triggers when understanding DWARF information, interacting with DWARF files, answering DWARF-related questions, or working with code that parses DWARF data.
- `entry-point-analyzer` — Analyzes smart contract codebases to identify state-changing entry points for security auditing. Detects externally callable functions that modify state, categorizes them by access level (public, admin, role-restricted, contract-only), and generates structured audit reports. Excludes view/pure/read-only functions. Use when auditing smart contracts (Solidity, Vyper, Solana/Rust, Move, TON, CosmWasm) or when asked to find entry points, audit flows, external functions, access control patterns, or privileged operations.
- `firebase-apk-scanner` — Scans Android APKs for Firebase security misconfigurations including open databases, storage buckets, authentication issues, and exposed cloud functions. Use when analyzing APK files for Firebase vulnerabilities, performing mobile app security audits, or testing Firebase endpoint security. For authorized security research only.
- `fp-check` — Systematically verifies suspected security bugs to eliminate false positives. Produces TRUE POSITIVE or FALSE POSITIVE verdicts with documented evidence for each bug.
- `fuzzing-dictionary` — Fuzzing dictionaries guide fuzzers with domain-specific tokens. Use when fuzzing parsers, protocols, or format-specific code.
- `fuzzing-obstacles` — Techniques for patching code to overcome fuzzing obstacles. Use when checksums, global state, or other barriers block fuzzer progress.
- `gh-cli` — Enforces authenticated gh CLI workflows over unauthenticated curl/WebFetch patterns. Use when working with GitHub URLs, API access, pull requests, or issues.
- `git-cleanup` — Safely analyzes and cleans up local git branches and worktrees by categorizing them as merged, squash-merged, superseded, or active work.
- `guidelines-advisor` — Smart contract development advisor based on Trail of Bits' best practices. Analyzes codebase to generate documentation/specifications, review architecture, check upgradeability patterns, assess implementation quality, identify pitfalls, review dependencies, and evaluate testing. Provides actionable recommendations.
- `harness-writing` — Techniques for writing effective fuzzing harnesses across languages. Use when creating new fuzz targets or improving existing harness code.
- `insecure-defaults` — Detects fail-open insecure defaults (hardcoded secrets, weak auth, permissive security) that allow apps to run insecurely in production. Use when auditing security, reviewing config management, or analyzing environment variable handling.
- `interpreting-culture-index` — Interprets Culture Index (CI) surveys, behavioral profiles, and personality assessment data. Supports individual profile interpretation, team composition analysis (gas/brake/glue), burnout detection, profile comparison, hiring profiles, manager coaching, interview transcript analysis for trait prediction, candidate debrief, onboarding planning, and conflict mediation. Accepts extracted JSON or PDF input via OpenCV extraction script.
- `let-fate-decide` — Draws 4 Tarot cards using os.urandom() to inject entropy into planning when prompts are vague or underspecified. Interprets the spread to guide next steps. Use when the user is nonchalant, feeling lucky, says 'let fate decide', makes Yu-Gi-Oh references ('heart of the cards'), demonstrates indifference about approach, or says 'try again' on a system with no changes. Also triggers on sufficiently ambiguous prompts where multiple approaches are equally valid.
- `libafl` — LibAFL is a modular fuzzing library for building custom fuzzers. Use for advanced fuzzing needs, custom mutators, or non-standard fuzzing targets.
- `libfuzzer` — Coverage-guided fuzzer built into LLVM for C/C++ projects. Use for fuzzing C/C++ code that can be compiled with Clang.
- `modern-python` — Configures Python projects with modern tooling (uv, ruff, ty). Use when creating projects, writing standalone scripts, or migrating from pip/Poetry/mypy/black.
- `ossfuzz` — OSS-Fuzz provides free continuous fuzzing for open source projects. Use when setting up continuous fuzzing infrastructure or enrolling projects.
- `property-based-testing` — Provides guidance for property-based testing across multiple languages and smart contracts. Use when writing tests, reviewing code with serialization/validation/parsing patterns, designing features, or when property-based testing would provide stronger coverage than example-based tests.
- `ruzzy` — Ruzzy is a coverage-guided Ruby fuzzer by Trail of Bits. Use for fuzzing pure Ruby code and Ruby C extensions.
- `sarif-parsing` — Parses and processes SARIF files from static analysis tools like CodeQL, Semgrep, or other scanners. Triggers on "parse sarif", "read scan results", "aggregate findings", "deduplicate alerts", or "process sarif output". Handles filtering, deduplication, format conversion, and CI/CD integration of SARIF data. Does NOT run scans — use the Semgrep or CodeQL skills for that.
- `seatbelt-sandboxer` — Generates minimal macOS Seatbelt sandbox configurations. Use when sandboxing, isolating, or restricting macOS applications with allowlist-based profiles.
- `second-opinion` — Runs external LLM code reviews (OpenAI Codex or Google Gemini CLI) on uncommitted changes, branch diffs, or specific commits. Use when the user asks for a second opinion, external review, codex review, gemini review, or mentions /second-opinion.
- `secure-workflow-guide` — Guides through Trail of Bits' 5-step secure development workflow. Runs Slither scans, checks special features (upgradeability/ERC conformance/token integration), generates visual security diagrams, helps document security properties for fuzzing/verification, and reviews manual security areas.
- `semgrep` — Run Semgrep static analysis scan on a codebase using parallel subagents. Supports two scan modes — "run all" (full ruleset coverage) and "important only" (high-confidence security vulnerabilities). Automatically detects and uses Semgrep Pro for cross-file taint analysis when available. Use when asked to scan code for vulnerabilities, run a security audit with Semgrep, find bugs, or perform static analysis. Spawns parallel workers for multi-language codebases.
- `semgrep-rule-creator` — Creates custom Semgrep rules for detecting security vulnerabilities, bug patterns, and code patterns. Use when writing Semgrep rules or building custom static analysis detections.
- `semgrep-rule-variant-creator` — Creates language variants of existing Semgrep rules. Use when porting a Semgrep rule to specified target languages. Takes an existing rule and target languages as input, produces independent rule+test directories for each language.
- `sharp-edges` — Identifies error-prone APIs, dangerous configurations, and footgun designs that enable security mistakes. Use when reviewing API designs, configuration schemas, cryptographic library ergonomics, or evaluating whether code follows 'secure by default' and 'pit of success' principles. Triggers: footgun, misuse-resistant, secure defaults, API usability, dangerous configuration.
- `skill-improver` — Iteratively reviews and fixes Claude Code skill quality issues until they meet standards. Runs automated fix-review cycles using the skill-reviewer agent. Use to fix skill quality issues, improve skill descriptions, run automated skill review loops, or iteratively refine a skill. Triggers on 'fix my skill', 'improve skill quality', 'skill improvement loop'. NOT for one-time reviews—use /skill-reviewer directly.
- `solana-vulnerability-scanner` — Scans Solana programs for 6 critical vulnerabilities including arbitrary CPI, improper PDA validation, missing signer/ownership checks, and sysvar spoofing. Use when auditing Solana/Anchor programs.
- `spec-to-code-compliance` — Verifies code implements exactly what documentation specifies for blockchain audits. Use when comparing code against whitepapers, finding gaps between specs and implementation, or performing compliance checks for protocol implementations.
- `substrate-vulnerability-scanner` — Scans Substrate/Polkadot pallets for 7 critical vulnerabilities including arithmetic overflow, panic DoS, incorrect weights, and bad origin checks. Use when auditing Substrate runtimes or FRAME pallets.
- `supply-chain-risk-auditor` — Identifies dependencies at heightened risk of exploitation or takeover. Use when assessing supply chain attack surface, evaluating dependency health, or scoping security engagements.
- `testing-handbook-generator` — Meta-skill that analyzes the Trail of Bits Testing Handbook (appsec.guide) and generates Claude Code skills for security testing tools and techniques. Use when creating new skills based on handbook content.
- `token-integration-analyzer` — Token integration and implementation analyzer based on Trail of Bits' token integration checklist. Analyzes token implementations for ERC20/ERC721 conformity, checks for 20+ weird token patterns, assesses contract composition and owner privileges, performs on-chain scarcity analysis, and evaluates how protocols handle non-standard tokens. Context-aware for both token implementations and token integrations.
- `ton-vulnerability-scanner` — Scans TON (The Open Network) smart contracts for 3 critical vulnerabilities including integer-as-boolean misuse, fake Jetton contracts, and forward TON without gas checks. Use when auditing FunC contracts.
- `ui-ux-pro-max` — UI/UX design intelligence for web and mobile. Includes 50+ styles, 161 color palettes, 57 font pairings, 161 product types, 99 UX guidelines, and 25 chart types across 10 stacks (React, Next.js, Vue, Svelte, SwiftUI, React Native, Flutter, Tailwind, shadcn/ui, and HTML/CSS). Actions: plan, build, create, design, implement, review, fix, improve, optimize, enhance, refactor, and check UI/UX code. Projects: website, landing page, dashboard, admin panel, e-commerce, SaaS, portfolio, blog, and mobile app. Elements: button, modal, navbar, sidebar, card, table, form, and chart. Styles: glassmorphism, claymorphism, minimalism, brutalism, neumorphism, bento grid, dark mode, responsive, skeuomorphism, and flat design. Topics: color systems, accessibility, animation, layout, typography, font pairing, spacing, interaction states, shadow, and gradient. Integrations: shadcn/ui MCP for component search and examples.
- `variant-analysis` — Find similar vulnerabilities and bugs across codebases using pattern-based analysis. Use when hunting bug variants, building CodeQL/Semgrep queries, analyzing security vulnerabilities, or performing systematic code audits after finding an initial issue.
- `wycheproof` — Wycheproof provides test vectors for validating cryptographic implementations. Use when testing crypto code for known attacks and edge cases.
- `yara-rule-authoring` — Guides authoring of high-quality YARA-X detection rules for malware identification. Use when writing, reviewing, or optimizing YARA rules. Covers naming conventions, string selection, performance optimization, migration from legacy YARA, and false positive reduction. Triggers on: YARA, YARA-X, malware detection, threat hunting, IOC, signature, crx module, dex module.
- `zeroize-audit` — Detects missing zeroization of sensitive data in source code and identifies zeroization removed by compiler optimizations, with assembly-level analysis, and control-flow verification. Use for auditing C/C++/Rust code handling secrets, keys, passwords, or other sensitive data.

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

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

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
