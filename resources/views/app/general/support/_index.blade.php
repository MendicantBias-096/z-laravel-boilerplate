<div class="flex flex-col gap-6 h-full">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-content">Asistente IA</h1>
            <p class="mt-1 text-sm text-content-muted">
                Pregúntame sobre usuarios, roles, permisos o el estado del sistema.
            </p>
        </div>
        @if(count($conversation) > 0)
            <x-ts-button wire:click="clear" color="red" outline>
                Limpiar
            </x-ts-button>
        @endif
    </div>

    {{-- Conversation --}}
    <x-ts-card class="flex-1">
        <div class="flex flex-col gap-4 min-h-64">

            {{-- Empty state --}}
            @if(count($conversation) === 0)
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="mb-4 rounded-full p-4" style="background: rgba(245,48,3,0.08)">
                        <x-ui.icon name="sparkles" class="size-8" style="color: #f53003" />
                    </div>
                    <p class="font-medium text-content">¿En qué puedo ayudarte?</p>
                    <p class="mt-1 text-sm text-content-muted">
                        Puedo consultar datos reales del sistema para responderte.
                    </p>

                    {{-- Sugerencias --}}
                    <div class="mt-6 flex flex-wrap justify-center gap-2">
                        @foreach([
                            '¿Cuántos usuarios hay registrados?',
                            '¿Qué roles existen en el sistema?',
                            '¿Cuáles son los últimos usuarios registrados?',
                        ] as $suggestion)
                            <button
                                wire:click="$set('message', '{{ $suggestion }}')"
                                class="rounded-full border px-3 py-1 text-xs text-content-muted transition hover:text-content"
                                style="border-color: var(--ui-border)"
                            >
                                {{ $suggestion }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Messages --}}
            @foreach($conversation as $msg)
                <div @class([
                    'flex gap-3',
                    'flex-row-reverse' => $msg['role'] === 'user',
                ])>
                    {{-- Avatar --}}
                    <div @class([
                        'flex size-8 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                        'text-white'                                         => $msg['role'] === 'user',
                        'text-content-muted border'                          => $msg['role'] === 'agent',
                    ])
                    style="{{ $msg['role'] === 'user'
                        ? 'background: linear-gradient(135deg, #f53003 0%, #c0392b 100%)'
                        : 'border-color: var(--ui-border); background: var(--ui-base)' }}"
                    >
                        {{ $msg['role'] === 'user' ? 'Tú' : 'IA' }}
                    </div>

                    {{-- Bubble --}}
                    <div @class([
                        'max-w-prose rounded-2xl px-4 py-3 text-sm',
                        'text-white rounded-tr-sm'  => $msg['role'] === 'user',
                        'text-content rounded-tl-sm' => $msg['role'] === 'agent',
                    ])
                    style="{{ $msg['role'] === 'user'
                        ? 'background: linear-gradient(135deg, #f53003 0%, #c0392b 100%)'
                        : 'background: var(--ui-base-muted); border: 1px solid var(--ui-border)' }}"
                    >
                        {!! nl2br(e($msg['content'])) !!}
                    </div>
                </div>
            @endforeach

            {{-- Thinking indicator --}}
            <div wire:loading wire:target="send" class="flex gap-3">
                <div class="flex size-8 shrink-0 items-center justify-center rounded-full border text-xs font-bold text-content-muted"
                     style="border-color: var(--ui-border); background: var(--ui-base)">
                    IA
                </div>
                <div class="flex items-center gap-1 rounded-2xl rounded-tl-sm px-4 py-3"
                     style="background: var(--ui-base-muted); border: 1px solid var(--ui-border)">
                    <span class="size-2 animate-bounce rounded-full bg-content-subtle" style="animation-delay: 0ms"></span>
                    <span class="size-2 animate-bounce rounded-full bg-content-subtle" style="animation-delay: 150ms"></span>
                    <span class="size-2 animate-bounce rounded-full bg-content-subtle" style="animation-delay: 300ms"></span>
                </div>
            </div>

        </div>

        {{-- Input --}}
        <x-slot:footer>
            <form wire:submit="send" class="flex gap-2">
                <x-ts-input
                    wire:model="message"
                    placeholder="Escribe tu pregunta..."
                    class="flex-1"
                    autocomplete="off"
                />
                <x-ts-button type="submit" wire:loading.attr="disabled">
                    <x-ui.icon name="paper-airplane" class="size-4" />
                    Enviar
                </x-ts-button>
            </form>
        </x-slot:footer>
    </x-ts-card>

</div>
