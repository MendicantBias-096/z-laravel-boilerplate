<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\File;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists all registered domains in the boilerplate with their route files, URL prefixes, middleware, and modules. Use this to understand the project structure before creating or modifying modules.')]
class ListDomainsTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $domains = [];

        // Parse bootstrap/app.php to find route registrations
        $bootstrapPath = base_path('bootstrap/app.php');
        $bootstrapContent = File::get($bootstrapPath);

        // Find route files registered in the "then" callback
        $routeFiles = glob(base_path('routes/*.php'));

        // Known system route files that are NOT domains
        $systemRoutes = ['web.php', 'api.php', 'console.php', 'channels.php', 'breadcrumbs.php', 'ai.php'];

        foreach ($routeFiles as $routeFile) {
            $fileName = basename($routeFile);

            if (in_array($fileName, $systemRoutes)) {
                continue;
            }

            $domainName = pathinfo($fileName, PATHINFO_FILENAME);
            $content = File::get($routeFile);

            // Extract URL prefix from Route::prefix() calls
            $prefix = '/';
            if (preg_match("/Route::prefix\(['\"]([^'\"]+)['\"]\)/", $content, $matches)) {
                $prefix = '/'.$matches[1];
            }

            // Detect middleware from bootstrap/app.php
            $middleware = ['web', 'auth'];
            if (str_contains($bootstrapContent, "routes/{$fileName}")) {
                if (preg_match("/group\(base_path\('routes\/{$fileName}'\)\)/", $bootstrapContent)) {
                    // Extract middleware from the chain above
                    $pattern = "/middleware\(\[([^\]]+)\]\)[^}]*?group\(base_path\('routes\/{$fileName}'\)\)/s";
                    if (preg_match($pattern, $bootstrapContent, $mwMatches)) {
                        $middleware = array_map(
                            fn (string $m): string => trim($m, " '\""),
                            explode(',', $mwMatches[1])
                        );
                    }
                }
            }

            // Find modules (Route groups with name prefix)
            $modules = [];
            preg_match_all("/Route::prefix\(['\"]([^'\"]+)['\"]\)->name\(['\"]([^'\"]+)['\"]\)/", $content, $moduleMatches, PREG_SET_ORDER);

            foreach ($moduleMatches as $match) {
                $moduleSlug = $match[1];
                $moduleName = rtrim($match[2], '.');

                // Check for corresponding Livewire components
                $livewireDir = app_path('Livewire/App/'.ucfirst($domainName));
                $moduleComponents = [];

                if (File::isDirectory($livewireDir)) {
                    $dirs = File::directories($livewireDir);
                    foreach ($dirs as $dir) {
                        $dirName = basename($dir);
                        $moduleComponents[] = $dirName;
                    }
                }

                // Extract permissions from route middleware
                $permissions = [];
                $sectionPattern = "/prefix\(['\"]".preg_quote($moduleSlug, '/')."['\"]\).*?}\);/s";
                if (preg_match($sectionPattern, $content, $sectionMatch)) {
                    preg_match_all("/permission:([^'\"]+)/", $sectionMatch[0], $permMatches);
                    $permissions = $permMatches[1] ?? [];
                }

                $modules[] = [
                    'slug' => $moduleSlug,
                    'route_name' => $moduleName,
                    'permissions' => $permissions,
                    'livewire_components' => $moduleComponents,
                ];
            }

            // Also detect standalone routes (not in a prefix group)
            preg_match_all("/->name\(['\"]([^'\"]+)['\"]\)/", $content, $standaloneNames);
            $standaloneRoutes = [];
            foreach ($standaloneNames[1] as $routeName) {
                if (! str_contains($routeName, '.')) {
                    $standaloneRoutes[] = $routeName;
                }
            }

            $domains[] = [
                'name' => $domainName,
                'route_file' => "routes/{$fileName}",
                'url_prefix' => $prefix,
                'middleware' => $middleware,
                'modules' => $modules,
                'standalone_routes' => $standaloneRoutes,
            ];
        }

        return Response::structured($domains);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
