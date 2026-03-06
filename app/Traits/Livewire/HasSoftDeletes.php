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
 */
trait HasSoftDeletes
{
    public function confirmDelete(int $id): void
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

    public function softDelete(int $id): void
    {
        $this->authorize($this->deletePermission);

        ($this->modelClass)::find($id)?->delete();

        $this->toast()->success(__('app.success'), __('app.soft_deleted', ['model' => $this->modelLabel]))->send();
    }

    public function confirmRestore(int $id): void
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

    public function restore(int $id): void
    {
        $this->authorize($this->restorePermission);

        ($this->modelClass)::withTrashed()->find($id)?->restore();

        $this->toast()->success(__('app.success'), __('app.restored', ['model' => $this->modelLabel]))->send();
    }
}
