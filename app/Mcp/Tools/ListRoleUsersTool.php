<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Spatie\Permission\Models\Role;

#[Description('
    Returns all users that belong to a given role, along with
    the permissions that role has. Use this to audit who has
    access to a specific area or feature.
')]
class ListRoleUsersTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $role = Role::findByName($validated['role']);

        return Response::structured([
            'role'        => $role->name,
            'permissions' => $role->permissions->pluck('name'),
            'users'       => $role->users->map(fn ($u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
            ]),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'role' => $schema->string()
                ->description('The name of the role to inspect (e.g. "admin", "editor").')
                ->required(),
        ];
    }
}
