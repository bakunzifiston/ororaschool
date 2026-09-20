@props([
    'course' => [],
    'platformLabel' => null,
    'progress' => null,
    'status' => null,
    'href' => null,
    'variant' => null,
])

@php
    $duration = (int) ($course['duration'] ?? 0);
    $hours = intdiv($duration, 60);
    $minutes = $duration % 60;
    $length = $hours ? trim("{$hours}h " . ($minutes ? "{$minutes}m" : '')) : "{$minutes}m";

    // Thumbnails are generated, not stock imagery: a flat accent-tinted field
    // with the platform's discipline glyph. Honest placeholder, nothing broken.
    $glyphs = [
        'ororafarm' => 'sprout',
        'gemura' => 'droplet',
        'buchapro' => 'tag',
        'feedgrid' => 'layers',
    ];
    $glyph = $glyphs[$course['platform'] ?? ''] ?? 'book';

    // Context decides the footer: a learner sees progress, a visitor sees
    // difficulty and price, staff see the instructor.
    $public = $variant === 'public';
    $showProgress = ! $public && ! is_null($progress);
    $badge = $status ?? ($course['status'] ?? 'draft');
    $link = $href ?? ($course['href'] ?? null);
@endphp

<article {{ $attributes->merge(['class' => 'group flex min-w-0 flex-col rounded-md border border-clay-200 bg-chalk transition-colors hover:border-clay-300 focus-within:border-accent-500']) }}>

    <div class="relative flex h-24 items-center justify-center rounded-t-md border-b border-clay-200 bg-accent-50">
        <x-icon :name="$glyph" class="h-7 w-7 text-accent-400" />
        <span class="absolute right-2 top-2">
            @if ($public)
                <x-status-badge :status="($course['paid'] ?? false) ? 'paid' : 'free'" />
            @else
                <x-status-badge :status="$badge" />
            @endif
        </span>
    </div>

    <div class="flex grow flex-col gap-2 p-4">
        @if ($platformLabel)
            <p class="truncate text-micro text-fern-500">{{ $platformLabel }}</p>
        @endif

        <h3 class="font-display text-panel font-semibold leading-snug text-basalt-900">
            @if ($link)
                <a href="{{ $link }}" class="rounded-xs hover:text-accent-600">{{ $course['title'] ?? '' }}</a>
            @else
                {{ $course['title'] ?? '' }}
            @endif
        </h3>

        <p class="line-clamp-2 text-dense leading-relaxed text-fern-500">{{ $course['summary'] ?? '' }}</p>

        <div class="mt-auto pt-2">
            @if ($showProgress)
                <x-progress-bar :value="$progress" size="sm"
                                :meta="($course['lessons'] ?? 0) . ' lessons · ' . $length" />
            @elseif ($public)
                <p class="text-micro text-fern-500">
                    {{ $course['difficulty'] ?? '' }}
                    <span class="text-clay-300">·</span>
                    {{ $length }}
                    <span class="text-clay-300">·</span>
                    {{ $course['language'] ?? 'English' }}
                </p>
            @else
                <div class="flex items-center justify-between gap-3">
                    <span class="flex min-w-0 items-center gap-2">
                        <x-avatar :name="$course['instructor'] ?? ''" size="sm" />
                        <span class="truncate text-micro text-fern-500">{{ $course['instructor'] ?? '' }}</span>
                    </span>
                    <span class="figure flex shrink-0 items-center gap-1 text-micro text-fern-500">
                        <x-icon name="clock" class="h-3 w-3" />{{ $length }}
                    </span>
                </div>
            @endif
        </div>
    </div>
</article>
