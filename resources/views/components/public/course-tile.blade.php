@props(['course' => []])

@php
    $glyphs = [
        'ororafarm' => 'sprout',
        'gemura' => 'droplet',
        'buchapro' => 'tag',
        'feedgrid' => 'layers',
    ];
    $glyph = $glyphs[$course['platform'] ?? ''] ?? 'book';
    $lessons = (int) ($course['lessons'] ?? 0);
    $enrolled = (int) ($course['enrolled'] ?? 0);
    $cover = $course['cover'] ?? null;
    $title = $course['title'] ?? '';
    $href = $course['href'] ?? '#';
    $paid = (bool) ($course['paid'] ?? false);
    $platformName = $course['platform_name'] ?? '';
    $focus = $course['academy'] ?? '';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'public-card public-card-hover group flex h-full min-w-0 flex-col overflow-hidden']) }}>
    <span class="relative aspect-[16/10] overflow-hidden bg-accent-50">
        @if ($cover)
            <img src="{{ asset($cover) }}"
                 alt="{{ $title }}"
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
        @if ($platformName !== '' || $focus !== '')
            <span class="text-micro font-medium text-fern-500">
                {{ collect([$platformName, $focus])->filter()->implode(' · ') }}
            </span>
        @endif

        <span class="mt-1.5 font-display text-panel font-semibold leading-snug text-basalt-900 group-hover:text-accent-700">
            {{ $title }}
        </span>

        @if (($course['summary'] ?? '') !== '')
            <span class="mt-2 line-clamp-2 text-dense leading-relaxed text-fern-600">{{ $course['summary'] }}</span>
        @endif

        <span class="mt-auto flex flex-col gap-3 pt-4">
            <span class="flex flex-wrap items-center gap-x-2 gap-y-1 text-micro text-fern-500">
                <span class="inline-flex items-center gap-1">
                    <x-icon name="clock" class="h-3.5 w-3.5" />
                    <x-public.duration :minutes="$course['duration'] ?? 0" />
                </span>
                @if ($lessons > 0)
                    <span class="text-clay-300" aria-hidden="true">·</span>
                    <span>{{ $lessons }} {{ $lessons === 1 ? 'lesson' : 'lessons' }}</span>
                @endif
                <span class="text-clay-300" aria-hidden="true">·</span>
                <span>{{ $paid ? 'Paid' : 'Free' }}</span>
                @if ($enrolled > 0)
                    <span class="text-clay-300" aria-hidden="true">·</span>
                    <span>{{ number_format($enrolled) }} enrolled</span>
                @endif
            </span>

            <span class="inline-flex items-center gap-1 text-dense font-medium text-accent-700">
                View course
                <x-icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5" />
            </span>
        </span>
    </span>
</a>
