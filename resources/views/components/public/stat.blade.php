@props(['value', 'label', 'icon' => null])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center gap-3 px-6 py-2 sm:px-8']) }}>
    @if (filled($icon))
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-accent-50 text-accent-600" aria-hidden="true">
            <x-icon :name="$icon" class="h-4 w-4" />
        </span>
    @endif
    <div class="min-w-0 text-left">
        <dt class="sr-only">{{ $label }}</dt>
        <dd class="figure text-title font-semibold tracking-tight text-accent-600">{{ $value }}</dd>
        <p class="mt-0.5 text-dense text-fern-500">{{ $label }}</p>
    </div>
</div>
