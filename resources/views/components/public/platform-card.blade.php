@props(['platform' => [], 'detail' => false])

@php
    $glyphs = [
        'ororafarm' => 'sprout',
        'gemura' => 'droplet',
        'buchapro' => 'tag',
        'feedgrid' => 'layers',
    ];
    $glyph = $glyphs[$platform['slug'] ?? ''] ?? 'book';
    $copy = $detail
        ? ($platform['description'] ?? $platform['tagline'] ?? '')
        : ($platform['tagline'] ?? $platform['description'] ?? '');
    $count = $platform['public_courses'] ?? null;
@endphp

<a href="{{ $platform['href'] ?? '#' }}"
   {{ $attributes->merge(['class' => 'public-card public-card-hover group flex flex-col p-5']) }}>
    <span class="public-card-icon inline-flex h-10 w-10 items-center justify-center rounded-md bg-accent-50 text-accent-500 transition-transform duration-150">
        <x-icon :name="$glyph" class="h-5 w-5" />
    </span>
    <span class="mt-4 font-display text-panel font-semibold text-basalt-900">{{ $platform['name'] ?? '' }}</span>
    <span class="mt-1 text-micro font-medium text-accent-700">{{ $platform['discipline'] ?? '' }}</span>
    <span class="mt-2 grow text-dense leading-relaxed text-fern-600">{{ $copy }}</span>
    @if ($detail && is_int($count))
        <span class="mt-3 text-micro text-fern-500">{{ $count }} {{ $count === 1 ? 'published course' : 'published courses' }}</span>
    @endif
    <span class="mt-4 inline-flex items-center gap-1 text-dense font-medium text-accent-700">
        Explore platform
        <x-icon name="arrow-right" class="h-3.5 w-3.5" />
    </span>
</a>
