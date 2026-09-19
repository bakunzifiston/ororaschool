@props([
    'value' => 0,
    'label' => null,
    'meta' => null,
    'showValue' => true,
    'size' => 'md',
])

@php
    $value = max(0, min(100, (int) $value));
    $complete = $value >= 100;
    $track = $size === 'sm' ? 'h-1' : 'h-1.5';
@endphp

<div {{ $attributes->merge(['class' => 'w-full min-w-0']) }}>
    @if ($label || $showValue)
        <div class="mb-1.5 flex items-baseline justify-between gap-3">
            @if ($label)
                <span class="truncate text-dense text-basalt-800">{{ $label }}</span>
            @endif
            @if ($showValue)
                <span class="figure shrink-0 text-micro {{ $complete ? 'text-st-completed' : 'text-fern-500' }}">{{ $value }}%</span>
            @endif
        </div>
    @endif

    <div class="{{ $track }} w-full overflow-hidden rounded-xs bg-clay-200"
         role="progressbar"
         aria-valuenow="{{ $value }}"
         aria-valuemin="0"
         aria-valuemax="100"
         aria-label="{{ $label ?? 'Progress' }}">
        <div class="h-full rounded-xs {{ $complete ? 'bg-st-completed' : 'bg-accent-500' }}"
             style="width: {{ $value }}%"></div>
    </div>

    @if ($meta)
        <p class="mt-1.5 text-micro text-fern-500">{{ $meta }}</p>
    @endif
</div>
