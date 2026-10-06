@props([
    'title' => '',
    'description' => null,
    'href' => null,
    'link' => null,
])

<section {{ $attributes->merge(['class' => 'mt-8 min-w-0']) }}>
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div class="min-w-0 max-w-2xl">
            <h2 class="font-display text-section text-basalt-900">{{ $title }}</h2>
            @if ($description)
                <p class="mt-1 text-dense leading-relaxed text-fern-500">{{ $description }}</p>
            @endif
        </div>

        @if ($href && $link)
            <a href="{{ $href }}" class="text-micro font-medium text-accent-700 hover:underline">{{ $link }}</a>
        @elseif (isset($actions))
            <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
        @endif
    </div>

    <div class="mt-5">
        {{ $slot }}
    </div>
</section>
