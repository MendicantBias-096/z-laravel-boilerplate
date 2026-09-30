<?php

declare(strict_types=1);

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\BoilerplateServer;
use App\Mcp\Tools\GetMenuStructureTool;
use App\Mcp\Tools\ListDomainsTool;
use App\Mcp\Tools\ListPermissionsTool;
use Tests\TestCase;

/**
 * Con laravel/mcp 1.0 `Response::structured()` devuelve un ResponseFactory:
 * un tool que declara `Response` le devuelve al agente un TypeError.
 */
class IntrospectionToolsTest extends TestCase
{
    public function test_list_domains_responds(): void
    {
        BoilerplateServer::tool(ListDomainsTool::class)->assertOk();
    }

    public function test_list_permissions_responds(): void
    {
        BoilerplateServer::tool(ListPermissionsTool::class)->assertOk();
    }

    public function test_get_menu_structure_responds(): void
    {
        BoilerplateServer::tool(GetMenuStructureTool::class)->assertOk();
    }
}
