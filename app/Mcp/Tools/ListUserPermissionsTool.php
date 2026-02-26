<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('
    Returns all roles and permissions assigned to a specific user.
    Use this to understand what a user can or cannot do in the app.
    Call this before diagnosing permission-related bugs.
')]
class ListUserPermissionsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);

        return Response::structured([
            'user'        => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'roles'       => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()
                ->description('The ID of the user to inspect.')
                ->required(),
        ];
    }
}
