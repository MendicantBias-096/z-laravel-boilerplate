<?php

namespace App\Traits\Livewire;

/**
 * Adds softDelete and restore actions to a Livewire table component.
 *
 * The component must define:
 *   protected string $modelClass      — FQCN of the Eloquent model
 *   protected string $deletePermission  — e.g. 'eliminar productos'
 *   protected string $restorePermission — e.g. 'restaurar productos'
 *   protected string $modelLabel        — e.g. 'Producto' (singular, capitalized)
 *
 * The component must also use TallStackUi\Traits\Interactions.
 *
 * El id viaja como `string|int` porque no todos los modelos numeran igual: unos
 * usan entero autoincremental y otros UUID o ULID. Llega como texto desde el
 * navegador, así que tipado `int` una clave no numérica o revienta con
 * `TypeError` (PHP 8.5) o —en versiones anteriores— llegaba convertida a `0`,
 * `find(0)` no encontraba nada y el toast de éxito salía igual: el botón
 * parecía funcionar y la fila seguía ahí (DAYA-237, LDT-6).
 */
trait HasSoftDeletes
{
    public function confirmDelete(string|int $id): void
    {
        $this->authorize($this->deletePermission);

        $model = strtolower($this->modelLabel);

        $this->dialog()
            ->question(
                __('table.dialog.delete.title', ['model' => $model]),
                __('table.dialog.delete.description'),
            )
            ->confirm(__('table.dialog.delete.confirm'), 'softDelete', $id)
            ->cancel(__('table.dialog.cancel'))
            ->send();
    }

    public function softDelete(string|int $id): void
    {
        $this->authorize($this->deletePermission);

        $model = ($this->modelClass)::find($id);

        // Entre que se pinta la fila y se confirma el diálogo pasa tiempo real,
        // y en ese hueco otro usuario pudo borrarla. Con `find($id)?->delete()`
        // el toast de éxito salía igual con la fila intacta delante: el peor de
        // los dos fallos posibles, porque el usuario deja de mirar.
        if ($model === null) {
            $this->toast()->error(__('app.error'), __('app.not_found', ['model' => $this->modelLabel]))->send();

            return;
        }

        $model->delete();

        $this->toast()->success(__('app.success'), __('app.soft_deleted', ['model' => $this->modelLabel]))->send();
    }

    public function confirmRestore(string|int $id): void
    {
        $this->authorize($this->restorePermission);

        $model = strtolower($this->modelLabel);

        $this->dialog()
            ->question(
                __('table.dialog.restore.title', ['model' => $model]),
                __('table.dialog.restore.description'),
            )
            ->confirm(__('table.dialog.restore.confirm'), 'restore', $id)
            ->cancel(__('table.dialog.cancel'))
            ->send();
    }

    public function restore(string|int $id): void
    {
        $this->authorize($this->restorePermission);

        $model = ($this->modelClass)::withTrashed()->find($id);

        if ($model === null) {
            $this->toast()->error(__('app.error'), __('app.not_found', ['model' => $this->modelLabel]))->send();

            return;
        }

        $model->restore();

        $this->toast()->success(__('app.success'), __('app.restored', ['model' => $this->modelLabel]))->send();
    }
}
