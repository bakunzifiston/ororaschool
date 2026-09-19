@props(['name', 'label' => '', 'checked' => false, 'hint' => null])

@php $id = 'toggle-'.$name; @endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <div class="flex items-start gap-3">
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1"
               @checked($checked)
               class="mt-1 h-4 w-4 shrink-0 rounded-xs border-clay-300 bg-chalk accent-[var(--color-accent-500)]">
        <div>
            <label for="{{ $id }}" class="text-dense font-medium text-basalt-800">{{ $label }}</label>
            @if ($hint)
                <p class="mt-0.5 text-micro leading-relaxed text-fern-500">{{ $hint }}</p>
            @endif
        </div>
    </div>
</div>
