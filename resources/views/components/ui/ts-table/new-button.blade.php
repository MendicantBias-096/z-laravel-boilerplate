@props([
    'route',
    'permission',
    'model',
])

@can($permission)
    <div class="mb-4 flex justify-end">
        <a href="{{ route($route) }}" wire:navigate
           class="inline-flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
           style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
            @svg('lucide-plus', 'size-4')
            {{ __('table.new', ['model' => $model]) }}
        </a>
    </div>
@endcan
