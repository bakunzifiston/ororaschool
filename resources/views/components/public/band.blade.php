@props(['tone' => 'basalt'])

<section {{ $attributes->merge(['class' => 'marketing-band-basalt on-basalt bg-basalt-900 text-chalk']) }}>
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20 lg:px-10 lg:py-24">
        {{ $slot }}
    </div>
</section>
