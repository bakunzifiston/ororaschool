@props(['platform' => [], 'detail' => false])

@php
    $glyphs = [
        'ororafarm' => 'sprout',
        'gemura' => 'droplet',
        'buchapro' => 'tag',
        'feedgrid' => 'layers',
    ];
    $glyph = $glyphs[$platform['slug'] ?? ''] ?? 'book';
    $copy = $platform['tagline'] ?? $platform['description'] ?? '';
    $count = $platform['public_courses'] ?? null;
    $cover = $platform['cover'] ?? null;
    $name = $platform['name'] ?? '';
    $discipline = $platform['discipline'] ?? '';
    $region = $platform['region'] ?? '';
@endphp

<a href="{{ $platform['href'] ?? '#' }}"
   {{ $attributes->merge(['class' => 'public-card public-card-hover group flex h-full min-w-0 flex-col overflow-hidden']) }}>
    <span class="relative aspect-[16/10] overflow-hidden bg-accent-50">
        @if ($cover)
            <img src="{{ asset($cover) }}"
                 alt="{{ $name }}"
                 width="1400" height="875"
                 loading="lazy"
                 decoding="async"
                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.04]">
        @else
            <span class="flex h-full items-center justify-center">
                <x-icon :name="$glyph" class="h-8 w-8 text-accent-400" />
            </span>
        @endif
        @if ($discipline !== '')
            <span class="absolute left-3 top-3 rounded-full bg-chalk/90 px-2.5 py-1 text-micro font-medium text-basalt-800 backdrop-blur-sm">
                {{ $discipline }}
            </span>
        @endif
    </span>

    <span class="flex grow flex-col p-5">
        <span class="font-display text-panel font-semibold leading-snug text-basalt-900 group-hover:text-accent-700">
            {{ $name }}
        </span>

        @if ($copy !== '')
            <span class="mt-2 line-clamp-2 text-dense leading-relaxed text-fern-600">{{ $copy }}</span>
        @endif

        <span class="mt-auto flex flex-col gap-3 pt-4">
            @if ($detail && ($region !== '' || is_int($count)))
                <span class="flex flex-wrap items-center gap-x-2 gap-y-1 text-micro text-fern-500">
                    @if ($region !== '')
                        <span>{{ $region }}</span>
                    @endif
                    @if ($region !== '' && is_int($count))
                        <span class="text-clay-300" aria-hidden="true">·</span>
                    @endif
                    @if (is_int($count))
                        <span>{{ $count }} {{ $count === 1 ? 'published course' : 'published courses' }}</span>
                    @endif
                </span>
            @endif

            <span class="inline-flex items-center gap-1 text-dense font-medium text-accent-700">
                Explore academy
                <x-icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5" />
            </span>
        </span>
    </span>
</a>
