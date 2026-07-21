# HandlesModelDocuments

**Código canónico:** `app/Traits/Livewire/HandlesModelDocuments.php`
**Stack:** Livewire 4 · WithFileUploads · Spatie Media Library

## Problema que resuelve

Formularios con documentos de tipo fijo (INE, CV, comprobante de domicilio…),
uno por campo. Cada componente reimplementaba subir + preview de lo guardado +
borrar + descargar. El trait deja todo el ciclo listo; el componente solo declara
las etiquetas.

## Cuándo usarlo

- Un modelo con **slots nombrados** de documentos (una colección Media Library por slot, `singleFile`).

## Cuándo NO usarlo

- Varios archivos sueltos en una sola colección → usa [handles-multiple-documents](handles-multiple-documents.md).

## Uso

```php
use App\Traits\Livewire\HandlesModelDocuments;

class Form extends Component
{
    use HandlesModelDocuments;

    public ?User $model = null;

    public function mount(User $user): void
    {
        $this->model = $user;
        $this->mountDocuments();
    }

    public function documentLabels(): array
    {
        return ['ine' => 'INE', 'cv' => 'Currículum', 'proof' => 'Comprobante'];
    }
}
```

El modelo debe implementar `HasMedia` y registrar cada colección (idealmente
`->singleFile()`). Guarda el nombre original en la custom property `original_name`
y lo usa al descargar.

```blade
@foreach ($this->documentLabels() as $key => $label)
    <x-ts-input type="file" wire:model="documents.{{ $key }}" :label="$label" />
    <x-ts-button wire:click="uploadDocument('{{ $key }}')">Subir</x-ts-button>
    @if ($mediaFiles[$key] ?? null)
        <x-ts-button wire:click="downloadDocument('{{ $key }}')">Descargar</x-ts-button>
        <x-ts-button wire:click="deleteDocument('{{ $key }}')">Eliminar</x-ts-button>
    @endif
@endforeach
```

## Gotchas

- `mountDocuments()` **debe** llamarse en `mount()`, después de asignar `$this->model`.
- `deleteDocument()` limpia la colección completa (correcto para slots `singleFile`).
- Renombra a `nombre_YmdHis.ext` para evitar colisiones; el nombre visible sale de `original_name`.

## Mejorar cuando

- Se necesite validar tipo/tamaño por slot → agregar reglas en `documentLabels()` (ej. `['ine' => ['label' => 'INE', 'mimes' => 'pdf,jpg']]`).
