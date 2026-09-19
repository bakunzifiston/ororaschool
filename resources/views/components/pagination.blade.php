@props(['pagination' => []])

@php
    $page = (int) ($pagination['page'] ?? 1);
    $pages = max(1, (int) ($pagination['pages'] ?? 1));
@endphp

<nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-between gap-3 px-3 py-2.5']) }}
     aria-label="Pagination">
    <p class="text-micro text-fern-500">
        Showing <span class="figure text-basalt-700">{{ $pagination['from'] ?? 0 }}–{{ $pagination['to'] ?? 0 }}</span>
        of <span class="figure text-basalt-700">{{ number_format($pagination['total'] ?? 0) }}</span>
    </p>

    <div class="flex items-center gap-1.5">
        @if (! empty($pagination['prev']))
            <x-button variant="secondary" size="sm" icon="chevron-left"
                      :href="$pagination['prev']" aria-label="Previous page">Previous</x-button>
        @else
            <x-button variant="secondary" size="sm" icon="chevron-left"
                      disabled aria-label="Previous page">Previous</x-button>
        @endif

        <span class="px-2 text-micro text-fern-500">
            Page <span class="figure text-basalt-700">{{ $page }}</span> of <span class="figure">{{ $pages }}</span>
        </span>

        @if (! empty($pagination['next']))
            <x-button variant="secondary" size="sm" icon-after="chevron-right"
                      :href="$pagination['next']" aria-label="Next page">Next</x-button>
        @else
            <x-button variant="secondary" size="sm" icon-after="chevron-right"
                      disabled aria-label="Next page">Next</x-button>
        @endif
    </div>
</nav>
