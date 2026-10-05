@props([
    'title' => '',
    'subtitle' => null,
    'headline' => null,
    'caption' => null,
    'icon' => null,
    'labels' => [],
    'values' => [],
    'suffix' => '',
])

@php
    $palette = [
        'Published' => '#1B7A4A',
        'Approved' => '#5A9A1F',
        'Pending review' => '#3B82F6',
        'Draft' => '#EAB308',
        'Archived' => '#F97316',
        'In progress' => 'var(--color-accent-500)',
        'Completed' => 'var(--color-ok)',
        'Active' => 'var(--color-ok)',
        'Inactive' => 'var(--color-st-pending)',
    ];
    $fallback = [
        'var(--color-ok)',
        'var(--color-accent-500)',
        'var(--color-st-approved)',
        'var(--color-st-pending)',
        'var(--color-st-completed)',
        'var(--color-st-archived)',
    ];
    $total = array_sum(array_map('intval', (array) $values));
    $radius = 34;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference * 0.25;
    $slices = [];

    foreach ((array) $values as $index => $value) {
        $amount = (int) $value;
        $label = $labels[$index] ?? '';
        $length = $total > 0 ? ($amount / $total) * $circumference : 0;
        $percent = $total > 0 ? (int) round(($amount / $total) * 100) : 0;

        $slices[] = [
            'label' => $label,
            'value' => $amount,
            'percent' => $percent,
            'color' => $palette[$label] ?? $fallback[$index % count($fallback)],
            'dash' => $length.' '.($circumference - $length),
            'offset' => $offset,
        ];

        $offset -= $length;
    }
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

    @if ($total === 0)
        <p class="mt-6 text-dense text-fern-500">No figures to plot yet.</p>
    @else
        <div class="mt-6 flex flex-wrap items-center gap-8">
            <div class="relative mx-auto h-48 w-48 shrink-0">
                <svg viewBox="0 0 100 100" class="h-full w-full" role="img" aria-hidden="true">
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="var(--color-clay-100)" stroke-width="10" />
                    @foreach ($slices as $slice)
                        @if ($slice['value'] > 0)
                            <circle cx="50" cy="50" r="{{ $radius }}" fill="none"
                                    stroke="{{ $slice['color'] }}"
                                    stroke-width="10"
                                    stroke-dasharray="{{ $slice['dash'] }}"
                                    stroke-dashoffset="{{ $slice['offset'] }}"
                                    stroke-linecap="butt" />
                        @endif
                    @endforeach
                </svg>
                @if ($headline)
                    <p class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="figure text-title font-semibold leading-none text-basalt-900">{{ $headline }}</span>
                        @if ($caption)
                            <span class="mt-1 text-micro text-fern-500">{{ $caption }}</span>
                        @endif
                    </p>
                @endif
            </div>

            <ul class="min-w-0 flex-1 space-y-2.5" role="list">
                @foreach ($slices as $slice)
                    <li class="flex items-baseline justify-between gap-3 text-dense">
                        <span class="flex min-w-0 items-center gap-2">
                            <span class="h-2 w-2 shrink-0 rounded-full" style="background: {{ $slice['color'] }}" aria-hidden="true"></span>
                            <span class="truncate text-basalt-800">{{ $slice['label'] }}</span>
                        </span>
                        <span class="figure shrink-0 text-micro text-fern-500">
                            {{ number_format($slice['value']) }}{{ $suffix }}
                            <span class="text-fern-400">· {{ $slice['percent'] }}%</span>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</figure>
