<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists all roles, permissions, and their assignments from config/roles.php. Returns the complete permission map: roles with display names, module-to-permission mappings, role-to-module assignments, and module groups. Use this before creating or modifying permissions for a module.')]
class ListPermissionsTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $roles = config('roles.roles', []);
        $rolesModules = config('roles.roles_modules', []);
        $permissions = config('roles.permissions', []);
        $moduleGroups = config('roles.module_groups', []);

        // Build a complete map of which roles have which permissions
        $rolePermissionMap = [];
        foreach ($roles as $roleKey => $roleLabel) {
            $assignedModules = $rolesModules[$roleKey] ?? [];
            $assignedPermissions = [];

            foreach ($assignedModules as $moduleName) {
                if (isset($permissions[$moduleName])) {
                    $assignedPermissions[$moduleName] = $permissions[$moduleName];
                }
            }

            $rolePermissionMap[$roleKey] = [
                'display_name' => $roleLabel,
                'assigned_modules' => $assignedModules,
                'permissions' => $assignedPermissions,
            ];
        }

        return Response::structured([
            'roles' => $rolePermissionMap,
            'all_permissions' => $permissions,
            'module_groups' => $moduleGroups,
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
