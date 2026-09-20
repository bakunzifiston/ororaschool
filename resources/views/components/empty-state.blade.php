@props([
    'icon' => 'inbox',
    'title' => '',
    'message' => null,
])

{{-- Empty states tell the reader what to do next. The copy is supplied by the
     page so it can name the actual cohort, platform or queue. --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <span class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-md border border-clay-200 bg-papyrus text-fern-500">
        @isset($illustration)
            {{ $illustration }}
        @else
            <x-icon :name="$icon" class="h-5 w-5" />
        @endisset
    </span>

    <h3 class="font-display text-panel font-semibold text-basalt-900">{{ $title }}</h3>

    @if ($message)
        <p class="mt-1.5 max-w-md text-dense leading-relaxed text-fern-500">{{ $message }}</p>
    @endif

    @isset($actions)
        <div class="mt-5 flex flex-wrap items-center justify-center gap-2">{{ $actions }}</div>
    @endisset
</div>
