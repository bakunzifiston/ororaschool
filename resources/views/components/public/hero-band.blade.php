@props(['image' => null, 'alt' => '', 'compact' => false, 'tall' => false])

<section {{ $attributes->class('public-hero-banner relative isolate overflow-hidden') }}>
    @if (filled($image))
        <img src="{{ asset($image) }}"
             alt="{{ $alt }}"
             width="1600" height="900"
             fetchpriority="high"
             class="absolute inset-0 h-full w-full object-cover object-[center_35%]">
        <div class="absolute inset-0 bg-gradient-to-r from-basalt-950/80 via-basalt-950/60 to-basalt-950/30"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-basalt-950/45 via-transparent to-basalt-950/20"></div>
    @else
        <div class="absolute inset-0 bg-basalt-900"></div>
    @endif

    <div @class([
        'relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-10',
        'pt-8 pb-10 sm:pt-10 sm:pb-12 lg:pb-14' => $compact,
        'flex min-h-[32rem] items-center pt-14 pb-16 sm:min-h-[38rem] sm:pt-16 sm:pb-20 lg:min-h-[42rem] lg:pt-20 lg:pb-24' => $tall,
        'pt-10 pb-14 sm:pt-12 sm:pb-16 lg:pb-20' => ! $compact && ! $tall,
    ])>
        {{ $slot }}
    </div>
</section>
