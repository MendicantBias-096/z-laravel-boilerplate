<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Description('
    Grants an existing permission to a role. Only call this when the user
    has explicitly confirmed which permission and which role to modify.
    The permission and role must already exist — this does not create them.
')]
class AssignPermissionToRoleTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'role'       => 'required|string|exists:roles,name',
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $role       = Role::findByName($validated['role']);
        $permission = Permission::findByName($validated['permission']);

        if ($role->hasPermissionTo($permission)) {
            return Response::text("Role \"{$role->name}\" already has permission \"{$permission->name}\".");
        }

        $role->givePermissionTo($permission);

        return Response::structured([
            'result'     => 'success',
            'role'       => $role->name,
            'permission' => $permission->name,
            'message'    => "Permission \"{$permission->name}\" granted to role \"{$role->name}\".",
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'role' => $schema->string()
                ->description('The name of the role to grant the permission to.')
                ->required(),
            'permission' => $schema->string()
                ->description('The exact name of the permission to grant (e.g. "ver productos").')
                ->required(),
        ];
    }
}
