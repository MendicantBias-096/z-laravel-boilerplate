---
name: create-mcp
description: Creates a Laravel MCP server with tools, resources and prompts using laravel/mcp.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to activate

Activate when the user wants to expose app functionality to AI clients via MCP.
Trigger phrases: "crear servidor mcp", "nuevo mcp", "exponer al agente", "create mcp", "mcp tool".

---

## Required variables — ask if missing

| Variable | Description | Example |
|---|---|---|
| `{Server}` | PascalCase server name | `ProductsServer` |
| `{server-slug}` | kebab-case (URL / artisan) | `products` |
| `{Tools}` | Tools to include | `ListProductsTool, CreateProductTool` |

---

## Execution plan

1. Artisan commands
2. Server class
3. Tool class(es)
4. Register in `routes/ai.php`
5. (Optional) Resource or Prompt
6. Commit

---

## Step 1 — Artisan commands

```bash
ddev exec php artisan make:mcp-server {Server}
ddev exec php artisan make:mcp-tool {Tool}
# ddev exec php artisan make:mcp-resource {Resource}
# ddev exec php artisan make:mcp-prompt {Prompt}
```

Publish routes file if missing:

```bash
ddev exec php artisan vendor:publish --tag=ai-routes
```

---

## Step 2 — Server `app/Mcp/Servers/{Server}.php`

```php
<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\{Tool};
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('{Server}')]
#[Version('1.0.0')]
#[Instructions('Describe what this server does and what tools are available.')]
class {Server} extends Server
{
    protected array $tools = [
        {Tool}::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
```

---

## Step 3 — Tool `app/Mcp/Tools/{Tool}.php`

```php
<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Describe what this tool does.')]
class {Tool} extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'param' => 'required|string',
        ]);

        // business logic here...

        return Response::text('Result');
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'param' => $schema->string()
                ->description('Description of the parameter.')
                ->required(),
        ];
    }
}
```

### Response types

```php
return Response::text('Plain text result');
return Response::structured(['key' => 'value']);
return Response::error('Something went wrong.');
return Response::fromStorage('path/to/file.png');   // image/audio
```

### Conditional registration (e.g., subscription required)

```php
public function shouldRegister(Request $request): bool
{
    return $request?->user()?->subscribed() ?? false;
}
```

---

## Step 4 — Register in `routes/ai.php`

```php
use App\Mcp\Servers\{Server};
use Laravel\Mcp\Facades\Mcp;

// Web (HTTP) — for remote AI clients
Mcp::web('/mcp/{server-slug}', {Server}::class)
    ->middleware(['auth:sanctum', 'throttle:mcp']);

// Local (stdio) — for Claude Code / CLI agents
Mcp::local('{server-slug}', {Server}::class);
```

---

## Step 5 — (Optional) Resource

```php
<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Uri('{server-slug}://resources/data')]
#[MimeType('text/plain')]
#[Description('Exposes static context for AI clients.')]
class {Resource} extends Resource
{
    public function handle(Request $request): Response
    {
        return Response::text('Context data here...');
    }
}
```

---

## Step 6 — Commit

```
feat: MCP server {Server}

- Server `{Server}` con herramientas: {Tools}
- Rutas en routes/ai.php (web + local)
```

---

## Rules

- Always annotate tools with `#[Description('...')]` — the AI uses this to decide which tool to call.
- Validate all inputs inside `handle()` using `$request->validate([...])`.
- Return `Response::error()` for recoverable errors; throw exceptions for unrecoverable ones.
- Use `Mcp::local()` for CLI agents (Claude Code), `Mcp::web()` for remote HTTP clients.
- Prefer `Response::structured()` when returning structured data — it's more reliable than parsing text.
