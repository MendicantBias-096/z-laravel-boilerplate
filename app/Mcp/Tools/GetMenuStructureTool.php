<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Returns the sidebar menu structure from config/menu.php. Shows all menu items with their labels, icons, routes, permissions, and nested children. Use this to understand the navigation structure or before adding a new menu entry.')]
class GetMenuStructureTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $menu = config('menu.menu', []);

        return Response::structured([
            'menu' => $menu,
            'total_items' => $this->countItems($menu),
        ]);
    }

    /**
     * Recursively count all menu items including children.
     */
    private function countItems(array $menu): int
    {
        $count = 0;
        foreach ($menu as $item) {
            $count++;
            if (isset($item['items'])) {
                $count += $this->countItems($item['items']);
            }
        }

        return $count;
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
