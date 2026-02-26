<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AssignPermissionToRoleTool;
use App\Mcp\Tools\ListRoleUsersTool;
use App\Mcp\Tools\ListUserPermissionsTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Permissions Server')]
#[Version('1.0.0')]
#[Instructions('
    Provides tools to inspect and manage users, roles and permissions
    using Spatie Laravel Permission.
    - ListUserPermissionsTool: see what a specific user can do.
    - ListRoleUsersTool: see which users belong to a role.
    - AssignPermissionToRoleTool: grant a permission to a role.
')]
class PermissionsServer extends Server
{
    protected array $tools = [
        ListUserPermissionsTool::class,
        ListRoleUsersTool::class,
        AssignPermissionToRoleTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}
