@props([
    'title' => null,
    'subtitle' => null,
    'variant' => 'plain',
    'padded' => true,
])

@php
    /**
     * Three surface treatments by function, so panels are not all the same
     * rectangle: `plain` for editorial content, `table` for record lists (no
     * outer side borders, the table supplies its own head rule), `quiet` for
     * secondary asides.
     */
    $variants = [
        'plain' => 'rounded-md border border-clay-200 bg-chalk',
        'table' => 'rounded-md border border-clay-200 bg-chalk',
        'quiet' => 'rounded-md border border-clay-100 bg-papyrus',
    ];
@endphp

{{-- min-w-0 keeps a wide child (a table, a long identifier) from forcing the
     page into horizontal overflow when this panel is a grid or flex item. --}}
<section {{ $attributes->merge(['class' => 'min-w-0 ' . ($variants[$variant] ?? $variants['plain'])]) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-clay-200 px-4 py-3">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="truncate font-display text-panel font-semibold text-basalt-900">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-micro text-fern-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="{{ $padded ? 'p-4' : '' }}">
        {{ $slot }}
    </div>
</section>
