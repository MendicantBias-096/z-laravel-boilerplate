---
name: livewire-development
description: >-
    Develops reactive Livewire 4 components inside app/Modules. Activates when creating, updating,
    or modifying Livewire components; working with wire:model, wire:click, wire:loading, or any
    wire: directives; adding real-time updates, loading states, or reactivity; debugging component
    behavior; or when the user mentions Livewire, component, or reactive UI.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## Component structure

Every Livewire component lives inside its module and follows the
wrapper/`_component` convention:

```
app/Modules/{Context}/Livewire/{Resource}/Index.php               ← class
app/Modules/{Context}/Resources/views/{resource}/_index.blade.php ← component view
app/Modules/{Context}/Resources/views/{resource}/index.blade.php  ← wrapper, has the layout
```

The three names derive mechanically:

```
class      App\Modules\Billing\Livewire\Invoices\Index
view       billing::invoices._index
livewire   @livewire('billing::invoices.index')
```

**The Livewire name carries the `::` namespace.** `@livewire('billing::invoices.index')`,
never `@livewire('billing.invoices.index')` — the second resolves to a different
component or to nothing, with no error either way.

### Class

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Livewire\{Resource};

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render(): Factory|View
    {
        return view('{context}::{resource}._index');
    }
}
```

### Wrapper

```blade
<x-layouts.app icon="lucide-{icon}" parent="{Context_es}" title="{Title}">
    {{ Breadcrumbs::render('{context}.{resource}.index') }}
    @livewire('{context}::{resource}.index')
</x-layouts.app>
```

The layout goes here and only here — never `->layout()` in `render()`.

---

## Registration is not automatic

A module's components resolve because its ServiceProvider declares them:

```php
Livewire::addNamespace(
    self::NAMESPACE,
    viewPath: __DIR__.'/Resources/views',
    classNamespace: __NAMESPACE__.'\\Livewire',
);
```

`addNamespace`, not `addLocation`: the second derives the name by trimming the
prefix, so two modules with the same subfolder produce the same name and the
first registered wins, silently. A component that renders nothing is usually a
missing or wrong registration, not a broken component.

---

## Form objects

`app/Modules/{Context}/Livewire/Forms/{Model}Form.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\{Context}\Livewire\Forms;

use App\Modules\{Context}\Models\{Model};
use Livewire\Form;

class {Model}Form extends Form
{
    // `?string` porque la clave es UUID. Con `$table->id()` sería `?int`.
    public ?string $id = null;

    public string $name = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function store(): {Model}
    {
        return {Model}::updateOrCreate(
            ['id' => $this->id],
            $this->only('name'),
        );
    }
}
```

---

## Table components

Do not hand-roll list state. Two traits in Platform own it:

- `App\Modules\Platform\Traits\Livewire\HasTable` — `$search`, `$quantity`,
  `$sort`, `$filters`, the `updating*` hooks, `sortBy()`, `clearFilters()` and
  `applyTableQuery()`. Declare `searchable()`, `filterable()` and
  `defaultSort()`; do not re-declare the properties.
- `App\Modules\Platform\Traits\Livewire\HasSoftDeletes` — `confirmDelete()`,
  `softDelete()`, `confirmRestore()`, `restore()`. Reads four `protected`
  strings: `$modelClass`, `$deletePermission`, `$restorePermission`,
  `$modelLabel`.

For a full CRUD use the `create-crud` skill rather than assembling it here.

### Row ids

```php
// string|int, nunca int: llegan como texto desde el navegador.
public function softDelete(string|int $id): void
```

```blade
{{-- Js::from(), nunca interpolado: Livewire evalúa wire:click como una
     expresión, así que un UUID desnudo muere en un SyntaxError. --}}
wire:click="confirmDelete({{ \Illuminate\Support\Js::from($row->id) }})"
```

Integer keys satisfy both by accident, so a mistake only surfaces on the first
UUID-keyed table. `app/Modules/Access/Tests/Feature/Users/ClaveNoNumericaTest.php` guards it.

---

## Key directives

```blade
{{-- Two-way binding --}}
<input wire:model="property" />
<input wire:model.live="property" />              {{-- every keystroke --}}
<input wire:model.live.debounce.400ms="search" /> {{-- for search inputs --}}

{{-- Actions --}}
<button wire:click="methodName">Click</button>
<form wire:submit="save">...</form>

{{-- Loading states --}}
<button wire:loading.attr="disabled">Guardar</button>
<span wire:loading.class="opacity-0">Texto normal</span>

{{-- SPA navigation --}}
<a href="{{ route('...') }}" wire:navigate>Enlace</a>
```

Use `wire:loading.class` for transitions; it animates where toggling
`display:none` snaps.

---

## After save

```php
public function save(): void
{
    $this->form->validate();
    $this->form->store();

    $this->toast()
        ->success(
            __('platform::app.success'),
            __('platform::app.created', ['model' => __('{context}::{resource}.singular')])
        )
        ->flash()
        ->send();

    $this->redirect(route('{context}.{resource}.index'), navigate: true);
}
```

`->flash()` is what survives the redirect. Without it the toast is built and
discarded on the same request, and nothing shows.

---

## Text goes through `__()`

The boilerplate ships `es` and `en`. A hardcoded string renders identically in
both, which is a bug that only the second language reveals. Module strings live
in `app/Modules/{Context}/Resources/lang/{es,en}/`; shared ones in
`platform::app` and `platform::table`.

Check the key exists before using it:

```bash
grep -rn "'save'" app/Modules/Platform/Resources/lang/es/app.php
```

A missing key renders as its own path — `platform::app.save` in the middle of
the page.

---

## Authorization

Validate and authorize in the action, on the server. A `@can` in Blade hides a
button; it does not stop the request.

```php
public function softDelete(string|int $id): void
{
    $this->authorize($this->deletePermission);
    // …
}
```

An `auth()` call belongs in the UI layer — a Livewire component or an HTTP
class. Actions and Models receive the actor as a parameter (R18): in a queued
job there is no session and `auth()` returns null in silence.

---

## Rules

- The layout lives in the wrapper view. Never `->layout()` in `render()`.
- A route points at a view, never at a Livewire class.
- Use Alpine only for client-only behavior — toggles, local state, animation.
- `$this->redirect(..., navigate: true)` after a save.
- Never `sleep()` to test a loading state.
