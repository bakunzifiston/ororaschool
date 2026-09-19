@props(['name', 'label' => '', 'checked' => false, 'hint' => null])

@php $id = 'check-' . $name; @endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <div class="flex items-start gap-2.5">
        <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1"
               @checked($checked)
               class="mt-0.5 h-4 w-4 shrink-0 rounded-xs border-clay-300 bg-chalk text-accent-500 accent-[var(--color-accent-500)]">

        <label for="{{ $id }}" class="text-dense leading-snug text-basalt-800">{{ $label }}</label>
    </div>

    @if ($hint)
        <p class="mt-1 pl-6.5 text-micro text-fern-500">{{ $hint }}</p>
    @endif
</div>
