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
@endphp

<article {{ $attributes->merge(['class' => 'public-card public-card-hover flex min-w-0 flex-col overflow-hidden']) }}>
    <div class="relative flex h-28 items-center justify-center bg-accent-50">
        <x-icon :name="$glyph" class="h-8 w-8 text-accent-400" />
        <span class="absolute left-3 top-3 rounded-sm bg-chalk px-2 py-0.5 text-micro font-medium text-basalt-800">
            {{ $course['platform_name'] ?? '' }}
        </span>
    </div>

    <div class="flex grow flex-col p-4">
        <h3 class="font-display text-panel font-semibold leading-snug text-basalt-900">
            <a href="{{ $course['href'] ?? '#' }}" class="rounded-xs hover:text-accent-700">{{ $course['title'] ?? '' }}</a>
        </h3>

        @if (($course['summary'] ?? '') !== '')
            <p class="mt-2 line-clamp-2 text-dense leading-relaxed text-fern-600">{{ $course['summary'] }}</p>
        @endif

        <p class="mt-3 text-micro text-fern-500">
            {{ $course['platform_name'] ?? '' }}
            <span class="text-clay-300">•</span>
            <x-public.duration :minutes="$course['duration'] ?? 0" />
            @if ($lessons > 0)
                <span class="text-clay-300">•</span>
                {{ $lessons }} {{ $lessons === 1 ? 'lesson' : 'lessons' }}
            @endif
            <span class="text-clay-300">•</span>
            {{ ($course['paid'] ?? false) ? 'Paid' : 'Free' }}
        </p>

        @if ($enrolled > 0)
            <p class="mt-1 text-micro text-fern-500">{{ number_format($enrolled) }} enrolled</p>
        @endif

        <p class="mt-4">
            <a href="{{ $course['href'] ?? '#' }}" class="text-dense font-medium text-accent-700 hover:underline">View course</a>
        </p>
    </div>
</article>
