@props([
    'label' => '',
    'value' => '',
    'trend' => null,
    'direction' => null,
    'note' => null,
])

@php
    // Mono only for actual figures. A prose trend like "Oldest 4 days" in a
    // monospace face reads its capital O as a zero.
    $monoTrend = (bool) preg_match('/^[+\-−]?[\d.,]+\s*(%|pts)?$/u', (string) $trend);
@endphp

{{-- Accent rule on the leading edge, not a drop shadow: stat cards are the one
     surface that earns emphasis, and they earn it structurally. --}}
<div {{ $attributes->merge(['class' => 'min-w-0 rounded-md border border-clay-200 border-l-2 border-l-accent-500 bg-chalk px-4 py-3.5']) }}>
    <p class="truncate text-micro text-fern-500">{{ $label }}</p>

    <p class="figure mt-1.5 text-title leading-none text-basalt-900">{{ $value }}</p>

    @if ($trend || $note)
        <div class="mt-2 flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
            @if ($trend)
                <span @class([
                    'inline-flex items-center gap-0.5 text-micro font-medium',
                    'figure' => $monoTrend,
                    'text-ok' => $direction === 'up',
                    'text-danger' => $direction === 'down',
                    'text-fern-500' => ! in_array($direction, ['up', 'down'], true),
                ])>
                    @if ($direction === 'up')
                        <x-icon name="arrow-up" class="h-3 w-3" />
                    @elseif ($direction === 'down')
                        <x-icon name="arrow-down" class="h-3 w-3" />
                    @endif
                    {{ $trend }}
                </span>
            @endif

            @if ($note)
                <span class="text-micro text-fern-400">{{ $note }}</span>
            @endif
        </div>
    @endif
</div>
