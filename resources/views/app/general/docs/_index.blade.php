<div class="grid w-full gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,4fr)] xl:grid-cols-[minmax(0,1fr)_minmax(0,3fr)_minmax(0,1fr)] xl:gap-10">

    {{-- Índice de documentos --}}
    <aside class="hidden lg:block">
        <div class="sticky top-32 max-h-[calc(100vh-11rem)] overflow-y-auto pb-8">
            @foreach ($this->grouped as $group => $documents)
                <p @class([
                    'px-3 text-[0.6875rem] font-semibold uppercase tracking-[0.08em] text-content-subtle',
                    'mt-7' => ! $loop->first,
                ])>
                    {{ __("docs.groups.{$group}") }}
                </p>

                <nav class="doc-nav mt-2 flex flex-col">
                    @foreach ($documents as $slug => $document)
                        <button
                            type="button"
                            wire:click="$set('doc', '{{ $slug }}')"
                            @class([
                                'doc-link',
                                'doc-link--active' => $this->doc === $slug,
                            ])
                        >
                            {{ $document['title'] }}
                        </button>
                    @endforeach
                </nav>
            @endforeach
        </div>
    </aside>

    {{-- Documento --}}
    <article class="min-w-0">
        {{-- Bajo lg el índice lateral desaparece: un select nativo lo sustituye
             para que se pueda cambiar de documento en pantallas pequeñas. --}}
        <label class="mb-7 block lg:hidden">
            <span class="sr-only">{{ __('docs.title') }}</span>
            <select wire:model.live="doc" class="doc-select">
                @foreach ($this->grouped as $group => $documents)
                    <optgroup label="{{ __("docs.groups.{$group}") }}">
                        @foreach ($documents as $slug => $document)
                            <option value="{{ $slug }}">{{ $document['title'] }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </label>

        @if ($this->html === '')
            <p class="py-24 text-center text-sm text-content-muted">
                {{ __('docs.not_found') }}
            </p>
        @else
            <div class="markdown">{!! $this->html !!}</div>
        @endif
    </article>

    {{-- En esta página --}}
    @if ($this->outline !== [])
        <nav
            class="hidden xl:block"
            wire:key="outline-{{ $this->doc }}"
            x-data="{
                active: null,
                init() {
                    const headings = document.querySelectorAll('.markdown h1[id], .markdown h2[id]');
                    if (! headings.length) return;

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) this.active = entry.target.id;
                        });
                    }, { rootMargin: '-144px 0px -70% 0px' });

                    headings.forEach((heading) => observer.observe(heading));

                    // Con 73 entradas el resaltado queda fuera de vista casi
                    // siempre; el rail sigue a la lectura en vez de esperarla.
                    this.$watch('active', (id) => {
                        this.$el.querySelector(`[href='#${id}']`)
                            ?.scrollIntoView({ block: 'nearest' });
                    });

                    this.$el.addEventListener('alpine:destroyed', () => observer.disconnect());
                },
            }"
        >
            <div class="sticky top-32 max-h-[calc(100vh-11rem)] overflow-y-auto pb-8">
                <p class="text-[0.6875rem] font-semibold uppercase tracking-[0.08em] text-content-subtle">
                    {{ __('docs.on_this_page') }}
                </p>

                <ul class="outline-list mt-3 flex flex-col gap-px">
                    @foreach ($this->outline as $entry)
                        <li>
                            <a
                                href="#{{ $entry['id'] }}"
                                @class([
                                    'outline-link',
                                    'outline-link--section' => $entry['level'] === 1,
                                ])
                                :class="active === '{{ $entry['id'] }}' && 'outline-link--current'"
                            >
                                @if ($entry['rule'])
                                    <span class="outline-link__id">{{ $entry['rule'] }}</span>
                                @endif
                                <span class="outline-link__label">{{ $entry['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>
    @endif

</div>

