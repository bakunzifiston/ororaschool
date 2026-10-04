@props(['items' => []])

@php
    $columns = count($items);
    $tones = [
        'accent' => [
            'cell' => 'bg-accent-50 border-accent-100',
            'icon' => 'bg-accent-500 text-accent-on',
            'value' => 'text-accent-700',
        ],
        'ok' => [
            'cell' => 'bg-ok-bg border-ok/20',
            'icon' => 'bg-ok text-white',
            'value' => 'text-ok',
        ],
        'active' => [
            'cell' => 'bg-st-active-bg border-st-active/20',
            'icon' => 'bg-st-active text-white',
            'value' => 'text-st-active',
        ],
        'approved' => [
            'cell' => 'bg-st-approved-bg border-st-approved/20',
            'icon' => 'bg-st-approved text-white',
            'value' => 'text-st-approved',
        ],
        'pending' => [
            'cell' => 'bg-st-pending-bg border-st-pending/20',
            'icon' => 'bg-st-pending text-white',
            'value' => 'text-st-pending',
        ],
        'completed' => [
            'cell' => 'bg-st-completed-bg border-st-completed/20',
            'icon' => 'bg-st-completed text-white',
            'value' => 'text-st-completed',
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'kpi-strip']) }}>
    <div @class([
        'grid grid-cols-2 gap-3',
        'xl:grid-cols-4' => $columns <= 4,
        'xl:grid-cols-5' => $columns >= 5,
    ])>
        @foreach ($items as $item)
            @php
                $href = $item['href'] ?? null;
                $monoTrend = (bool) preg_match('/^[+\-−]?[\d.,]+\s*(%|pts)?$/u', (string) ($item['trend'] ?? ''));
                $direction = $item['direction'] ?? null;
                $tone = $tones[$item['tone'] ?? 'accent'] ?? $tones['accent'];
                $cellClass = [
                    'min-w-0 rounded-md border px-3.5 py-3',
                    $tone['cell'],
                    'block hover:brightness-[0.98]' => (bool) $href,
                ];
            @endphp

            @if ($href)
                <a href="{{ $href }}" @class($cellClass)>
            @else
                <div @class($cellClass)>
            @endif
                    <div class="flex items-start justify-between gap-2">
                        <p class="truncate text-micro text-basalt-700">{{ $item['label'] }}</p>
                        @if (! empty($item['icon']))
                            <span @class(['inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full', $tone['icon']]) aria-hidden="true">
                                <x-icon :name="$item['icon']" class="h-3.5 w-3.5" />
                            </span>
                        @endif
                    </div>

                    <p @class(['figure mt-1.5 text-section leading-none', $tone['value']])>{{ $item['value'] }}</p>

                    @if (! empty($item['trend']) || ! empty($item['note']))
                        <div class="mt-1.5 flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5">
                            @if (! empty($item['trend']))
                                <span @class([
                                    'inline-flex items-center gap-0.5 text-micro font-medium',
                                    'figure' => $monoTrend,
                                    'text-ok' => $direction === 'up',
                                    'text-st-pending' => $direction === 'warn',
                                    'text-danger' => $direction === 'down',
                                    'text-basalt-700' => ! in_array($direction, ['up', 'down', 'warn'], true),
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
                                <span class="text-micro text-basalt-700/80">{{ $item['note'] }}</span>
                            @endif
                        </div>
                    @endif
            @if ($href)
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
</div>
