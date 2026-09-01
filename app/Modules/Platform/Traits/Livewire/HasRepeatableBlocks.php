<?php

declare(strict_types=1);

namespace App\Modules\Platform\Traits\Livewire;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Bloques de varios campos que se repiten, con archivos dentro.
 *
 * Parece el hermano mayor de [[HasRepeatableFields]] y no lo es: aquel numera
 * las filas de 0 a N y **re-indexa al eliminar**, que es justo lo que aquí no
 * se puede hacer.
 *
 * El motivo es el archivo. Un `wire:model="bloques.2.comprobante"` guarda un
 * `TemporaryUploadedFile` que Livewire referencia por esa ruta exacta. Si al
 * borrar la fila 1 los índices se corren, el archivo que estaba en la 2 pasa a
 * la 1, la vista lo pide por su índice nuevo y el que responde es el del
 * vecino: los comprobantes aparecen intercambiados, y solo se nota al abrirlos.
 * Ese es el «problema de reactividad de archivos» del ticket, y no se arregla
 * con más `wire:key`: hay que dejar de numerar por posición.
 *
 * Así que cada bloque nace con una clave propia y el array se indexa por ella.
 * Eliminar no mueve a nadie; el orden lo lleva el propio array, que en PHP
 * conserva el de inserción.
 *
 *     public array $experimentos = [];
 *
 *     protected function repeatableBlocks(): array
 *     {
 *         return ['experimentos' => [
 *             'nombre' => '', 'valor' => null, 'comprobante' => null,
 *         ]];
 *     }
 *
 * El componente necesita además `Livewire\WithFileUploads` para los archivos.
 * Persistirlos es cosa suya: `HasRepeatableBlocks` solo garantiza que cada uno
 * sigue en el bloque donde se subió.
 */
trait HasRepeatableBlocks
{
    /**
     * Campos repetibles y la forma de un bloque nuevo.
     *
     * @return array<string, array<string, mixed>>
     */
    abstract protected function repeatableBlocks(): array;

    public function addBlock(string $field): void
    {
        $bloques = $this->blocksOf($field);

        $bloques[(string) Str::uuid()] = $this->repeatableBlocks()[$field];

        $this->{$field} = $bloques;
    }

    public function removeBlock(string $field, string $key): void
    {
        $bloques = $this->blocksOf($field);

        unset($bloques[$key]);

        $this->{$field} = $bloques;
    }

    /**
     * Mueve un bloque una posición. Reordenar sí cambia el orden del array,
     * pero **no las claves**, así que ningún archivo cambia de ruta.
     */
    public function moveBlock(string $field, string $key, int $direction): void
    {
        $bloques = $this->blocksOf($field);
        $claves = array_keys($bloques);
        $posicion = array_search($key, $claves, true);

        if ($posicion === false || ! in_array($direction, [-1, 1], true)) {
            return;
        }

        $destino = $posicion + $direction;

        if (! array_key_exists($destino, $claves)) {
            return;
        }

        [$claves[$posicion], $claves[$destino]] = [$claves[$destino], $claves[$posicion]];

        $ordenados = [];

        foreach ($claves as $clave) {
            $ordenados[$clave] = $bloques[$clave];
        }

        $this->{$field} = $ordenados;
    }

    /**
     * Un formulario que nace vacío no tiene dónde escribir. Se llama desde
     * `mount()`.
     */
    protected function ensureOneBlock(string $field): void
    {
        if ($this->blocksOf($field) === []) {
            $this->addBlock($field);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function blocksOf(string $field): array
    {
        if (! array_key_exists($field, $this->repeatableBlocks())) {
            throw new RuntimeException("El bloque repetible «{$field}» no está declarado en repeatableBlocks().");
        }

        /** @var array<string, array<string, mixed>> $bloques */
        $bloques = $this->{$field} ?? [];

        return $bloques;
    }
}
