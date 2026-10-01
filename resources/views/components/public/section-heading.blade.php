@props([
    'title',
    'subtitle' => null,
    'align' => 'left',
])

@php
    $centered = $align === 'center';
@endphp

<div {{ $attributes->merge(['class' => $centered ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl']) }}>
    <h2 class="marketing-title text-basalt-900">{{ $title }}</h2>
    @if (filled($subtitle))
        <p @class([
            'mt-3 text-read leading-relaxed text-fern-600',
            'mx-auto' => $centered,
        ])>{{ $subtitle }}</p>
    @endif
</div>
