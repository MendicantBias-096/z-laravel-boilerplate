<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetUserDetailsTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Returns full details of a specific user: name, email, roles and permissions.
                Use this when the user asks about a specific person or account.';
    }

    public function handle(Request $request): Stringable|string
    {
        $user = User::where('email', $request['email'])
            ->orWhere('id', $request['identifier'])
            ->first();

        if (! $user) {
            return 'User not found.';
        }

        return json_encode([
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'created_at'  => $user->created_at->toDateString(),
            'roles'       => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'identifier' => $schema->string()
                ->description('The user ID or email address to look up.')
                ->required(),
        ];
    }
}
