@props(['platform' => []])

<li {{ $attributes->merge(['class' => 'border-t border-clay-200 first:border-t-0']) }}>
    <a href="{{ $platform['href'] ?? '#' }}"
       class="grid gap-1 py-4 sm:grid-cols-[9rem_11rem_minmax(0,1fr)] sm:items-baseline sm:gap-6">
        <span class="font-medium text-basalt-900">{{ $platform['name'] ?? '' }}</span>
        <span class="text-dense text-basalt-800">{{ $platform['discipline'] ?? '' }}</span>
        <span class="text-dense leading-relaxed text-fern-500">{{ $platform['tagline'] ?? $platform['description'] ?? '' }}</span>
    </a>
</li>
