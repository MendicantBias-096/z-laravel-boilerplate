<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sirve la versión Markdown de una página en su misma URL.
 *
 * Un agente que lee la aplicación gasta la mayor parte de sus tokens en
 * markup que no dice nada: clases de Tailwind, atributos de Livewire, la
 * navegación repetida en cada página. El gemelo entrega el contenido y nada
 * más.
 *
 * Se pide de dos formas, y las dos llevan al mismo sitio:
 *
 *     GET /nosotros.md
 *     GET /nosotros   con  Accept: text/markdown
 *
 * La página declara su gemelo; no se adivina:
 *
 *     Route::view('/nosotros', 'platform::public.about.index')
 *         ->defaults('markdown', 'platform::public.about.md')
 *
 * Una ruta sin ese default no tiene gemelo y responde su HTML de siempre,
 * que es lo correcto: un formulario o un panel no mejoran en Markdown.
 */
class ServeMarkdownTwin
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $quiereMarkdown = $this->stripSuffix($request) || $this->acceptsMarkdown($request);

        $response = $next($request);

        if (! $quiereMarkdown) {
            return $response;
        }

        $twin = $request->route()?->defaults['markdown'] ?? null;

        if (! is_string($twin) || ! View::exists($twin)) {
            return $response;
        }

        return response(View::make($twin)->render(), $response->getStatusCode(), [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

    /**
     * Quita el `.md` de la URI para que el router encuentre la ruta normal, y
     * dice si lo había.
     *
     * ponytail: la página se renderiza igual y se descarta. Evitarlo pide
     * resolver la ruta a mano antes del router, y este camino lo recorre un
     * agente de vez en cuando, no un usuario en cada clic.
     */
    private function stripSuffix(Request $request): bool
    {
        $path = $request->getPathInfo();

        if (! str_ends_with($path, '.md')) {
            return false;
        }

        $limpio = substr($path, 0, -3);

        // La raíz es el caso especial de todo esquema de sufijos: `/.md` no
        // llega ni al framework —nginx bloquea lo que empieza por punto— y
        // `/` no admite sufijo. `/index.md` es la convención, y aquí se
        // traduce de vuelta.
        if ($limpio === '/index') {
            $limpio = '/';
        }

        $request->server->set('REQUEST_URI', $limpio.
            ($request->getQueryString() !== null ? '?'.$request->getQueryString() : ''));

        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->getContent(),
        );

        return true;
    }

    /**
     * `Accept: text/markdown` a secas. Un navegador manda `text/html` primero
     * y `*​/*` al final, así que comparar contra el comodín serviría Markdown
     * a todo el mundo.
     */
    private function acceptsMarkdown(Request $request): bool
    {
        return in_array('text/markdown', $request->getAcceptableContentTypes(), true);
    }
}
