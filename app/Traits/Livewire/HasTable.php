<?php

namespace App\Traits\Livewire;

use Illuminate\Database\Eloquent\Builder;
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

    /** ['column' => string, 'direction' => 'asc'|'desc'] */
    public array $sort = [];

    /** Valores actuales de cada filtro declarado en filterable(). */
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

    /** Aplica búsqueda + filtros + orden a la query. Llamar en render(). */
    protected function applyTableQuery(Builder $query): Builder
    {
        $searchable = $this->searchable();

        // ponytail: ilike = Postgres (búsqueda case-insensitive); cambiar a like si migras a MySQL/SQLite
        if ($this->search !== '' && $searchable !== []) {
            $query->where(function (Builder $q) use ($searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'ilike', "%{$this->search}%");
                }
            });
        }

        foreach ($this->filterable() as $column => $operator) {
            $value = $this->filters[$column] ?? '';

            if ($value === '' || $value === null) {
                continue;
            }

            in_array($operator, ['like', 'ilike'], true)
                ? $query->where($column, $operator, "%{$value}%")
                : $query->where($column, $operator, $value);
        }

        if (! empty($this->sort['column'])) {
            $query->orderBy($this->sort['column'], $this->sort['direction'] ?? 'asc');
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
