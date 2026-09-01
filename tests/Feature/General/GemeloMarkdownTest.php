<?php

declare(strict_types=1);

namespace Tests\Feature\General;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * El gemelo Markdown existe para que un agente lea la aplicación sin gastar
 * tokens en markup. Estos casos cubren las tres formas de pedirlo y, sobre
 * todo, que `llms.txt` no enlace a páginas que no lo tienen: un mapa que
 * miente es peor que no tener mapa.
 */
class GemeloMarkdownTest extends TestCase
{
    public function test_el_sufijo_md_devuelve_markdown(): void
    {
        $respuesta = $this->get('/nosotros.md');

        $respuesta->assertOk();
        $respuesta->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $this->assertStringStartsWith('#', trim($respuesta->getContent() ?: ''));
    }

    public function test_la_cabecera_accept_devuelve_markdown(): void
    {
        $this->get('/nosotros', ['Accept' => 'text/markdown'])
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
    }

    /**
     * Un navegador manda `text/html` primero y `*​/*` al final. Comparar contra
     * el comodín serviría Markdown a todo el mundo.
     */
    public function test_un_navegador_sigue_recibiendo_html(): void
    {
        $respuesta = $this->get('/nosotros', ['Accept' => 'text/html,application/xhtml+xml,*/*;q=0.8']);

        $respuesta->assertOk();
        $this->assertStringContainsString('text/html', (string) $respuesta->headers->get('Content-Type'));
    }

    /**
     * La raíz es el caso especial de todo esquema de sufijos: `/.md` ni llega
     * al framework porque nginx bloquea lo que empieza por punto.
     */
    public function test_la_portada_responde_en_index_md(): void
    {
        $this->get('/index.md')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
    }

    public function test_una_ruta_sin_gemelo_devuelve_su_html(): void
    {
        $respuesta = $this->get('/login.md');

        $respuesta->assertOk();
        $this->assertStringContainsString('text/html', (string) $respuesta->headers->get('Content-Type'));
    }

    public function test_llms_txt_se_sirve_como_texto_plano(): void
    {
        $this->get('/llms.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('## Páginas', false);
    }

    /**
     * El caso que evita que el mapa se pudra: cada enlace `.md` que declara
     * `llms.txt` tiene que responder Markdown de verdad.
     */
    public function test_todo_lo_que_llms_txt_enlaza_existe_y_es_markdown(): void
    {
        $contenido = (string) $this->get('/llms.txt')->getContent();

        preg_match_all('/\]\((\S+\.md)\)/', $contenido, $coincidencias);

        $this->assertNotEmpty($coincidencias[1], 'llms.txt no enlaza ninguna página en Markdown.');

        foreach ($coincidencias[1] as $enlace) {
            $ruta = parse_url($enlace, PHP_URL_PATH);

            $this->get((string) $ruta)
                ->assertOk()
                ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        }
    }

    /**
     * Una ruta que declara gemelo pero apunta a una vista que no existe
     * devolvería HTML en silencio, y el fallo solo se vería leyendo la salida.
     */
    public function test_toda_ruta_que_declara_gemelo_apunta_a_una_vista_real(): void
    {
        $declaradas = 0;

        foreach (Route::getRoutes() as $ruta) {
            $vista = $ruta->defaults['markdown'] ?? null;

            if (! is_string($vista)) {
                continue;
            }

            $declaradas++;
            $this->assertTrue(view()->exists($vista), "La ruta «{$ruta->uri()}» declara la vista «{$vista}», que no existe.");
        }

        $this->assertGreaterThan(0, $declaradas);
    }
}
