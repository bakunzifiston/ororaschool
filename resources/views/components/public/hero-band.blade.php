@props(['image' => null, 'alt' => ''])

<section class="public-hero-banner relative isolate overflow-hidden">
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

    <div class="relative mx-auto max-w-6xl px-4 pt-10 pb-14 sm:px-6 sm:pt-12 sm:pb-16 lg:px-10 lg:pb-20">
        {{ $slot }}
    </div>
</section>
