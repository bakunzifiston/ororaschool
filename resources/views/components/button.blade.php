@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconAfter' => null,
    'href' => null,
])

@php
    $variants = [
        'primary' => 'bg-accent-500 text-accent-on border border-accent-500 hover:bg-accent-600 hover:border-accent-600',
        'secondary' => 'bg-chalk text-basalt-800 border border-clay-200 hover:border-fern-400 hover:bg-clay-100',
        'ghost' => 'bg-transparent text-fern-500 border border-transparent hover:bg-clay-100 hover:text-basalt-900',
        'danger' => 'bg-transparent text-danger border border-danger/40 hover:bg-danger-bg hover:border-danger',
    ];

    $sizes = [
        'sm' => 'h-11 px-2.5 text-micro gap-1.5 sm:h-7',
        'md' => 'h-11 px-3.5 text-dense gap-2 sm:h-9',
        'lg' => 'h-11 px-5 text-body gap-2',
    ];

    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-md font-medium transition-colors',
        'disabled:opacity-45 disabled:pointer-events-none whitespace-nowrap',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon) <x-icon :name="$icon" class="h-3.5 w-3.5" /> @endif
        {{ $slot }}
        @if ($iconAfter) <x-icon :name="$iconAfter" class="h-3.5 w-3.5" /> @endif
    </a>
@else
    <button type="{{ $attributes->get('type', 'button') }}"
            {{ $attributes->except('type')->merge(['class' => $classes]) }}>
        @if ($icon) <x-icon :name="$icon" class="h-3.5 w-3.5" /> @endif
        {{ $slot }}
        @if ($iconAfter) <x-icon :name="$iconAfter" class="h-3.5 w-3.5" /> @endif
    </button>
@endif
