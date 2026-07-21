# HandlesMultipleDocuments

**Código canónico:** `app/Traits/Livewire/HandlesMultipleDocuments.php`
**Stack:** Livewire 4 · WithFileUploads · Spatie Media Library

## Problema que resuelve

Subir **N archivos** a una misma colección desde un input múltiple, sin que cada
nueva selección sobrescriba la anterior. Usa un buffer de tres etapas:
`incomingDocuments` (input) → `tempDocuments` (pendientes de guardar) → `savedDocuments`.

## Cuándo usarlo

- Adjuntos libres: "sube los documentos que necesites", galerías, evidencias.

## Cuándo NO usarlo

- Documentos de tipo fijo, uno por campo → usa [handles-model-documents](handles-model-documents.md).

## Uso

```php
use App\Traits\Livewire\HandlesMultipleDocuments;

class Form extends Component
{
    use HandlesMultipleDocuments;

    public ?Requisition $model = null;
    protected string $documentCollectionName = 'attachments';

    public function mount(Requisition $requisition): void
    {
        $this->model = $requisition;
        $this->mountMultipleDocuments();
    }
}
```

```blade
<x-ts-input type="file" multiple wire:model="incomingDocuments" />

{{-- Pendientes de guardar --}}
@foreach ($tempDocuments as $i => $file)
    {{ $file->getClientOriginalName() }}
    <x-ts-button wire:click="downloadDocument({{ $i }})">Ver</x-ts-button>
    <x-ts-button wire:click="removeTempDocument({{ $i }})">Quitar</x-ts-button>
@endforeach

<x-ts-button wire:click="saveMultipleDocuments">Guardar adjuntos</x-ts-button>

{{-- Ya guardados --}}
@foreach ($savedDocuments as $media)
    {{ $media->getCustomProperty('original_name') ?? $media->file_name }}
    <x-ts-button wire:click="removeSavedDocument({{ $media->id }})">Eliminar</x-ts-button>
@endforeach
```

## Gotchas

- `updatedIncomingDocuments()` deduplica por nombre + tamaño y **resetea el buffer**
  del input; no pongas `skipRender` ahí (la vista debe refrescarse).
- `savedDocuments` es un array plano de objetos `Media`, no una colección Eloquent.
- `mountMultipleDocuments()` debe ir en `mount()` tras asignar `$this->model`.

## Mejorar cuando

- Se necesite límite de cantidad/tamaño total → validar en `updatedIncomingDocuments()` antes del merge.
