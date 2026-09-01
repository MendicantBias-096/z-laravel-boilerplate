<?php

declare(strict_types=1);

namespace App\Modules\Platform\Traits\Livewire;

use Illuminate\Support\Arr;
use RuntimeException;

/**
 * Campos que se repiten N veces dentro de un formulario.
 *
 * El array vive en el componente padre y no en un componente por fila: la
 * validación del conjunto —`telefonos.*`— se escribe una vez, y añadir una fila
 * no cuesta una petición ni un ciclo de vida propio.
 *
 * El componente declara qué campos son repetibles y con qué valor nace una fila:
 *
 *     public array $telefonos = [''];
 *
 *     protected function repeatableFields(): array
 *     {
 *         return ['telefonos' => ''];          // string
 *         // ['cantidades' => 0]               // integer
 *         // ['lineas' => ['tipo' => null]]    // varios campos por fila
 *     }
 *
 * **Esa lista es una guarda, no una comodidad.** Los tres métodos son públicos
 * y reciben el nombre del campo desde el navegador, así que sin ella
 * `removeRepeatable('record', 0)` alcanzaría cualquier propiedad del
 * componente. Es la misma lección de R57: lo que el cliente elige, se acota en
 * el servidor.
 */
trait HasRepeatableFields
{
    /**
     * Campos repetibles del componente y el valor con el que nace una fila.
     *
     * @return array<string, mixed>
     */
    abstract protected function repeatableFields(): array;

    public function addRepeatable(string $field): void
    {
        $filas = $this->repeatableRows($field);
        $filas[] = $this->repeatableFields()[$field];

        $this->setRepeatable($field, $filas);
    }

    /**
     * Quitar la fila 1 de `['a', 'b', 'c']` deja `[0 => 'a', 2 => 'c']`, y ese
     * hueco rompe `wire:model="campo.1"` en la vista y `campo.*` en la
     * validación. `array_values` es lo que vuelve a numerar de 0 a N.
     */
    public function removeRepeatable(string $field, int $index): void
    {
        $filas = $this->repeatableRows($field);

        unset($filas[$index]);

        $this->setRepeatable($field, array_values($filas));
    }

    /**
     * Mueve una fila una posición. `$direction` es -1 (subir) o 1 (bajar);
     * cualquier otro valor, o un destino fuera del array, no hace nada: el
     * índice llega del navegador y una lista de dos elementos tiene tantos
     * botones como para pedir el índice 5.
     */
    public function moveRepeatable(string $field, int $index, int $direction): void
    {
        $filas = $this->repeatableRows($field);
        $destino = $index + $direction;

        if (! in_array($direction, [-1, 1], true)) {
            return;
        }

        if (! array_key_exists($index, $filas) || ! array_key_exists($destino, $filas)) {
            return;
        }

        [$filas[$index], $filas[$destino]] = [$filas[$destino], $filas[$index]];

        $this->setRepeatable($field, $filas);
    }

    /**
     * @return array<int, mixed>
     */
    private function repeatableRows(string $field): array
    {
        if (! array_key_exists($field, $this->repeatableFields())) {
            throw new RuntimeException("El campo repetible «{$field}» no está declarado en repeatableFields().");
        }

        return Arr::wrap(data_get($this, $field, []));
    }

    /**
     * @param  array<int, mixed>  $filas
     */
    private function setRepeatable(string $field, array $filas): void
    {
        data_set($this, $field, $filas);
    }
}
