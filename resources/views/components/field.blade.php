@props([
    'name',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'hint' => null,
    'error' => null,
    'placeholder' => null,
    'autocomplete' => null,
    'revealable' => false,
    'readonly' => false,
    'required' => false,
    'autofocus' => false,
    'size' => 'lg',
])

@php
    $id = 'field-' . $name;
    $describedBy = array_filter([$hint ? $id . '-hint' : null, $error ? $id . '-error' : null]);

    // Inputs are 44px tall, not the 36px used inside the dense app shell: these
    // forms are filled in on phones, often outdoors.
    $height = $size === 'sm' ? 'h-9 text-dense' : 'h-11 text-body';
    $input = implode(' ', [
        $height.' w-full rounded-md border bg-chalk px-3 text-basalt-800',
        'placeholder:text-clay-300 read-only:bg-papyrus read-only:text-fern-500',
        $error ? 'border-danger' : 'border-clay-300',
        $revealable ? 'pr-11' : '',
    ]);
@endphp

<div {{ $attributes->merge(['class' => 'min-w-0']) }}
     @if ($revealable) x-data="{ show: false }" @endif>

    <label for="{{ $id }}" class="mb-1.5 flex items-center justify-between gap-3">
        <span class="text-dense font-medium text-basalt-800">{{ $label }}</span>
        @isset($labelEnd)
            <span class="shrink-0 text-micro">{{ $labelEnd }}</span>
        @endisset
    </label>

    <div class="relative">
        <input id="{{ $id }}"
               name="{{ $name }}"
               @if ($revealable) :type="show ? 'text' : '{{ $type }}'" @else type="{{ $type }}" @endif
               value="{{ $value }}"
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif
               @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               @if ($readonly) readonly @endif
               @if ($required) required @endif
               @if ($autofocus) autofocus @endif
               @if (count($describedBy)) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
               @if ($error) aria-invalid="true" @endif
               class="{{ $input }}">

        @if ($revealable)
            {{-- Passwords get typed wrong on phone keyboards; let people look. --}}
            <button type="button"
                    x-on:click="show = ! show"
                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-md text-fern-500 hover:text-basalt-900"
                    :aria-label="show ? 'Hide password' : 'Show password'"
                    :aria-pressed="show ? 'true' : 'false'">
                <x-icon name="eye" class="h-4 w-4" x-show="! show" />
                <x-icon name="eye-off" class="h-4 w-4" x-show="show" x-cloak />
            </button>
        @endif
    </div>

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-micro leading-relaxed text-fern-500">{{ $hint }}</p>
    @endif

    @if ($error)
        <p id="{{ $id }}-error" class="mt-1.5 flex items-start gap-1.5 text-micro text-danger">
            <x-icon name="alert" class="mt-0.5 h-3 w-3" />{{ $error }}
        </p>
    @endif
</div>
