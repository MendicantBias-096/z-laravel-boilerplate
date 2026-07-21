# DownloadsDocuments

**Código canónico:** `app/Traits/Livewire/DownloadsDocuments.php`
**Stack:** Livewire 4 · Spatie Media Library · ZipArchive

## Problema que resuelve

Descargar **todos** los documentos de un modelo en un solo ZIP, agrupados por
colección, con nombres originales. Complementa a los traits de subida.

## Cuándo usarlo

- Botón "descargar todo" en una ficha con varios documentos/adjuntos.

## Cuándo NO usarlo

- Descarga de un único archivo → `downloadDocument()` de los traits de documentos.

## Uso

```php
use App\Traits\Livewire\DownloadsDocuments;

class Show extends Component
{
    use DownloadsDocuments;

    public function downloadAll()
    {
        return $this->downloadAllDocumentsForModel($this->requisition, 'requisicion_42.zip');
    }
}
```

Estructura del ZIP: `{collection_name}/{original_name}`. Los archivos faltantes en
disco se omiten y se registran en el log (no rompen la descarga).

## Gotchas

- Escribe un temporal en `storage/app/temp/` y lo borra con `deleteFileAfterSend(true)`.
- Devuelve una respuesta HTTP → llámalo desde una acción que retorne (no en `mount`).
- ZIP en memoria de disco: para volúmenes muy grandes considera stream a S3.

## Mejorar cuando

- Se repita filtrar por colección específica → aceptar `array $collections` opcional.
