@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'labels' => [],
    'values' => [],
    'suffix' => '',
])

@php
    $series = array_map('intval', (array) $values);
    $count = count($series);
    $rawMax = $count > 0 ? max(1, ...$series) : 1;
    $magnitude = 10 ** (int) max(0, floor(log10($rawMax)));
    $scaled = $rawMax / $magnitude;
    $nice = match (true) {
        $scaled <= 1 => 1,
        $scaled <= 2 => 2,
        $scaled <= 5 => 5,
        default => 10,
    };
    $ceiling = (int) ($nice * $magnitude);
    $ticks = [0, (int) round($ceiling / 3), (int) round($ceiling * 2 / 3), $ceiling];
    $width = 400;
    $height = 176;
    $padLeft = 6;
    $padRight = 10;
    $padTop = 14;
    $padBottom = 8;
    $innerWidth = $width - $padLeft - $padRight;
    $innerHeight = $height - $padTop - $padBottom;
    $points = [];

    foreach ($series as $index => $value) {
        $x = $count === 1
            ? $padLeft + ($innerWidth / 2)
            : $padLeft + (($index / ($count - 1)) * $innerWidth);
        $y = $padTop + $innerHeight - (($value / $ceiling) * $innerHeight);
        $points[] = [
            'x' => round($x, 2),
            'y' => round($y, 2),
            'value' => $value,
            'label' => $labels[$index] ?? '',
        ];
    }

    $polyline = implode(' ', array_map(fn (array $point): string => $point['x'].','.$point['y'], $points));
    $area = $count === 0
        ? ''
        : $padLeft.','.($height - $padBottom).' '.$polyline.' '.($width - $padRight).','.($height - $padBottom);
@endphp

<figure {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <figcaption>
        <div class="flex items-start gap-2.5">
            @if ($icon)
                <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent-50 text-accent-600" aria-hidden="true">
                    <x-icon :name="$icon" class="h-3.5 w-3.5" />
                </span>
            @endif
            <div class="min-w-0">
                <h3 class="font-medium text-basalt-900">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="mt-0.5 text-micro text-fern-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </figcaption>

    @if ($count === 0)
        <p class="mt-6 text-dense text-fern-500">No figures to plot yet.</p>
    @else
        <div class="mt-6 flex gap-3">
            <div class="flex h-44 shrink-0 flex-col justify-between py-1">
                @foreach (array_reverse($ticks) as $tick)
                    <span class="figure text-micro leading-none text-fern-400">{{ number_format($tick) }}</span>
                @endforeach
            </div>

            <div class="min-w-0 flex-1">
                <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-44 w-full" role="img" aria-label="{{ $title }}">
                    @foreach ($ticks as $tick)
                        @php
                            $y = $padTop + $innerHeight - (($tick / $ceiling) * $innerHeight);
                        @endphp
                        <line x1="{{ $padLeft }}" y1="{{ $y }}" x2="{{ $width - $padRight }}" y2="{{ $y }}"
                              stroke="var(--color-clay-100)" stroke-width="1" />
                    @endforeach
                    @if ($area !== '')
                        <polygon points="{{ $area }}" fill="#5A9A1F" fill-opacity="0.12" />
                    @endif
                    @if ($count > 1)
                        <polyline points="{{ $polyline }}" fill="none" stroke="#3f8c1f" stroke-width="2.25"
                                  stroke-linecap="round" stroke-linejoin="round" />
                    @endif
                    @foreach ($points as $point)
                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4.5"
                                fill="#fff" stroke="#3f8c1f" stroke-width="2.25">
                            <title>{{ $point['label'] }}: {{ number_format($point['value']) }}{{ $suffix }}</title>
                        </circle>
                    @endforeach
                </svg>

                <div class="mt-2 flex gap-1">
                    @foreach ($labels as $label)
                        <span class="min-w-0 flex-1 truncate text-center text-micro text-fern-500">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</figure>
