@props([
    'title' => '',
    'subtitle' => null,
    'labels' => [],
    'values' => [],
    'suffix' => '',
])

@php
    $max = max(1, ...(array) $values);
@endphp

<figure {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <figcaption>
        <h3 class="font-display text-panel font-semibold text-basalt-900">{{ $title }}</h3>
        @if ($subtitle)
            <p class="mt-0.5 text-micro text-fern-500">{{ $subtitle }}</p>
        @endif
    </figcaption>

    <div class="mt-4 flex h-36 items-end gap-1.5">
        @foreach ($values as $i => $value)
            @php $height = (int) round(($value / $max) * 100); @endphp
            <div class="flex min-w-0 flex-1 flex-col items-stretch justify-end gap-1">
                <span class="figure self-center text-micro text-fern-500">{{ $value }}{{ $suffix }}</span>
                <div class="w-full rounded-xs bg-accent-500"
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
</figure>
