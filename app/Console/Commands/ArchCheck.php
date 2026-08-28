<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

/**
 * Los checks de docs/ARCHITECTURE_RULES.md que necesitan entender el código.
 *
 * La línea con `scripts/arch-lint.sh` es esta: si el check se resuelve leyendo
 * texto, va a bash y cuesta 0 s; si necesita el árbol sintáctico, el esquema o
 * el grafo de imports, va aquí y paga el segundo de arranque de Laravel.
 *
 * Hoy implementa R46 y R53, las dos que necesitan el lexer para no confundir
 * una variable o un comentario con su mención dentro de otra cosa. Las demás
 * de esta capa —R4, R7, R10, R12, R20, R27, R30, R32, R41, R45— esperan a que
 * exista `app/Modules/`, y se declaran omitidas al final en vez de callar.
 */
class ArchCheck extends Command
{
    protected $signature = 'arch:check {--rule= : Comprobar solo esta regla, por ejemplo R46}';

    protected $description = 'Comprueba las reglas de arquitectura que necesitan entender el código';

    /** Reglas de esta capa que dependen de la migración a app/Modules/. */
    private const PENDING = ['R4', 'R7', 'R10', 'R12', 'R20', 'R27', 'R30', 'R32', 'R41', 'R45'];

    private bool $failed = false;

    public function handle(): int
    {
        $this->line('arch:check · docs/ARCHITECTURE_RULES.md');
        $this->newLine();

        $only = $this->option('rule');

        if ($only === null || $only === 'R46') {
            $this->checkCommentWidth();
        }

        if ($only === null || $only === 'R53') {
            $this->checkShortNames();
        }

        if ($only === null && ! is_dir(base_path('app/Modules'))) {
            $this->newLine();
            $this->line(sprintf(
                '<fg=gray>· %s   omitidas: requieren app/Modules/, que aún no existe</>',
                implode(',', self::PENDING)
            ));
        }

        $this->newLine();

        if ($this->failed) {
            $this->line('<fg=red>arch:check: falla</>');

            return self::FAILURE;
        }

        $this->line('<fg=green>arch:check: pasa</>');

        return self::SUCCESS;
    }

    /**
     * R46 · Una línea de comentario no pasa de 90 caracteres.
     *
     * Usa el lexer y no un `grep` porque hay cinco contextos que un patrón no
     * distingue: `#[` de `#`, `//` dentro de una URL en un string, `#` dentro
     * de una cadena, y la frontera entre prosa y anotación dentro del mismo
     * docblock. `T_COMMENT` y `T_DOC_COMMENT` regalan esa distinción.
     *
     * Quedan exentas las anotaciones: un genérico largo no se parte de forma
     * que PHPDoc entienda, y la regla es sobre la prosa.
     */
    private function checkCommentWidth(): void
    {
        $violations = [];

        foreach ($this->sourceFiles() as $path) {
            $source = file_get_contents($path);

            if ($source === false) {
                continue;
            }

            $lines = explode("\n", $source);

            foreach (token_get_all($source) as $token) {
                if (! is_array($token) || ! in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }

                $start = $token[2];

                foreach (explode("\n", $token[1]) as $offset => $text) {
                    if ($this->isAnnotation($text)) {
                        continue;
                    }

                    $line = $lines[$start + $offset - 1] ?? $text;
                    $width = mb_strlen(rtrim($line));

                    if ($width > 90) {
                        $violations[] = sprintf(
                            '%s:%d (%d)',
                            $this->relative($path),
                            $start + $offset,
                            $width
                        );
                    }
                }
            }
        }

        if ($violations === []) {
            $this->line('<fg=green>✓ R46</>  ninguna línea de comentario pasa de 90 caracteres');

            return;
        }

        $this->failed = true;
        $this->line(sprintf('<fg=red>✗ R46</>  %d líneas de comentario pasan de 90 caracteres:', count($violations)));

        foreach (array_slice($violations, 0, 10) as $violation) {
            $this->line('      '.$violation);
        }

        if (count($violations) > 10) {
            $this->line(sprintf('      … y %d más', count($violations) - 10));
        }
    }

    /**
     * R53 · Los nombres son completos; los de una letra van por lista blanca.
     *
     * Cerrada: `$i`, `$k` y `$v` como índices de bucle, `$q` para closures de
     * query —que es lo que usa la documentación de Laravel— y `$a`/`$b` para
     * comparadores, que es lo que usa la de PHP. Pelearse con el idioma de
     * ninguna de las dos aclara nada.
     *
     * También necesita el lexer: un `$p` mencionado dentro de un comentario no
     * es una variable, y un `grep` lo marca igual. Se descubrió con este mismo
     * archivo, que documenta una cicatriz citando `User::permission($p)`.
     */
    private function checkShortNames(): void
    {
        $allowed = ['i', 'k', 'v', 'q', 'a', 'b'];
        $violations = [];

        foreach ($this->sourceFiles() as $path) {
            $source = file_get_contents($path);

            if ($source === false) {
                continue;
            }

            foreach (token_get_all($source) as $token) {
                if (! is_array($token) || $token[0] !== T_VARIABLE) {
                    continue;
                }

                $name = ltrim($token[1], '$');

                if (mb_strlen($name) !== 1 || in_array($name, $allowed, true)) {
                    continue;
                }

                $violations[] = sprintf('%s:%d  $%s', $this->relative($path), $token[2], $name);
            }
        }

        if ($violations === []) {
            $this->line('<fg=green>✓ R53</>  sin variables de una letra fuera de la lista blanca');

            return;
        }

        $this->line(sprintf('<fg=yellow>! R53</>  %d variables de una letra:', count($violations)));

        foreach (array_slice($violations, 0, 8) as $violation) {
            $this->line('      '.$violation);
        }
    }

    /** Una anotación de tipo queda exenta: partirla rompe lo que PHPDoc lee. */
    private function isAnnotation(string $text): bool
    {
        return preg_match('/^\s*\*?\s*@[a-zA-Z-]+/', $text) === 1;
    }

    /** @return list<string> */
    private function sourceFiles(): array
    {
        $roots = array_filter([
            base_path('app'),
            base_path('routes'),
            base_path('database'),
            base_path('tests'),
        ], 'is_dir');

        $files = (new Finder)->files()->in($roots)->name('*.php');

        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->getRealPath();
        }

        // Los config/ publicados por paquetes vienen con banner de fábrica y
        // no tiene sentido tocarlos; los propios sí entran.
        foreach (['menu', 'roles', 'notifications'] as $own) {
            $path = base_path("config/{$own}.php");

            if (is_file($path)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    private function relative(string $path): string
    {
        return str_replace(base_path().'/', '', $path);
    }
}
