<?php

namespace App\Ai\Tools;

use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stringable;

class GetAppStatsTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Returns general statistics about the application: total users,
                roles, permissions and recently registered users.
                Use this when the user asks about the overall state of the app.';
    }

    public function handle(Request $request): Stringable|string
    {
        $stats = [
            'total_users'       => User::count(),
            'active_users'      => User::whereNull('deleted_at')->count(),
            'total_roles'       => Role::count(),
            'total_permissions' => Permission::count(),
            'recent_users'      => User::latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'created_at'])
                ->toArray(),
        ];

        return json_encode($stats);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
