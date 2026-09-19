@props([
    'name',
    'label' => '',
    'value' => '',
    'hint' => null,
    'rows' => 4,
])

@php $id = 'field-'.$name; @endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <label for="{{ $id }}" class="mb-1.5 block text-dense font-medium text-basalt-800">{{ $label }}</label>
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
              class="w-full rounded-md border border-clay-300 bg-chalk px-3 py-2 text-dense text-basalt-800 placeholder:text-clay-300">{{ $value }}</textarea>
    @if ($hint)
        <p class="mt-1.5 text-micro leading-relaxed text-fern-500">{{ $hint }}</p>
    @endif
</div>
