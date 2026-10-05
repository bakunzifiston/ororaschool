@props([
    'title' => '',
    'subtitle' => null,
    'headline' => null,
    'trend' => null,
    'trendNote' => null,
    'labels' => [],
    'values' => [],
    'suffix' => '',
    'orientation' => 'vertical',
    'max' => null,
])

@php
    $ceiling = $max !== null ? max(1, (int) $max) : max(1, ...(array) $values);
    $horizontal = $orientation === 'horizontal';
@endphp

<figure {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <figcaption>
        <h3 class="font-display text-panel font-semibold text-basalt-900">{{ $title }}</h3>
        @if ($headline)
            <p class="figure mt-2 text-title leading-none text-basalt-900">{{ $headline }}</p>
            @if ($trend || $trendNote)
                <p class="mt-1.5 flex flex-wrap items-baseline gap-x-1.5 text-micro">
                    @if ($trend)
                        <span class="font-medium text-ok">{{ $trend }}</span>
                    @endif
                    @if ($trendNote)
                        <span class="text-fern-500">{{ $trendNote }}</span>
                    @endif
                </p>
            @endif
        @endif
        @if ($subtitle)
            <p class="mt-0.5 text-micro text-fern-500">{{ $subtitle }}</p>
        @endif
    </figcaption>

    @if ($horizontal)
        @php
            $barColors = ['bg-accent-500', 'bg-ok', 'bg-st-approved', 'bg-st-pending', 'bg-st-active', 'bg-st-completed'];
        @endphp
        <ul class="mt-4 grid gap-2.5" role="list">
            @foreach ($values as $i => $value)
                @php $width = (int) round(($value / $ceiling) * 100); @endphp
                <li class="min-w-0">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="truncate text-dense text-basalt-800">{{ $labels[$i] ?? '' }}</span>
                        <span class="figure shrink-0 text-micro text-fern-500">{{ number_format((int) $value) }}{{ $suffix }}</span>
                    </div>
                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-clay-100" role="img"
                         aria-label="{{ $labels[$i] ?? '' }}: {{ number_format((int) $value) }}{{ $suffix }}">
                        <div class="h-full rounded-full {{ $barColors[$i % count($barColors)] }}" style="width: {{ max(4, $width) }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="mt-4 flex h-40 items-end gap-2">
            @foreach ($values as $i => $value)
                @php $height = (int) round(($value / $ceiling) * 100); @endphp
                <div class="flex min-w-0 flex-1 flex-col items-stretch justify-end gap-1.5">
                    <span class="figure self-center text-micro text-fern-500">{{ $value }}{{ $suffix }}</span>
                    <div class="flex h-32 justify-center overflow-hidden rounded-sm bg-clay-100">
                        <div class="mt-auto w-full rounded-t-sm bg-accent-500"
                             style="height: {{ max(4, $height) }}%"
                             title="{{ $labels[$i] ?? '' }}: {{ $value }}{{ $suffix }}"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2 flex gap-1.5">
            @foreach ($labels as $label)
                <span class="min-w-0 flex-1 truncate text-center text-micro text-fern-500">{{ $label }}</span>
            @endforeach
        </div>
    @endif
</figure>
