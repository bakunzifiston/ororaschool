@props([
    'platform' => [],
    'href' => null,
    'variant' => 'tile',
    'description' => null,
    'meta' => null,
])

@php
    /**
     * One glyph map for Home, Platforms index, platform show and the public
     * course landing. `tile` is the linked card; `mark` is the square icon;
     * `banner` is the wide course-landing field.
     */
    $glyphs = [
        'ororafarm' => 'sprout',
        'gemura' => 'droplet',
        'buchapro' => 'tag',
        'feedgrid' => 'layers',
    ];
    $glyph = $glyphs[$platform['slug'] ?? ''] ?? 'book';
    $link = $href ?? ($platform['href'] ?? null);
    $copy = $description ?? $platform['tagline'] ?? $platform['description'] ?? '';
    $count = $platform['public_courses'] ?? null;
    $countLabel = $meta ?? (is_int($count)
        ? $count.' '.($count === 1 ? 'published course' : 'published courses')
        : null);
@endphp

@if ($variant === 'mark')
    <span {{ $attributes->merge(['class' => 'inline-flex h-20 w-20 shrink-0 items-center justify-center rounded-md border border-clay-200 bg-accent-50 text-accent-500']) }}>
        <x-icon :name="$glyph" class="h-9 w-9" />
    </span>
@elseif ($variant === 'banner')
    <div {{ $attributes->merge(['class' => 'flex h-40 items-center justify-center rounded-md border border-clay-200 bg-accent-50']) }}>
        <x-icon :name="$glyph" class="h-10 w-10 text-accent-400" />
    </div>
@else
    <a href="{{ $link }}"
       {{ $attributes->merge(['class' => 'flex gap-4 rounded-md border border-clay-200 bg-chalk p-5 transition-colors hover:border-clay-300']) }}>
        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-accent-50 text-accent-500">
            <x-icon :name="$glyph" class="h-6 w-6" />
        </span>
        <span class="min-w-0">
            <span class="block font-display text-panel font-semibold text-basalt-900">{{ $platform['name'] ?? '' }}</span>
            @if ($copy !== '')
                <span class="mt-1 block text-dense leading-relaxed text-fern-500">{{ $copy }}</span>
            @endif
            @if ($countLabel)
                <span class="mt-2 block text-micro text-fern-500">{{ $countLabel }}</span>
            @endif
        </span>
    </a>
@endif
