---
name: livewire-development
description: >-
  Develops reactive Livewire 4 components. Activates when creating, updating, or modifying
  Livewire components; working with wire:model, wire:click, wire:loading, or any wire: directives;
  adding real-time updates, loading states, or reactivity; debugging component behavior;
  or when the user mentions Livewire, component, or reactive UI.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## When to Apply

Activate this skill when:
- Creating or modifying Livewire components
- Using wire: directives (model, click, loading, navigate, sort)
- Implementing forms with validation
- Adding loading states or transitions
- Debugging Livewire reactivity

---

## Component Structure in This Boilerplate

Every Livewire component follows the wrapper/_component convention:

```
app/Livewire/App/{Domain}/{Module}/Index.php   ← class
resources/views/app/{domain}/{module}/_index.blade.php  ← view
resources/views/app/{domain}/{module}/index.blade.php   ← wrapper (has layout)
```

### Class template

```php
<?php

namespace App\Livewire\App\{Domain}\{Module};

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    public function render()
    {
        return view('app.{domain}.{module}._index');
    }
}
```

### Wrapper template

```blade
<x-layouts.app>
    @livewire('app.{domain}.{module}.index')
</x-layouts.app>
```

---

## Livewire Form Objects

For create/edit forms, use Livewire Form objects:

```php
<?php

namespace App\Livewire\Forms;

use App\Models\{Model};
use Livewire\Form;

class {Model}Form extends Form
{
    public ?int $id = null;
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

## Key Directives

```blade
{{-- Two-way binding --}}
<input wire:model="property" />
<input wire:model.live="property" /> {{-- updates on every keystroke --}}

{{-- Actions --}}
<button wire:click="methodName">Click</button>
<form wire:submit="save">...</form>

{{-- Loading states (prefer over display:none) --}}
<button wire:loading.attr="disabled">Save</button>
<span wire:loading>Saving...</span>
<span wire:loading.class="opacity-0">Normal text</span>
<span wire:loading.class.remove="hidden">Loading text</span>

{{-- SPA navigation --}}
<a href="{{ route('...') }}" wire:navigate>Link</a>
```

---

## After Save Pattern

```php
public function save(): void
{
    $this->form->validate();
    $this->form->store();

    $this->toast()
        ->success('Éxito', 'Guardado correctamente.')
        ->flash()
        ->send();

    $this->redirect(route('general.module.index'), navigate: true);
}
```

---

## Rules

- Never use `->layout()` in `render()`.
- Never use `sleep()` for testing loading states.
- Use `$this->redirect(..., navigate: true)` for SPA navigation after actions.
- Use `wire:loading.class` instead of `wire:loading` + `display:none` for smooth transitions.
