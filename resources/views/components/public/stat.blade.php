@props(['value', 'label'])

<div {{ $attributes->merge(['class' => 'px-6 py-7 text-center sm:py-8']) }}>
    <dt class="sr-only">{{ $label }}</dt>
    <dd class="figure text-title font-semibold tracking-tight text-accent-600">{{ $value }}</dd>
    <p class="mt-1.5 text-dense text-fern-500">{{ $label }}</p>
</div>
