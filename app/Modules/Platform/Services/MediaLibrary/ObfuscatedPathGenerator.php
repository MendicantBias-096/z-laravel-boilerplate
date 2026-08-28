<?php

namespace App\Modules\Platform\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Guarda cada archivo en un directorio ofuscado (hash del id del media),
 * de modo que la ruta no revele el modelo ni el orden de subida. Útil para
 * documentos privados servidos siempre a través de la app, nunca por URL directa.
 *
 * Registrar en config/media-library.php:
 *   'path_generator' =>
 *       App\Modules\Platform\Services\MediaLibrary\ObfuscatedPathGenerator::class,
 *
 * ponytail: usa APP_KEY como sal; si rotas la key, los paths existentes quedan
 * huérfanos. Migra a un hash de una columna estable (uuid) si necesitas rotarla.
 */
class ObfuscatedPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return md5($media->id.config('app.key')).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive-images/';
    }
}
