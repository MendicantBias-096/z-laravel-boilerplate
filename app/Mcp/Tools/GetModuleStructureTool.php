<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\File;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Returns the complete file structure of a module following the boilerplate three-layer convention. Provide the domain (e.g. "personal") and module name (e.g. "usuarios"). Shows which files exist and which are missing, including model, migration, views, Livewire components, routes, breadcrumbs, menu entry, and permissions.')]
class GetModuleStructureTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'domain' => 'required|string',
            'module' => 'required|string',
        ]);

        $domain = strtolower($validated['domain']);
        $module = strtolower($validated['module']);
        $domainPascal = ucfirst($domain);

        // Detect Livewire component folder name (could be PascalCase variation)
        $livewireBase = app_path("Livewire/App/{$domainPascal}");
        $componentFolder = null;

        if (File::isDirectory($livewireBase)) {
            foreach (File::directories($livewireBase) as $dir) {
                if (strtolower(basename($dir)) === str_replace('-', '', $module)) {
                    $componentFolder = basename($dir);
                    break;
                }
            }
        }

        // Try to find the model by scanning app/Models
        $modelName = null;
        $modelFiles = File::glob(app_path('Models/*.php'));
        foreach ($modelFiles as $modelFile) {
            $name = pathinfo($modelFile, PATHINFO_FILENAME);
            if (strtolower($name) === str_replace('-', '', rtrim($module, 's'))
                || strtolower($name) === str_replace('-', '', $module)) {
                $modelName = $name;
                break;
            }
        }

        // Build expected file paths
        $files = [
            'model' => [
                'expected' => $modelName ? "app/Models/{$modelName}.php" : 'app/Models/'.ucfirst(rtrim($module, 's')).'.php',
                'exists' => $modelName ? File::exists(app_path("Models/{$modelName}.php")) : false,
            ],
            'livewire_components' => [],
            'views' => [
                'wrapper_index' => [
                    'path' => "resources/views/app/{$domain}/{$module}/index.blade.php",
                    'exists' => File::exists(resource_path("views/app/{$domain}/{$module}/index.blade.php")),
                ],
                'component_index' => [
                    'path' => "resources/views/app/{$domain}/{$module}/_index.blade.php",
                    'exists' => File::exists(resource_path("views/app/{$domain}/{$module}/_index.blade.php")),
                ],
                'wrapper_create' => [
                    'path' => "resources/views/app/{$domain}/{$module}/create.blade.php",
                    'exists' => File::exists(resource_path("views/app/{$domain}/{$module}/create.blade.php")),
                ],
                'wrapper_edit' => [
                    'path' => "resources/views/app/{$domain}/{$module}/edit.blade.php",
                    'exists' => File::exists(resource_path("views/app/{$domain}/{$module}/edit.blade.php")),
                ],
                'component_form' => [
                    'path' => "resources/views/app/{$domain}/{$module}/_form.blade.php",
                    'exists' => File::exists(resource_path("views/app/{$domain}/{$module}/_form.blade.php")),
                ],
            ],
            'route_file' => [
                'path' => "routes/{$domain}.php",
                'exists' => File::exists(base_path("routes/{$domain}.php")),
            ],
            'form_object' => null,
        ];

        // Livewire components
        if ($componentFolder) {
            $livewireDir = app_path("Livewire/App/{$domainPascal}/{$componentFolder}");
            $phpFiles = File::glob($livewireDir.'/*.php');
            foreach ($phpFiles as $file) {
                $className = pathinfo($file, PATHINFO_FILENAME);
                $files['livewire_components'][] = [
                    'class' => "App\\Livewire\\App\\{$domainPascal}\\{$componentFolder}\\{$className}",
                    'path' => "app/Livewire/App/{$domainPascal}/{$componentFolder}/{$className}.php",
                    'exists' => true,
                ];
            }
        }

        // Form objects
        $formObjects = File::glob(app_path('Livewire/Forms/*Form.php'));
        foreach ($formObjects as $formFile) {
            $formName = pathinfo($formFile, PATHINFO_FILENAME);
            if ($modelName && str_contains($formName, $modelName)) {
                $files['form_object'] = [
                    'class' => "App\\Livewire\\Forms\\{$formName}",
                    'path' => "app/Livewire/Forms/{$formName}.php",
                    'exists' => true,
                ];
            }
        }

        // Migrations
        $migrations = [];
        $tableName = str_replace('-', '_', $module);
        $migrationFiles = File::glob(database_path('migrations/*.php'));
        foreach ($migrationFiles as $migFile) {
            $migName = basename($migFile);
            if (str_contains($migName, $tableName) || ($modelName && str_contains($migName, strtolower($modelName)))) {
                $migrations[] = [
                    'path' => "database/migrations/{$migName}",
                    'exists' => true,
                ];
            }
        }
        $files['migrations'] = $migrations;

        // Factory
        if ($modelName) {
            $files['factory'] = [
                'path' => "database/factories/{$modelName}Factory.php",
                'exists' => File::exists(database_path("factories/{$modelName}Factory.php")),
            ];
        }

        // Check permissions in config/roles.php
        $rolesConfig = config('roles.permissions', []);
        $modulePermissions = [];
        foreach ($rolesConfig as $key => $perms) {
            // Match by module slug (e.g. 'usuarios' matches 'usuarios')
            if ($key === $module || $key === str_replace('-', '_', $module)) {
                $modulePermissions[$key] = $perms;
            }
        }
        $files['permissions'] = $modulePermissions;

        // Check menu entry
        $menuConfig = config('menu.menu', []);
        $hasMenuEntry = $this->findMenuEntry($menuConfig, $domain, $module);
        $files['menu_entry'] = $hasMenuEntry;

        // Check breadcrumbs
        $breadcrumbsPath = base_path('routes/breadcrumbs.php');
        $hasBreadcrumbs = false;
        if (File::exists($breadcrumbsPath)) {
            $breadcrumbContent = File::get($breadcrumbsPath);
            $hasBreadcrumbs = str_contains($breadcrumbContent, "{$domain}.{$module}.");
        }
        $files['breadcrumbs'] = $hasBreadcrumbs;

        return Response::structured([
            'domain' => $domain,
            'module' => $module,
            'files' => $files,
        ]);
    }

    /**
     * Recursively search menu config for a module entry.
     */
    private function findMenuEntry(array $menu, string $domain, string $module): bool
    {
        foreach ($menu as $item) {
            $route = $item['route'] ?? '';
            if (str_contains($route, "{$domain}.{$module}.")) {
                return true;
            }
            if (isset($item['items'])) {
                if ($this->findMenuEntry($item['items'], $domain, $module)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'domain' => $schema->string()
                ->description('Domain name in lowercase (e.g. "personal", "general").')
                ->required(),
            'module' => $schema->string()
                ->description('Module slug in lowercase (e.g. "usuarios", "roles").')
                ->required(),
        ];
    }
}
