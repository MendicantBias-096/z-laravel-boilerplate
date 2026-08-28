<div>
    @can('crear usuario')
        <a href="{{ route('access.users.create') }}" wire:navigate
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white"
           style="background: linear-gradient(135deg, #f53003 0%, #c0392b 100%);">
            @svg('lucide-plus', 'size-4')
            {{ __('platform::table.new', ['model' => mb_strtolower(__('platform::table.users.headers.username'))]) }}
        </a>
    @endcan
</div>
