@unless ($breadcrumbs->isEmpty())
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex flex-wrap items-center gap-1 text-sm">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{ $breadcrumb->url }}" wire:navigate
                           class="text-content-muted transition-colors hover:text-content">
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                    <li class="font-medium text-content" aria-current="page">
                        {{ $breadcrumb->title }}
                    </li>
                @endif

                @unless ($loop->last)
                    <li class="select-none text-content-subtle">/</li>
                @endunless

            @endforeach
        </ol>
    </nav>
@endunless
