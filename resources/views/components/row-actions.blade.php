@props([
    'view' => null,
    'edit' => null,
    'delete' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-end gap-2']) }}>
    @if ($view)
        <x-button variant="ghost" size="sm" :href="$view">View</x-button>
    @endif

    @if ($edit)
        <x-button variant="ghost" size="sm" :href="$edit">Edit</x-button>
    @endif

    @if ($delete)
        <x-button variant="danger" size="sm" x-on:click="$dispatch('open-modal', '{{ $delete }}')">Delete</x-button>
    @endif
</div>
