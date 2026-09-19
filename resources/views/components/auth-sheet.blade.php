@props([
    'title' => '',
    'subtitle' => null,
    'step' => null,
])

{{--
    Every unauthenticated page is one of these. The treatment is a filled-in
    record sheet rather than a floating card: a ruled header band carrying the
    form's name, a heavier basalt rule under it, near-square corners, no shadow
    and no gradient. `step` orients people mid-way through password recovery,
    which is the one place a two-screen flow needs explaining.
--}}
<section {{ $attributes->merge(['class' => 'min-w-0 rounded-md border border-clay-200 bg-chalk']) }}>
    <header class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 border-b-2 border-basalt-800 bg-papyrus px-5 py-3.5">
        <h1 class="font-display text-section leading-tight text-basalt-900">{{ $title }}</h1>

        @if ($step)
            <p class="figure shrink-0 text-micro text-fern-500">{{ $step }}</p>
        @endif
    </header>

    <div class="px-5 py-5">
        @if ($subtitle)
            <p class="mb-5 text-dense leading-relaxed text-fern-500">{{ $subtitle }}</p>
        @endif

        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="border-t border-clay-100 bg-papyrus px-5 py-3 text-dense text-fern-500">
            {{ $footer }}
        </footer>
    @endisset
</section>
