@props(['name' => '', 'size' => 'md', 'onBasalt' => false])

@php
    $parts = array_values(array_filter(preg_split('/[\s\-]+/', trim($name)) ?: []));
    $initials = strtoupper(
        mb_substr($parts[0] ?? '', 0, 1) . mb_substr(count($parts) > 1 ? end($parts) : '', 0, 1)
    );

    $sizes = [
        'sm' => 'h-6 w-6 text-[0.625rem]',
        'md' => 'h-8 w-8 text-micro',
        'lg' => 'h-10 w-10 text-dense',
    ];

    $tone = $onBasalt
        ? 'border-basalt-700 bg-basalt-800 text-clay-200'
        : 'border-clay-200 bg-clay-100 text-basalt-800';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-md border font-medium ' . $tone . ' ' . ($sizes[$size] ?? $sizes['md'])]) }}
      title="{{ $name }}" aria-hidden="true">
    <span class="figure">{{ $initials }}</span>
</span>
