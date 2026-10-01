@props([
    'title' => '',
    'subtitle' => null,
    'labels' => [],
    'values' => [],
    'suffix' => '',
    'orientation' => 'vertical',
])

@php
    $max = max(1, ...(array) $values);
    $horizontal = $orientation === 'horizontal';
@endphp

<figure {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <figcaption>
        <h3 class="font-display text-panel font-semibold text-basalt-900">{{ $title }}</h3>
        @if ($subtitle)
            <p class="mt-0.5 text-micro text-fern-500">{{ $subtitle }}</p>
        @endif
    </figcaption>

    @if ($horizontal)
        <ul class="mt-4 grid gap-3" role="list">
            @foreach ($values as $i => $value)
                @php $width = (int) round(($value / $max) * 100); @endphp
                <li class="min-w-0">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="truncate text-dense text-basalt-800">{{ $labels[$i] ?? '' }}</span>
                        <span class="figure shrink-0 text-micro text-fern-500">{{ number_format((int) $value) }}{{ $suffix }}</span>
                    </div>
                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-clay-100" role="img"
                         aria-label="{{ $labels[$i] ?? '' }}: {{ number_format((int) $value) }}{{ $suffix }}">
                        <div class="h-full rounded-full bg-accent-500" style="width: {{ max(4, $width) }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="mt-4 flex h-36 items-end gap-1.5">
            @foreach ($values as $i => $value)
                @php $height = (int) round(($value / $max) * 100); @endphp
                <div class="flex min-w-0 flex-1 flex-col items-stretch justify-end gap-1">
                    <span class="figure self-center text-micro text-fern-500">{{ $value }}{{ $suffix }}</span>
                    <div class="w-full rounded-sm bg-accent-500"
                         style="height: {{ max(4, $height) }}%"
                         title="{{ $labels[$i] ?? '' }}: {{ $value }}{{ $suffix }}"></div>
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
