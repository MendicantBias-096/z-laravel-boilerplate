<?php

declare(strict_types=1);

namespace Tests\Feature\General;

use App\Modules\Platform\Livewire\Docs\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_docs(): void
    {
        $this->get(route('platform.docs.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_docs(): void
    {
        $this->actingAs($this->createUser())
            ->get(route('platform.docs.index'))
            ->assertStatus(200);
    }

    public function test_architecture_rules_render_with_headings_and_tables(): void
    {
        Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->assertSet('doc', 'empezar-aqui')
            ->assertSee('Empezar aquí')
            ->set('doc', 'architecture-rules')
            ->assertSee('Reglas de arquitectura')
            ->assertSeeHtml('<table>');
    }

    /**
     * El ID de una regla es lo que se cita en un code review, así que es
     * también su ancla: `#r25` tiene que llevar a R25.
     */
    public function test_each_rule_is_anchored_by_its_own_id(): void
    {
        Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->set('doc', 'architecture-rules')
            ->assertSeeHtml('<h2 id="r25" class="rule">')
            ->assertSeeHtml('<a class="rule__id" href="#r25">R25</a>');
    }

    /** La severidad es lo que más se consulta: sale como chip, no como prosa. */
    public function test_enforcement_block_becomes_a_severity_chip(): void
    {
        Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->set('doc', 'architecture-rules')
            ->assertSeeHtml('meta__sev--error')
            ->assertSeeHtml('meta__sev--warning')
            ->assertSeeHtml('meta__sev--guideline')
            ->assertDontSeeHtml('<blockquote><p>Enforcement');
    }

    public function test_outline_lists_sections_and_rules(): void
    {
        $outline = Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->set('doc', 'architecture-rules')
            ->instance()
            ->outline();

        $rules = array_filter($outline, fn (array $entry): bool => $entry['rule'] !== null);

        $this->assertCount(58, $rules, 'El documento declara 58 reglas.');
        $this->assertSame('R1', reset($rules)['rule']);
    }

    /**
     * Cada regla lleva su explicación en lenguaje llano: sin ella el
     * documento vuelve a servir solo a quien ya conoce el vocabulario.
     */
    public function test_every_rule_has_a_plain_language_layer(): void
    {
        $html = Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->set('doc', 'architecture-rules')
            ->instance()
            ->html();

        $this->assertSame(
            58,
            substr_count($html, '<p class="plain">'),
            'Las 58 reglas deben tener su párrafo «Qué significa».'
        );
    }

    public function test_pattern_docs_are_listed(): void
    {
        Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->assertSee('Patrones');
    }

    /**
     * El slug es la llave de una lista blanca, así que un valor arbitrario
     * no puede alcanzar el disco: solo produce el estado vacío.
     */
    public function test_unknown_slug_does_not_reach_the_filesystem(): void
    {
        Livewire::actingAs($this->createUser())
            ->test(Index::class)
            ->set('doc', '../../.env')
            ->assertSee(__('platform::docs.not_found'))
            ->assertDontSee('APP_KEY');
    }
}
