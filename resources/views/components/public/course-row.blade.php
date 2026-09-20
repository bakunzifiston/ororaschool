@props([
    'course' => [],
    'detail' => false,
])

@php
    $href = $course['href'] ?? null;
    $price = ($course['paid'] ?? false) ? 'Paid' : 'Free';
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'group block border-t border-clay-200 py-4 first:border-t-0']) }}>
    <span class="block font-display text-panel font-semibold leading-snug text-basalt-900 group-hover:text-accent-700">
        {{ $course['title'] ?? '' }}
    </span>
    <span class="mt-2 flex flex-wrap items-baseline gap-x-6 gap-y-1 text-dense text-fern-500">
        <span>{{ $course['platform_name'] ?? '' }}</span>
        <x-public.duration :minutes="$course['duration'] ?? 0" />
        @if ($detail)
            <span>{{ $course['language'] ?? '' }}</span>
            <span>{{ $price }}</span>
        @endif
    </span>
</a>
