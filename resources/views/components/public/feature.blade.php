@props(['icon', 'title'])

<div {{ $attributes->merge(['class' => 'flex h-full flex-col']) }}>
    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-accent-50 text-accent-600">
        <x-icon :name="$icon" class="h-5 w-5" />
    </span>
    <h3 class="mt-5 font-display text-panel font-semibold text-basalt-900">{{ $title }}</h3>
    <p class="mt-2 grow text-dense leading-relaxed text-fern-600">{{ $slot }}</p>
</div>
