@props(['tone' => 'chalk', 'compact' => false])

@php
    $tones = [
        'chalk' => 'bg-chalk',
        'papyrus' => 'bg-papyrus',
        'accent' => 'bg-accent-50',
        'terrace' => 'marketing-band-accent bg-accent-500 text-chalk',
        'forest' => 'bg-basalt-950 text-chalk',
        'mist' => 'bg-st-approved-bg',
        'basalt' => 'marketing-band-basalt on-basalt bg-basalt-900 text-chalk',
    ];
@endphp

<section {{ $attributes->merge(['class' => ($tones[$tone] ?? $tones['chalk']).' '.($compact ? 'py-10 sm:py-14' : 'py-16 sm:py-20')]) }}>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10">
        {{ $slot }}
    </div>
</section>
