@props(['items' => [], 'solid' => false])

@php
    $columns = count($items);
@endphp

<div {{ $attributes->merge(['class' => 'kpi-strip']) }}>
    <div @class([
        'grid grid-cols-1 gap-3 sm:grid-cols-2',
        'lg:grid-cols-3' => $columns === 3,
        'lg:grid-cols-4' => $columns === 4,
        'lg:grid-cols-5' => $columns >= 5,
    ])>
        @foreach ($items as $item)
            @php
                $href = $item['href'] ?? null;
                $monoTrend = (bool) preg_match('/^[+\-−]?[\d.,]+\s*(%|pts)?$/u', (string) ($item['trend'] ?? ''));
                $direction = $item['direction'] ?? null;
                $tint = $item['tint'] ?? 'accent';
                $tints = [
                    'accent' => 'bg-accent-50 text-accent-600',
                    'green' => 'kpi-tint kpi-tint-green',
                    'blue' => 'kpi-tint kpi-tint-blue',
                    'violet' => 'kpi-tint kpi-tint-violet',
                    'amber' => 'kpi-tint kpi-tint-amber',
                ];
                $cellClass = [
                    'estate-kpi app-stat min-w-0 rounded-lg border border-clay-200 bg-chalk px-3 py-2.5',
                    'hover:border-clay-300' => (bool) $href,
                ];
            @endphp

            @if ($href)
                <a href="{{ $href }}" @class($cellClass)>
            @else
                <div @class($cellClass)>
            @endif
                    <div class="estate-kpi-head flex items-center justify-between gap-2">
                        <div class="estate-kpi-title flex min-w-0 items-center gap-2.5">
                            @if (! empty($item['icon']))
                                <span @class(['estate-kpi-icon inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full', $tints[$tint] ?? $tints['accent']]) aria-hidden="true">
                                    <x-icon :name="$item['icon']" :solid="$solid" class="h-4 w-4" />
                                </span>
                            @endif
                            <p class="estate-kpi-label truncate text-micro text-fern-500">{{ $item['label'] }}</p>
                        </div>
                        @if ($href)
                            <x-icon name="arrow-right" class="estate-kpi-arrow h-3.5 w-3.5 shrink-0 text-fern-400" />
                        @endif
                    </div>

                    <p class="estate-kpi-value mt-2 text-section font-semibold leading-none text-basalt-900">{{ $item['value'] }}</p>

                    @if (! empty($item['trend']) || ! empty($item['note']))
                        <p class="estate-kpi-meta mt-2 flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5 text-micro text-fern-500">
                            @if (! empty($item['trend']))
                                <span @class([
                                    'estate-kpi-trend',
                                    'is-up' => $direction === 'up',
                                    'is-down' => $direction === 'down',
                                    'is-warn' => $direction === 'warn',
                                ])>
                                    @if ($direction === 'up')
                                        <x-icon name="arrow-up" class="h-3 w-3" />
                                    @elseif ($direction === 'down')
                                        <x-icon name="arrow-down" class="h-3 w-3" />
                                    @endif
                                    {{ $item['trend'] }}
                                </span>
                            @endif
                            @if (! empty($item['note']))
                                <span>{{ $item['note'] }}</span>
                            @endif
                        </p>
                    @endif
            @if ($href)
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
</div>
