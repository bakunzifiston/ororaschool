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
    'autosubmit' => false,
    'model' => null,
    'appearance' => 'box',
    'disableWhen' => null,
    'alpineOptions' => null,
])

@php
    $error = $error ?? $errors->first($name);
    $selected = old($name, $selected);
    $id = 'field-' . $name;
    $pill = $appearance === 'pill';

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
            @if ($autosubmit) onchange="this.form.submit()" @endif
            @if ($model) x-model="{{ $model }}" @endif
            @if ($disableWhen) x-bind:disabled="{{ $disableWhen }}" @endif
            class="{{ $size === 'sm' ? 'h-9 text-dense' : 'h-11 text-body' }} w-full border text-basalt-800 {{ $pill ? 'rounded-full bg-papyrus px-4' : 'rounded-md bg-chalk px-2.5' }} {{ $error ? 'border-danger' : ($pill ? 'border-clay-200' : 'border-clay-300') }}">
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if ($alpineOptions)
            <template x-for="option in {{ $alpineOptions }}" :key="option">
                <option :value="option" x-text="option"></option>
            </template>
        @else
            @foreach ($normalised as $value => $optionLabel)
                <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $optionLabel }}</option>
            @endforeach
        @endif
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
