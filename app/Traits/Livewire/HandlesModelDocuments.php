<?php

namespace App\Traits\Livewire;

use Livewire\WithFileUploads;

/**
 * Maneja documentos con "slot nombrado" (un archivo por colección) usando
 * Spatie Media Library en componentes Livewire: carga, preview, borrado y
 * descarga.
 *
 * El componente debe:
 *   - exponer el modelo en $this->model (implementa Spatie\MediaLibrary\HasMedia)
 *   - sobrescribir documentLabels(): ['ine' => 'INE', 'cv' => 'Currículum']
 *   - llamar mountDocuments() dentro de su mount()
 *
 * Opcional: registrar un PathGenerator en config/media-library.php para
 * personalizar la ruta de almacenamiento.
 */
trait HandlesModelDocuments
{
    use WithFileUploads;

    /** Documentos temporales subidos desde el frontend (Livewire). */
    public array $documents = [];

    /** Archivos ya almacenados en Media Library, indexados por colección. */
    public array $mediaFiles = [];

    /** Inicializa los archivos guardados para su visualización. Llamar en mount(). */
    public function mountDocuments(): void
    {
        if (! $this->model) {
            return;
        }

        foreach ($this->documentKeys() as $collection) {
            $this->mediaFiles[$collection] = $this->model->getFirstMedia($collection);
            $this->documents[$collection] = null;
        }
    }

    /** Guarda el documento subido en la colección, con nombre único y seguro. */
    public function uploadDocument(string $collection): void
    {
        if (! ($this->documents[$collection] ?? null)) {
            return;
        }

        $originalName = $this->documents[$collection]->getClientOriginalName();
        $extension = $this->documents[$collection]->getClientOriginalExtension();
        $safeName = pathinfo($originalName, PATHINFO_FILENAME);
        $filename = "{$safeName}_".now()->format('YmdHis').".{$extension}";

        $media = $this->model
            ->addMedia($this->documents[$collection]->getRealPath())
            ->usingFileName($filename)
            ->withCustomProperties(['original_name' => $originalName])
            ->toMediaCollection($collection);

        $this->mediaFiles[$collection] = $media;
        $this->documents[$collection] = null;
    }

    /** Elimina el documento de la colección. */
    public function deleteDocument(string $collection): void
    {
        $this->model->clearMediaCollection($collection);

        $this->mediaFiles[$collection] = null;
        $this->documents[$collection] = null;

        $this->dispatch('$refresh');
    }

    /** Descarga el documento con su nombre original. */
    public function downloadDocument(string $collection)
    {
        $media = $this->model->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        $originalName = $media->getCustomProperty('original_name') ?? $media->file_name;

        return response()->download($media->getPath(), $originalName);
    }

    /** Claves de documentos soportados. Por defecto, las de documentLabels(). */
    public function documentKeys(): array
    {
        return array_keys($this->documentLabels());
    }

    /** Nombre visible de cada documento. Sobrescribir en el componente. */
    public function documentLabels(): array
    {
        return [];
    }
}
