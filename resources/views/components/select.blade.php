@props([
    'name',
    'label' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'size' => 'lg',
])

@php
    $id = 'field-' . $name;

    // Accepts either a flat list of labels or a value => label map.
    $normalised = array_is_list($options)
        ? array_combine($options, $options)
        : $options;
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <label for="{{ $id }}" class="mb-1.5 block text-dense font-medium text-basalt-800">{{ $label }}</label>

    <select id="{{ $id }}" name="{{ $name }}"
            @if ($required) required @endif
            @if ($hint) aria-describedby="{{ $id }}-hint" @endif
            class="{{ $size === 'sm' ? 'h-9 text-dense' : 'h-11 text-body' }} w-full rounded-md border bg-chalk px-2.5 text-basalt-800 {{ $error ? 'border-danger' : 'border-clay-300' }}">
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($normalised as $value => $optionLabel)
            <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-micro leading-relaxed text-fern-500">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="mt-1.5 flex items-start gap-1.5 text-micro text-danger">
            <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $error }}
        </p>
    @endif
</div>
