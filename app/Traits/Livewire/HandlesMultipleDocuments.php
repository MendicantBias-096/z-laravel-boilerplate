<?php

namespace App\Traits\Livewire;

use Livewire\WithFileUploads;

/**
 * Maneja múltiples archivos en una sola colección desde un único input
 * Livewire, con buffer para no sobrescribir selecciones:
 *   incomingDocuments (input) → tempDocuments (pendientes) → savedDocuments.
 *
 * El componente debe:
 *   - exponer el modelo en $this->model (implementa Spatie\MediaLibrary\HasMedia)
 *   - llamar mountMultipleDocuments() dentro de su mount()
 *   - opcional: sobrescribir $documentCollectionName
 */
trait HandlesMultipleDocuments
{
    use WithFileUploads;

    /** Buffer del input; se resetea en cada selección. */
    public array $incomingDocuments = [];

    /** Archivos temporales acumulados antes de guardar. */
    public array $tempDocuments = [];

    /** Archivos ya guardados en Media Library. */
    public array $savedDocuments = [];

    /** Colección de Media Library donde se acumulan los archivos. */
    protected string $documentCollectionName = 'attachments';

    public function mountMultipleDocuments(): void
    {
        if (! $this->model) {
            return;
        }

        $this->savedDocuments = $this->model->getMedia($this->documentCollectionName)->all();
        $this->tempDocuments = [];
        $this->incomingDocuments = [];
    }

    /** Al cambiar el buffer del input, hace merge (sin duplicados) y lo limpia. */
    public function updatedIncomingDocuments($value): void
    {
        $newFiles = is_array($value) ? $value : [$value];

        foreach ($newFiles as $file) {
            $exists = collect($this->tempDocuments)->contains(
                fn ($existing) => $existing->getClientOriginalName() === $file->getClientOriginalName()
                    && $existing->getSize() === $file->getSize()
            );

            if (! $exists) {
                $this->tempDocuments[] = $file;
            }
        }

        $this->reset('incomingDocuments');
    }

    /** Guarda todos los temporales en la colección con nombre único. */
    public function saveMultipleDocuments(): void
    {
        if (empty($this->tempDocuments)) {
            return;
        }

        foreach ($this->tempDocuments as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $safeName = pathinfo($originalName, PATHINFO_FILENAME);
            $filename = "{$safeName}_".now()->format('YmdHis').".{$extension}";

            $this->model
                ->addMedia($file->getRealPath())
                ->usingFileName($filename)
                ->withCustomProperties(['original_name' => $originalName])
                ->toMediaCollection($this->documentCollectionName);
        }

        $this->savedDocuments = $this->model->getMedia($this->documentCollectionName)->all();
        $this->tempDocuments = [];
        $this->incomingDocuments = [];
    }

    /** Quita un temporal por índice. */
    public function removeTempDocument(int $index): void
    {
        unset($this->tempDocuments[$index]);
        $this->tempDocuments = array_values($this->tempDocuments);
    }

    /** Descarga un temporal por índice. */
    public function downloadDocument(int $key)
    {
        if (isset($this->tempDocuments[$key])) {
            $file = $this->tempDocuments[$key];

            return response()->download($file->getRealPath(), $file->getClientOriginalName());
        }

        abort(404, 'Archivo no encontrado.');
    }

    /** Quita un guardado por id de media. */
    public function removeSavedDocument(int $mediaId): void
    {
        $this->model->media()->find($mediaId)?->delete();

        $this->savedDocuments = $this->model->getMedia($this->documentCollectionName)->all();
    }
}
