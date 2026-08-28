<div
    x-data="{ open: false }"
    x-init="
        if (window.Echo) {
            window.Echo.private('App.Models.User.{{ auth()->id() }}')
                .listen('.new-notification', () => {
                    $wire.refreshCount();
                });
        }
    "
    @click.outside="open = false"
    class="relative"
>

    {{-- Trigger --}}
    <button
        type="button"
        @click="open = !open"
        class="relative inline-flex cursor-pointer items-center justify-center rounded-md border border-line bg-panel p-2 text-content-subtle hover:bg-panel-alt hover:text-content focus:outline-none transition-colors"
    >
        @svg('lucide-bell', 'size-5')

        @if ($unreadCount > 0)
            <span class="absolute -top-1.5 -end-1.5 flex items-center justify-center rounded-full bg-danger px-1.5 min-w-[1.25rem] h-5 text-[10px] font-bold text-white shadow-sm">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute end-0 z-50 mt-2 w-96 origin-top-right rounded-xl border border-line bg-panel shadow-xl"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <h3 class="text-sm font-semibold text-content">{{ __('platform::notifications.title') }}</h3>
            @if ($unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="cursor-pointer text-xs font-medium text-primary-500 hover:text-primary-600 transition-colors"
                >
                    {{ __('platform::notifications.mark_all_read') }}
                </button>
            @endif
        </div>

        {{-- Notification list (hidden scrollbar) --}}
        <div
            x-ref="notifScroll"
            x-data="{ activeAction: null }"
            class="max-h-[22rem] overflow-y-auto"
            style="-ms-overflow-style: none; scrollbar-width: none;"
        >

            @forelse ($grouped as $label => $notifications)
                {{-- Time divider --}}
                <div class="sticky top-0 z-10 border-b border-line bg-panel-alt/80 backdrop-blur-sm px-4 py-1.5">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-content-subtle">{{ $label }}</span>
                </div>

                @foreach ($notifications as $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $data = $notification->data;
                    @endphp
                    <div @class([
                        'group relative flex items-center gap-3 border-b border-line px-4 py-3 last:border-b-0 transition-colors',
                        'bg-primary-50/50 dark:bg-primary-950/20' => $isUnread,
                        'hover:bg-panel-alt' => !$isUnread,
                    ])>
                        {{-- Unread indicator --}}
                        <div class="flex shrink-0">
                            @if ($isUnread)
                                <span class="size-2 rounded-full bg-primary-500"></span>
                            @else
                                <span class="size-2 rounded-full bg-transparent"></span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <button
                            type="button"
                            wire:click="goToNotification('{{ $notification->id }}')"
                            @click="open = false"
                            class="min-w-0 flex-1 text-left cursor-pointer"
                        >
                            <p class="text-sm font-medium text-content line-clamp-1">{{ $data['title'] ?? '' }}</p>
                            <p class="mt-0.5 text-xs text-content-muted line-clamp-2">{{ $data['message'] ?? '' }}</p>
                            <p class="mt-1 text-[11px] text-primary-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </button>

                        {{-- Action menu (smart placement) --}}
                        <div class="relative shrink-0" x-data="{
                            placement: 'bottom',
                            toggle(el) {
                                if (activeAction === '{{ $notification->id }}') {
                                    activeAction = null;
                                    return;
                                }
                                const scroll = $refs.notifScroll;
                                if (scroll) {
                                    const btnRect = el.getBoundingClientRect();
                                    const scrollRect = scroll.getBoundingClientRect();
                                    const spaceBelow = scrollRect.bottom - btnRect.bottom;
                                    this.placement = spaceBelow < 90 ? 'top' : 'bottom';
                                }
                                activeAction = '{{ $notification->id }}';
                            }
                        }">
                            <button
                                type="button"
                                @click.stop="toggle($el)"
                                class="cursor-pointer rounded-full p-1 text-content-subtle opacity-0 group-hover:opacity-100 hover:bg-panel-alt hover:text-content transition-all"
                                :class="{ '!opacity-100': activeAction === '{{ $notification->id }}' }"
                            >
                                @svg('lucide-ellipsis-vertical', 'size-4')
                            </button>

                            <div
                                x-show="activeAction === '{{ $notification->id }}'"
                                x-cloak
                                x-transition
                                @click.outside="activeAction = null"
                                class="absolute end-0 z-20 w-44 overflow-hidden rounded-lg border border-line bg-panel shadow-lg"
                                :class="placement === 'top' ? 'bottom-full mb-1' : 'top-full mt-1'"
                            >
                                @if ($isUnread)
                                    <button
                                        type="button"
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                        @click="activeAction = null"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-xs text-content-muted hover:bg-panel-alt cursor-pointer"
                                    >
                                        @svg('lucide-check', 'size-3.5 shrink-0')
                                        {{ __('platform::notifications.mark_read') }}
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        wire:click="markAsUnread('{{ $notification->id }}')"
                                        @click="activeAction = null"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-xs text-content-muted hover:bg-panel-alt cursor-pointer"
                                    >
                                        @svg('lucide-circle', 'size-3.5 shrink-0')
                                        {{ __('platform::notifications.mark_unread') }}
                                    </button>
                                @endif

                                <button
                                    type="button"
                                    wire:click="deleteNotification('{{ $notification->id }}')"
                                    @click="activeAction = null"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-xs text-danger hover:bg-danger/10 cursor-pointer"
                                >
                                    @svg('lucide-trash-2', 'size-3.5 shrink-0')
                                    {{ __('platform::notifications.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="flex flex-col items-center justify-center py-10">
                    @svg('lucide-bell-off', 'size-8 text-content-subtle mb-2')
                    <p class="text-xs text-content-muted">{{ __('platform::notifications.empty_all') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="border-t border-line">
            <a
                href="{{ route('platform.notifications.index') }}"
                wire:navigate
                @click="open = false"
                class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-primary-500 hover:bg-panel-alt transition-colors rounded-b-xl"
            >
                @svg('lucide-eye', 'size-4')
                {{ __('platform::notifications.view_all') }}
            </a>
        </div>
    </div>
</div>
