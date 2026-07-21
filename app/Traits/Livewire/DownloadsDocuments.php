<?php

namespace App\Traits\Livewire;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Descarga todos los documentos de un modelo (Spatie Media Library) en un
 * único ZIP, agrupados por colección. Los archivos faltantes se omiten y
 * se registran en el log.
 */
trait DownloadsDocuments
{
    public function downloadAllDocumentsForModel($model, ?string $zipName = null)
    {
        $mediaItems = $model->media;

        if ($mediaItems->isEmpty()) {
            return back()->with('error', 'Este registro no tiene documentos adjuntos.');
        }

        $zipFileName = $zipName ?? Str::slug(class_basename($model)).'_documentos.zip';
        $zipPath = storage_path("app/temp/{$zipFileName}");

        if (! file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        foreach ($mediaItems as $media) {
            $filePath = $media->getPath();
            $fileName = $media->getCustomProperty('original_name') ?? $media->file_name;

            if (! file_exists($filePath)) {
                Log::warning('Archivo no encontrado', ['path' => $filePath, 'media_id' => $media->id]);

                continue;
            }

            $zip->addFile($filePath, "{$media->collection_name}/{$fileName}");
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
