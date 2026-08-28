<?php

namespace App\Modules\Platform\Traits\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\WithPagination;

/**
 * Generaliza la lógica repetida de las tablas Livewire: búsqueda, orden,
 * paginación y filtros declarativos. Reemplaza el copy-paste de $search,
 * updatingSearch(), clearFilters(), etc. en cada componente Table.
 *
 * El componente sobrescribe:
 *   searchable(): array   — columnas donde busca $search
 *       ['username', 'name', 'email']
 *   filterable(): array   — filtro => operador SQL
 *       ['email' => 'like', 'status' => '=']
 *   defaultSort(): array  — orden inicial
 *       ['column' => 'username', 'direction' => 'asc']
 *
 * En render() encadena la query:
 *   $users = $this->applyTableQuery(User::query())->paginate($this->quantity);
 *
 * En la vista:
 *   wire:model.live.debounce="search"
 *   wire:model.live="filters.email"
 *   wire:click="sortBy('username')" / wire:click="clearFilters"
 */
trait HasTable
{
    use WithPagination;

    public string $search = '';

    public int $quantity = 25;

    /** @var array<string, string> */
    public array $sort = [];

    /**
     * Valores actuales de cada filtro declarado en filterable().
     *
     * @var array<string, mixed>
     */
    public array $filters = [];

    public function mountHasTable(): void
    {
        if (empty($this->sort)) {
            $this->sort = $this->defaultSort();
        }

        foreach (array_keys($this->filterable()) as $key) {
            $this->filters[$key] ??= '';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if (($this->sort['column'] ?? null) === $column) {
            $this->sort['direction'] = ($this->sort['direction'] ?? 'asc') === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sort = ['column' => $column, 'direction' => 'asc'];
    }

    public function clearFilters(): void
    {
        foreach (array_keys($this->filters) as $key) {
            $this->filters[$key] = '';
        }

        $this->resetPage();
    }

    /**
     * Aplica búsqueda + filtros + orden a la query. Llamar en render().
     *
     * Los genéricos son obligatorios: PHPStan atribuye el error del trait
     * a cada clase que lo usa, así que sin ellos todo Table nuevo nace con
     * cuatro errores que su baseline no cubre y el CI se pone rojo.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function applyTableQuery(Builder $query): Builder
    {
        $searchable = $this->searchable();

        // ponytail: `ilike` es de Postgres y hace la búsqueda insensible a
        // mayúsculas; en MySQL o SQLite hay que cambiarlo por `like`.
        if ($this->search !== '' && $searchable !== []) {
            $query->where(function (Builder $q) use ($searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'ilike', "%{$this->search}%");
                }
            });
        }

        foreach ($this->filterable() as $column => $operator) {
            $value = $this->filters[$column] ?? '';

            if ($value === '') {
                continue;
            }

            in_array($operator, ['like', 'ilike'], true)
                ? $query->where($column, $operator, "%{$value}%")
                : $query->where($column, $operator, $value);
        }

        if (! empty($this->sort['column'])) {
            // `orderBy` solo acepta 'asc' o 'desc'. La dirección viene de una
            // propiedad pública de Livewire, o sea del navegador: normalizarla
            // aquí es lo que impide que llegue cualquier cosa a la consulta.
            $direction = ($this->sort['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

            $query->orderBy($this->sort['column'], $direction);
        }

        return $query;
    }

    /** Columnas donde busca $search. Sobrescribir en el componente. */
    protected function searchable(): array
    {
        return [];
    }

    /** Filtros disponibles => operador SQL. Sobrescribir en el componente. */
    protected function filterable(): array
    {
        return [];
    }

    /** Orden inicial. Sobrescribir si aplica. */
    protected function defaultSort(): array
    {
        return ['column' => 'id', 'direction' => 'desc'];
    }
}
