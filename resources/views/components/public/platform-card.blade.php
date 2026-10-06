@props(['platform' => [], 'detail' => false, 'showcase' => false])

@php
    $glyphs = [
        'ororafarm' => 'sprout',
        'gemura' => 'droplet',
        'buchapro' => 'tag',
        'feedgrid' => 'layers',
    ];
    $platformTones = [
        'ororafarm' => 'border-st-published/30 bg-st-published-bg text-st-published',
        'gemura' => 'border-st-approved/30 bg-st-approved-bg text-st-approved',
        'buchapro' => 'border-st-pending/30 bg-st-pending-bg text-st-pending',
        'feedgrid' => 'border-st-active/30 bg-st-active-bg text-st-active',
    ];
    $slug = $platform['slug'] ?? '';
    $glyph = $glyphs[$slug] ?? 'book';
    $platformTone = $platformTones[$slug] ?? 'border-accent-200 bg-accent-50 text-accent-700';
    $copy = $platform['tagline'] ?? $platform['description'] ?? '';
    $count = $platform['public_courses'] ?? null;
    $cover = $platform['cover'] ?? null;
    $name = $platform['name'] ?? '';
    $discipline = $platform['discipline'] ?? '';
    $region = $platform['region'] ?? '';
    $toneKey = array_key_exists($slug, $platformTones) ? $slug : 'default';
@endphp

@if ($showcase)
    <a href="{{ $platform['href'] ?? '#' }}"
       {{ $attributes->class([
           'academy-showcase group flex h-full min-w-0 flex-col',
           'academy-showcase--'.$toneKey,
       ]) }}>
        <span class="academy-showcase-glow" aria-hidden="true"></span>
        <span class="academy-showcase-floor" aria-hidden="true"></span>

        <span class="academy-showcase-card flex h-full min-w-0 flex-col">
            <span class="academy-showcase-media">
                @if ($cover)
                    <img src="{{ asset($cover) }}"
                         alt="{{ $name }}"
                         width="1400" height="875"
                         loading="lazy"
                         decoding="async"
                         class="academy-showcase-photo">
                @else
                    <span class="flex h-full items-center justify-center bg-accent-50">
                        <x-icon :name="$glyph" class="h-8 w-8 text-accent-400" />
                    </span>
                @endif
                <span class="academy-showcase-media-sheen" aria-hidden="true"></span>
            </span>

            <span class="academy-showcase-body flex grow flex-col">
                @if ($name !== '')
                    <span class="flex flex-wrap items-center gap-2">
                        <span @class([
                            'inline-flex items-center rounded-md border px-2.5 py-1 text-micro font-medium',
                            $platformTone,
                        ])>
                            {{ $name }}
                        </span>
                    </span>
                @endif

                <span class="mt-2 font-display text-panel font-semibold leading-snug text-basalt-900 group-hover:text-accent-700">
                    {{ $discipline !== '' ? $discipline : $name }}
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
        </span>
    </a>
@else
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
        </span>

        <span class="flex grow flex-col p-5">
            @if ($name !== '')
                <span class="flex flex-wrap items-center gap-2">
                    <span @class([
                        'inline-flex items-center rounded-md border px-2.5 py-1 text-micro font-medium',
                        $platformTone,
                    ])>
                        {{ $name }}
                    </span>
                </span>
            @endif

            <span class="mt-2 font-display text-panel font-semibold leading-snug text-basalt-900 group-hover:text-accent-700">
                {{ $discipline !== '' ? $discipline : $name }}
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
@endif
