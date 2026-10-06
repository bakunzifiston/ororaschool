@props([
    'course' => [],
    'platformLabel' => null,
    'progress' => null,
    'status' => null,
    'href' => null,
    'variant' => null,
    'view' => null,
    'edit' => null,
    'action' => null,
    'actionHref' => null,
    'actionIcon' => null,
    'size' => 'md',
])

@php
    $duration = (int) ($course['duration'] ?? 0);
    $hours = intdiv($duration, 60);
    $minutes = $duration % 60;
    $length = $hours ? trim("{$hours}h " . ($minutes ? "{$minutes}m" : '')) : "{$minutes}m";

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
    $glyph = $glyphs[$course['platform'] ?? ''] ?? 'book';
    $platformTone = $platformTones[$course['platform'] ?? ''] ?? 'border-accent-200 bg-accent-50 text-accent-700';
    $cover = $course['cover'] ?? null;
    $instructor = $course['instructor'] ?? '';

    $public = $variant === 'public';
    $compact = $size === 'sm';
    $showProgress = ! $public && ! is_null($progress);
    $badge = $status ?? ($course['status'] ?? 'draft');
    $link = $href ?? ($course['href'] ?? null);
    $buttonHref = $actionHref ?? $link;
@endphp

<article {{ $attributes->merge(['class' => 'app-card group flex min-w-0 flex-col overflow-hidden rounded-md border border-clay-200 bg-chalk transition-colors hover:border-clay-300 focus-within:border-accent-500']) }}>

    <div @class([
        'relative overflow-hidden bg-accent-50',
        'aspect-[16/9]' => $compact,
        'aspect-[16/10]' => ! $compact,
    ])>
        @if ($cover)
            <img src="{{ asset($cover) }}"
                 alt=""
                 width="640"
                 height="{{ $compact ? 360 : 400 }}"
                 loading="lazy"
                 decoding="async"
                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
        @else
            <span class="flex h-full items-center justify-center">
                <x-icon :name="$glyph" @class(['text-accent-400', 'h-7 w-7' => $compact, 'h-8 w-8' => ! $compact]) />
            </span>
        @endif
        <span @class(['absolute', 'right-2 top-2' => $compact, 'right-2.5 top-2.5' => ! $compact])>
            @if ($public)
                <x-status-badge :status="($course['paid'] ?? false) ? 'paid' : 'free'" />
            @else
                <x-status-badge :status="$badge" />
            @endif
        </span>
    </div>

    <div @class([
        'flex grow flex-col',
        'gap-1.5 p-3.5' => $compact,
        'gap-2 p-4' => ! $compact,
    ])>
        @if ($platformLabel)
            @if ($public && filled($course['platform'] ?? null))
                <div>
                    <a href="{{ route('catalog.platforms.show', ['platform' => $course['platform']]) }}"
                       @class([
                           'inline-flex items-center rounded-md border px-2.5 py-1 text-micro font-medium transition-opacity hover:opacity-80',
                           $platformTone,
                       ])>
                        {{ $platformLabel }}
                    </a>
                </div>
            @else
                <p @class([
                    'inline-flex w-fit items-center rounded-md border px-2.5 py-1 text-micro font-medium',
                    $platformTone,
                ])>
                    {{ $platformLabel }}
                </p>
            @endif
        @endif

        <h3 @class([
            'font-display font-semibold leading-snug text-basalt-900',
            'text-dense' => $compact,
            'text-panel' => ! $compact,
        ])>
            @if ($link)
                <a href="{{ $link }}" class="rounded-xs hover:text-accent-600">{{ $course['title'] ?? '' }}</a>
            @else
                {{ $course['title'] ?? '' }}
            @endif
        </h3>

        <p @class([
            'line-clamp-2 leading-relaxed text-fern-500',
            'text-micro' => $compact,
            'text-dense' => ! $compact,
        ])>{{ $course['summary'] ?? '' }}</p>

        @if ($instructor !== '' && ! $public)
            <p class="flex min-w-0 items-center gap-2 text-micro text-fern-500">
                <x-avatar :name="$instructor" size="sm" />
                <span class="truncate">{{ $instructor }}</span>
            </p>
        @endif

        <div @class(['mt-auto', 'pt-1.5' => $compact, 'pt-2' => ! $compact])>
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
                <span class="figure flex items-center gap-1 text-micro text-fern-500">
                    <x-icon name="clock" class="h-3 w-3" />{{ $length }}
                </span>
            @endif
        </div>
    </div>

    @if ($action && $buttonHref)
        <div @class([
            'border-t border-clay-100',
            'px-3.5 py-2.5' => $compact,
            'px-4 py-3' => ! $compact,
        ])>
            <x-button size="sm" :icon="$actionIcon" :href="$buttonHref">{{ $action }}</x-button>
        </div>
    @elseif ($view || $edit)
        <div class="border-t border-clay-100 px-4 py-2.5">
            <x-row-actions :view="$view" :edit="$edit" class="justify-start" />
        </div>
    @endif
</article>
