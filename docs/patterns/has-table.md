# HasTable

**Código canónico:** `app/Traits/Livewire/HasTable.php`
**Stack:** Livewire 4 · WithPagination

## Problema que resuelve

En cada componente `Table` se reescribía a mano `$search`, `$sort`, `$quantity`,
`updatingSearch()`, `clearFilters()`, `resetPage()` y un `updatingFilterX()` por
cada filtro. Los *searchable filters* se copiaban tabla por tabla y nunca se
generalizaron. Este trait absorbe todo eso; una tabla nueva solo declara sus
columnas y filtros.

## Cuándo usarlo

- Cualquier tabla Livewire con búsqueda, orden y/o filtros server-side.
- Cuando quieras filtros declarativos (`campo => operador`) en vez de un método por filtro.

## Cuándo NO usarlo

- Tablas triviales de solo lectura sin búsqueda ni orden.
- Si necesitas joins/agregados complejos, arma la query fuera y pásala a `applyTableQuery()`.

## Uso

```php
use App\Traits\Livewire\HasTable;

class Table extends Component
{
    use HasTable;

    protected function searchable(): array
    {
        return ['username', 'name', 'email'];
    }

    protected function filterable(): array
    {
        return ['email' => 'ilike', 'status' => '='];
    }

    protected function defaultSort(): array
    {
        return ['column' => 'username', 'direction' => 'asc'];
    }

    public function render()
    {
        $users = $this->applyTableQuery(User::query())->paginate($this->quantity);

        return view('livewire...', compact('users'));
    }
}
```

```blade
<x-ts-input wire:model.live.debounce.400ms="search" />
<x-ts-select wire:model.live="filters.status" :options="$statuses" />
<th wire:click="sortBy('username')">Usuario</th>
<x-ts-button wire:click="clearFilters">Limpiar filtros</x-ts-button>
```

`mountHasTable()` se ejecuta solo (Livewire llama `mount{Trait}` automáticamente):
inicializa el orden por defecto y las llaves de `filters`.

## Gotchas

- **Postgres:** la búsqueda usa `ilike %valor%` (case-insensitive). Si migras a
  MySQL/SQLite cambia `ilike` por `like` en el trait (marcado con `ponytail:`).
- `search` cubre las columnas de `searchable()` en un `orWhere` agrupado. Para
  buscar sobre relaciones (ej. `profile.first_name`) deja `searchable()` vacío y
  arma el `->when($this->search, ...)` en `render()`, luego encadena
  `applyTableQuery()` para filtros + orden (ver `User/Table`).
- Operadores de `filterable()`: `like`/`ilike` envuelven en `%valor%`; cualquier
  otro (`=`, `>=`, …) compara exacto.
- `filters.<campo>` se autoinicializa desde `filterable()`; `clearFilters()` limpia
  solo los filtros, no la búsqueda.
- Convive con `HasSoftDeletes`: ese trait aporta las acciones de borrado/restore.

## Mejorar cuando

- Se repita filtrar por rangos (fechas, montos) → agregar operadores `between`/`>=`.
- Se necesite búsqueda sobre relaciones en varias tablas → soportar `relacion.columna`.
