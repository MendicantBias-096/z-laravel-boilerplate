<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetMenuStructureTool;
use App\Mcp\Tools\GetModuleStructureTool;
use App\Mcp\Tools\ListDomainsTool;
use App\Mcp\Tools\ListPermissionsTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Boilerplate')]
#[Version('1.0.0')]
#[Instructions('Introspection server for the Laravel Boilerplate. Provides tools to inspect the project architecture: domains, modules, permissions, and menu structure. Use these tools to understand the project before creating or modifying modules, CRUDs, or permissions.')]
class BoilerplateServer extends Server
{
    protected array $tools = [
        ListDomainsTool::class,
        GetModuleStructureTool::class,
        ListPermissionsTool::class,
        GetMenuStructureTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
