<?php

declare(strict_types=1);

namespace App\Console\Commands\Arch;

use App\Console\Commands\ArchCheck;

/**
 * Las reglas sobre la forma del proyecto: R45 y R41.
 *
 * Fuera del comando por R52 —un archivo no pasa de 350 líneas— y porque el
 * comando se lee mejor como lo que es: quién comprueba qué.
 */
class StructureChecks
{
    private bool $fallo = false;

    public function __construct(private readonly ArchCheck $cmd) {}

    public function run(?string $only): bool
    {
        if ($only === null || $only === 'R45') {
            $this->checkActionsHaveTests();
        }

        if ($only === null || $only === 'R41') {
            $this->checkMenuRoutes();
        }

        return $this->fallo;
    }

    /**
     * R45 · todo Action tiene su archivo de test.
     *
     * No comprueba que el test sea bueno —eso no lo sabe una máquina— sino
     * que exista, que es lo que se olvida.
     */
    private function checkActionsHaveTests(): void
    {
        $faltan = [];

        foreach (glob(base_path('app/Modules/*/Actions/*.php')) ?: [] as $action) {
            $modulo = basename(dirname($action, 2));
            $nombre = basename($action, '.php');
            $esperado = base_path("app/Modules/{$modulo}/Tests/Unit/{$nombre}Test.php");

            if (! is_file($esperado)) {
                $faltan[] = str_replace(base_path().'/', '', $action);
            }
        }

        if ($faltan === []) {
            $this->cmd->line('<fg=green>✓ R45</>  todo Action tiene su test');

            return;
        }

        $this->fallo = true;
        $this->cmd->line(sprintf('<fg=red>✗ R45</>  %d Actions sin test:', count($faltan)));

        foreach (array_slice($faltan, 0, 8) as $accion) {
            $this->cmd->line('      '.$accion);
        }
    }

    /**
     * R41 · el menú es el único paso fuera del módulo, y por eso se comprueba.
     *
     * Sin entrada en `config/menu.php` el módulo existe y es invisible; con
     * una entrada que apunta a una ruta que no existe, la navegación revienta
     * en la primera carga.
     */
    private function checkMenuRoutes(): void
    {
        $rotas = [];
        $nombres = $this->menuRoutes(config('menu.menu', []));

        // Un check que no encontró nada que mirar no pasa: informa de que no
        // pudo comprobar (R56). Se descubrió leyendo `menu.items`, que no
        // existe —la clave es `menu.menu`— y dando verde con la lista vacía.
        if ($nombres === []) {
            $this->fallo = true;
            $this->cmd->line('<fg=red>✗ R41</>  el menú no declara ninguna ruta; ¿cambió la forma de config/menu.php?');

            return;
        }

        foreach ($nombres as $nombre) {
            if (! app('router')->has($nombre)) {
                $rotas[] = $nombre;
            }
        }

        if ($rotas === []) {
            $this->cmd->line('<fg=green>✓ R41</>  toda entrada del menú apunta a una ruta que existe');

            return;
        }

        $this->fallo = true;
        $this->cmd->line(sprintf('<fg=red>✗ R41</>  %d entradas del menú apuntan a rutas inexistentes:', count($rotas)));

        foreach ($rotas as $ruta) {
            $this->cmd->line('      '.$ruta);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return list<string>
     */
    private function menuRoutes(array $items): array
    {
        $nombres = [];

        foreach ($items as $item) {
            if (is_string($item['route'] ?? null)) {
                $nombres[] = $item['route'];
            }

            if (is_array($item['items'] ?? null)) {
                $nombres = array_merge($nombres, $this->menuRoutes($item['items']));
            }
        }

        return $nombres;
    }
}
