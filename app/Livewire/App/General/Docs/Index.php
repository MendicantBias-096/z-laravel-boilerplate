<?php

declare(strict_types=1);

namespace App\Livewire\App\General\Docs;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Lee la documentación de `docs/` y la renderiza dentro de la aplicación.
 *
 * El markdown es la fuente de verdad y vive en el repositorio; esta pantalla
 * solo lo muestra. No hay copia en base de datos que se pueda desincronizar.
 *
 * Sobre el HTML que produce `render()`:
 *   headings   — se les asigna un id estable para poder enlazarlos
 *   reglas     — `## R25 — enunciado` se parte en etiqueta y enunciado
 *   enforcement— el blockquote pasa a barra de metadatos con severidad
 */
class Index extends Component
{
    /**
     * Documentos que van primero en el índice, en este orden.
     *
     * El resto queda por orden alfabético detrás. Sin esto, «Reglas de
     * arquitectura» abriría la lista y quien llega de cero empezaría por la
     * referencia en vez de por la puerta.
     */
    private const ORDER = ['empezar-aqui', 'architecture-rules'];

    /** Slug del documento abierto. Siempre una llave de documents(). */
    #[Url]
    public string $doc = 'empezar-aqui';

    /**
     * Documentos disponibles, indexados por slug.
     *
     * El slug es la llave de una lista blanca construida desde el disco: la
     * entrada del usuario nunca se concatena a una ruta de archivo, así que
     * no hay superficie para path traversal.
     *
     * @return array<string, array{title: string, path: string, group: string}>
     */
    #[Computed]
    public function documents(): array
    {
        $roots = [
            'general' => base_path('docs/*.md'),
            'patterns' => base_path('docs/patterns/*.md'),
        ];

        $documents = [];

        foreach ($roots as $group => $pattern) {
            foreach (glob($pattern) ?: [] as $path) {
                $name = basename($path, '.md');

                // `_template.md` es la plantilla de docs/patterns, no un patrón.
                if (str_starts_with($name, '_')) {
                    continue;
                }

                $slug = Str::slug($group === 'patterns' ? "patterns {$name}" : $name);

                $documents[$slug] = [
                    'title' => $this->titleOf($path, $name),
                    'path' => $path,
                    'group' => $group,
                ];
            }
        }

        $order = array_flip(self::ORDER);

        uksort($documents, static fn (string $a, string $b): int => (
            ($order[$a] ?? PHP_INT_MAX) <=> ($order[$b] ?? PHP_INT_MAX)
        ) ?: strcmp($a, $b));

        return $documents;
    }

    /**
     * Documentos agrupados para el índice lateral.
     *
     * @return array<string, array<string, array{title: string, path: string, group: string}>>
     */
    #[Computed]
    public function grouped(): array
    {
        $grouped = [];

        foreach ($this->documents() as $slug => $document) {
            $grouped[$document['group']][$slug] = $document;
        }

        return $grouped;
    }

    /**
     * HTML del documento abierto, ya post-procesado.
     *
     * Devuelve cadena vacía si el slug no está en la lista blanca.
     */
    #[Computed]
    public function html(): string
    {
        $document = $this->documents()[$this->doc] ?? null;

        if ($document === null) {
            return '';
        }

        $html = Str::markdown((string) file_get_contents($document['path']));

        return $this->decorateCode($this->decorateRules($this->anchorHeadings($html)));
    }

    /**
     * Entradas del índice lateral del documento abierto.
     *
     * @return list<array{level: int, id: string, label: string, rule: ?string}>
     */
    #[Computed]
    public function outline(): array
    {
        preg_match_all(
            '/<h([12]) id="([^"]+)"[^>]*>(.*?)<\/h\1>/s',
            $this->html(),
            $matches,
            PREG_SET_ORDER
        );

        $outline = [];

        foreach ($matches as [, $level, $id, $inner]) {
            // El id manda sobre el texto: tras decorar, la etiqueta y el
            // enunciado son dos nodos y `strip_tags` los pega sin espacio.
            $rule = preg_match('/^r(\d+)$/', $id, $found) === 1 ? 'R'.$found[1] : null;

            if ($rule !== null && preg_match('/rule__statement">(.*?)<\/span>/s', $inner, $statement) === 1) {
                $inner = $statement[1];
            }

            $text = trim(html_entity_decode(strip_tags($inner)));

            if ($text === '') {
                continue;
            }

            $outline[] = [
                'level' => (int) $level,
                'id' => $id,
                'label' => $text,
                'rule' => $rule,
            ];
        }

        return $outline;
    }

    /** Título del documento abierto, para la cabecera de la página. */
    #[Computed]
    public function title(): string
    {
        return $this->documents()[$this->doc]['title'] ?? __('docs.not_found');
    }

    public function render(): Factory|View
    {
        return view('app.general.docs._index');
    }

    /** Primer encabezado de nivel uno del archivo; si no hay, el nombre. */
    private function titleOf(string $path, string $fallback): string
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return $fallback;
        }

        try {
            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, '# ')) {
                    return trim(substr($line, 2));
                }
            }
        } finally {
            fclose($handle);
        }

        return Str::headline($fallback);
    }

    /**
     * Asigna a cada h1/h2/h3 un id estable derivado de su texto.
     *
     * Se hace aquí y no con la extensión HeadingPermalink de CommonMark para
     * que el id de una regla sea su propio identificador (`R25`), que es lo
     * que se cita en un code review y lo que se pega en un enlace.
     */
    private function anchorHeadings(string $html): string
    {
        $seen = [];

        return (string) preg_replace_callback(
            '/<h([123])>(.*?)<\/h\1>/s',
            function (array $match) use (&$seen): string {
                [, $level, $inner] = $match;

                $text = trim(html_entity_decode(strip_tags($inner)));
                $id = preg_match('/^(R\d+)\b/', $text, $rule) === 1
                    ? Str::lower($rule[1])
                    : (Str::slug($text) ?: 'seccion');

                $seen[$id] = ($seen[$id] ?? 0) + 1;

                if ($seen[$id] > 1) {
                    $id .= '-'.$seen[$id];
                }

                return sprintf('<h%s id="%s">%s</h%s>', $level, $id, $inner, $level);
            },
            $html
        );
    }

    /**
     * Envuelve cada bloque de código para numerar sus líneas y poder copiarlo.
     *
     * Los números son un contador CSS sobre `.line`, así que no entran en la
     * selección de texto ni en lo que copia el botón — que es el defecto de
     * numerar escribiendo el número dentro del código.
     */
    private function decorateCode(string $html): string
    {
        $copy = svg('lucide-copy', 'size-3.5')->toHtml();
        $done = svg('lucide-check', 'size-3.5')->toHtml();

        return (string) preg_replace_callback(
            '/<pre><code([^>]*)>(.*?)<\/code><\/pre>/s',
            function (array $match) use ($copy, $done): string {
                [, $attributes, $code] = $match;

                $lines = array_map(
                    static fn (string $line): string => '<span class="line">'.$line.'</span>',
                    explode("\n", rtrim($code, "\n"))
                );

                return '<div class="code" x-data="{ copied: false }">'
                    .'<button type="button" class="code__copy"'
                    .' :aria-label="copied ? \''.__('docs.copied').'\' : \''.__('docs.copy').'\'"'
                    .' @click="navigator.clipboard.writeText('
                    .'$el.nextElementSibling.innerText); copied = true;'
                    .' setTimeout(() => copied = false, 1600)">'
                    .'<span x-show="! copied">'.$copy.'</span>'
                    .'<span x-show="copied" x-cloak>'.$done.'</span>'
                    .'</button>'
                    .'<pre><code'.$attributes.'>'.implode('', $lines).'</code></pre>'
                    .'</div>';
            },
            $html
        );
    }

    /**
     * Da forma a las dos piezas propias del documento de reglas.
     *
     * `## R25 — enunciado` se parte en etiqueta y enunciado para que el ID
     * quede como ancla visible, y el blockquote de Enforcement pasa a una
     * barra de metadatos con la severidad como chip. Sin esto, lo que más se
     * consulta —qué rompe el build— queda enterrado en prosa.
     */
    private function decorateRules(string $html): string
    {
        // La capa en lenguaje llano de cada regla, marcada para poder darle
        // un peso visual distinto al del motivo técnico que la sigue.
        $html = str_replace(
            '<p><strong>Qué significa.</strong>',
            '<p class="plain"><strong>Qué significa.</strong>',
            $html
        );

        $html = (string) preg_replace(
            '/<h2 id="(r\d+)">\s*(R\d+)\s*—\s*(.*?)<\/h2>/s',
            '<h2 id="$1" class="rule"><a class="rule__id" href="#$1">$2</a>'
            .'<span class="rule__statement">$3</span></h2>',
            $html
        );

        // CommonMark une las dos líneas del blockquote en un solo párrafo con
        // un salto en medio, no en dos <p>. Partir por «Escape:» y no por la
        // etiqueta es lo que sobrevive a ese detalle.
        return (string) preg_replace_callback(
            '/<blockquote>\s*<p>Enforcement:\s*(.*?)\s*\R\s*Escape:\s*(.*?)<\/p>\s*<\/blockquote>/s',
            function (array $match): string {
                [, $enforcement, $escape] = $match;

                $severity = preg_match('/Severidad:\s*(\w+)/u', $enforcement, $found) === 1
                    ? Str::lower($found[1])
                    : 'guideline';

                $tool = trim(Str::before($enforcement, '· Severidad'));
                $tool = trim(str_replace('—', '', $tool), " ·\u{00A0}");

                return sprintf(
                    '<div class="meta"><span class="meta__sev meta__sev--%s">%s</span>%s'
                    .'<span class="meta__escape">Escape: %s</span></div>',
                    $severity,
                    $severity,
                    $tool === '' ? '' : '<span class="meta__tool">'.$tool.'</span>',
                    trim($escape),
                );
            },
            $html
        );
    }
}
