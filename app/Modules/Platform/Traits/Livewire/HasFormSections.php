<?php

declare(strict_types=1);

namespace App\Modules\Platform\Traits\Livewire;

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;

/**
 * Un formulario partido en secciones, para el chasis `<x-ui.form-rail>`.
 *
 * Partir un formulario largo resuelve el problema de la pantalla infinita y
 * crea dos nuevos, que es lo que este trait tapa: el usuario no ve los errores
 * de las secciones cerradas, y no sabe qué le queda por guardar en ellas.
 *
 * El componente declara sus secciones y en cuál vive cada campo:
 *
 *     protected function formSections(): array
 *     {
 *         return [
 *             ['key' => 'identity', 'icon' => 'lucide-user-round', 'label' => __('...')],
 *         ];
 *     }
 *
 *     protected function sectionFields(): array
 *     {
 *         return ['form.email' => 'account', 'permissionList' => 'access'];
 *     }
 */
trait HasFormSections
{
    /**
     * Sección abierta.
     *
     * Va en la URL para que recargar —o compartir el enlace— no devuelva
     * siempre a la primera. No lleva `#[Locked]` a propósito: es estado de
     * navegación y no dice sobre qué registro se opera. Lo que sí hace falta
     * es acotarla, y de eso se encarga `goTo()`.
     */
    #[Url]
    public string $section = '';

    /**
     * @return list<array{key: string, icon: string, label: string, badge?: string}>
     */
    abstract protected function formSections(): array;

    /**
     * En qué sección vive cada campo, indexado por la clave del error de
     * validación: `form.email => account`.
     *
     * @return array<string, string>
     */
    abstract protected function sectionFields(): array;

    /**
     * Cambia de sección.
     *
     * La clave llega del navegador, así que se comprueba contra las
     * declaradas: sin esto el chasis dibuja una caja vacía sin decir por qué.
     */
    public function goTo(string $key): void
    {
        if (in_array($key, array_column($this->formSections(), 'key'), true)) {
            $this->section = $key;
        }
    }

    protected function firstSectionKey(): string
    {
        return $this->formSections()[0]['key'] ?? '';
    }

    /**
     * Ejecuta el guardado y, si la validación falla, abre la sección donde
     * vive el primer campo que falló.
     *
     * Sin esto un formulario partido esconde sus propios fallos: si el correo
     * está repetido y esa sección no está abierta, el guardado no hace nada y
     * no se ve por qué.
     *
     * @param  callable(): void  $guardar
     */
    protected function saveShowingErrors(callable $guardar): void
    {
        try {
            $guardar();
        } catch (ValidationException $e) {
            $this->section = $this->sectionOfFirstError($e);

            throw $e;
        }
    }

    private function sectionOfFirstError(ValidationException $e): string
    {
        $campos = $this->sectionFields();

        foreach (array_keys($e->errors()) as $campo) {
            $campo = (string) $campo;

            // `permissionList.0` cae en la misma sección que `permissionList`.
            $raiz = strtok($campo, '.') ?: $campo;

            foreach ([$campo, $raiz] as $clave) {
                if (isset($campos[$clave])) {
                    return $campos[$clave];
                }
            }
        }

        return $this->section;
    }
}
