<div>

    {{-- ── Barra superior ──────────────────────────────────────────────── --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

        {{-- Tabs --}}
        <div class="flex gap-1 rounded-lg border border-line bg-panel p-1">
            <button
                type="button"
                wire:click="$set('tab', 'unread')"
                @class([
                    'rounded-md px-4 py-1.5 text-sm font-medium transition-colors cursor-pointer',
                    'bg-primary-500 text-white shadow-sm' => $tab === 'unread',
                    'text-content-muted hover:text-content hover:bg-panel-alt' => $tab !== 'unread',
                ])
            >
                {{ __('notifications.tab_unread') }}
            </button>
            <button
                type="button"
                wire:click="$set('tab', 'all')"
                @class([
                    'rounded-md px-4 py-1.5 text-sm font-medium transition-colors cursor-pointer',
                    'bg-primary-500 text-white shadow-sm' => $tab === 'all',
                    'text-content-muted hover:text-content hover:bg-panel-alt' => $tab !== 'all',
                ])
            >
                {{ __('notifications.tab_all') }}
            </button>
        </div>

        {{-- Marcar todas como leídas --}}
        @if ($tab === 'unread' && $notifications->total() > 0)
            <button
                type="button"
                wire:click="markAllAsRead"
                wire:confirm="{{ __('notifications.confirm_mark_all') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-panel px-3 py-2 text-sm text-content-muted transition-colors hover:bg-panel-alt hover:text-content cursor-pointer"
            >
                @svg('lucide-check-check', 'size-4')
                {{ __('notifications.mark_all_read') }}
            </button>
        @endif
    </div>

    {{-- ── Lista de notificaciones ─────────────────────────────────────── --}}
    @if ($notifications->count() > 0)
        <div class="overflow-hidden rounded-lg border border-line bg-panel">
            @foreach ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data     = $notification->data;
                @endphp
                <div @class([
                    'flex items-start gap-4 border-b border-line px-5 py-4 last:border-b-0 transition-colors',
                    'bg-primary-50/50 dark:bg-primary-950/20' => $isUnread,
                ])>
                    {{-- Indicador --}}
                    <div class="mt-1.5 flex shrink-0">
                        @if ($isUnread)
                            <span class="size-2.5 rounded-full bg-primary-500"></span>
                        @else
                            <span class="size-2.5 rounded-full bg-transparent"></span>
                        @endif
                    </div>

                    {{-- Contenido --}}
                    <button
                        type="button"
                        wire:click="goToNotification('{{ $notification->id }}')"
                        class="min-w-0 flex-1 text-left cursor-pointer"
                    >
                        <p class="text-sm font-semibold text-content">{{ $data['title'] ?? '' }}</p>
                        <p class="mt-0.5 text-sm text-content-muted line-clamp-2">{{ $data['message'] ?? '' }}</p>
                        <p class="mt-1.5 text-xs text-content-subtle">{{ $notification->created_at->diffForHumans() }}</p>
                    </button>

                    {{-- Acciones --}}
                    <div
                        x-data="{ open: false }"
                        @click.outside="open = false"
                        class="relative shrink-0"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            class="cursor-pointer rounded-full p-1.5 text-content-subtle hover:bg-panel-alt hover:text-content"
                        >
                            @svg('lucide-ellipsis-vertical', 'size-4')
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition
                            class="absolute end-0 z-10 mt-1 w-48 overflow-hidden rounded-lg border border-line bg-panel shadow-lg"
                        >
                            @if ($isUnread)
                                <button
                                    type="button"
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    @click="open = false"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-sm text-content-muted hover:bg-panel-alt cursor-pointer"
                                >
                                    @svg('lucide-check', 'size-4 shrink-0')
                                    {{ __('notifications.mark_read') }}
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="markAsUnread('{{ $notification->id }}')"
                                    @click="open = false"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-sm text-content-muted hover:bg-panel-alt cursor-pointer"
                                >
                                    @svg('lucide-circle', 'size-4 shrink-0')
                                    {{ __('notifications.mark_unread') }}
                                </button>
                            @endif

                            <button
                                type="button"
                                wire:click="deleteNotification('{{ $notification->id }}')"
                                @click="open = false"
                                class="flex w-full items-center gap-2 px-4 py-2 text-sm text-danger hover:bg-danger/10 cursor-pointer"
                            >
                                @svg('lucide-trash-2', 'size-4 shrink-0')
                                {{ __('notifications.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if ($notifications->hasPages())
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 text-sm text-content-muted">
                    {{ __('table.showing') }}
                    <select
                        wire:model.live="quantity"
                        class="rounded-md border border-line bg-panel px-2 py-1 text-sm text-content focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    {{ __('table.results') }} {{ $notifications->total() }}
                </div>
                {{ $notifications->links('tallstack-ui::components.table.paginators') }}
            </div>
        @endif

    @else
        {{-- Estado vacío --}}
        <div class="flex flex-col items-center justify-center rounded-lg border border-line bg-panel py-16">
            @svg('lucide-bell-off', 'size-12 text-content-subtle mb-4')
            <p class="text-sm font-medium text-content-muted">
                {{ $tab === 'unread' ? __('notifications.empty_unread') : __('notifications.empty_all') }}
            </p>
        </div>
    @endif

</div>
