@props(['state' => 'none'])

{{-- La casilla de las maestras: fila y columna. Distingue los tres estados,
     porque «algunos» y «ninguno» no pueden verse igual. --}}
<span @class([
    'flex size-4 shrink-0 items-center justify-center rounded border transition-colors',
    'border-primary-600 bg-primary-600 dark:border-primary-500 dark:bg-primary-500' => $state !== 'none',
    'border-content-subtle bg-panel' => $state === 'none',
])>
    @if ($state === 'all')
        @svg('lucide-check', 'size-2.5 text-white', ['aria-hidden' => 'true'])
    @elseif ($state === 'some')
        <span class="h-0.5 w-2 rounded-full bg-white"></span>
    @endif
</span>
