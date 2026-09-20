@props(['role' => 'learner', 'showScope' => false, 'removable' => false, 'onBasalt' => false])

@php
    $meta = \App\Support\DemoData\Roles::find($role);
    $elevated = $meta['elevated'];

    $tone = match (true) {
        $elevated && $onBasalt => 'border-basalt-600 bg-basalt-700 text-clay-100',
        $elevated => 'border-basalt-700 bg-basalt-800 text-clay-100',
        $onBasalt => 'border-basalt-700 bg-basalt-800 text-clay-200',
        default => 'border-clay-200 bg-clay-100 text-basalt-800',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-sm border px-2 py-0.5 text-micro ' . $tone]) }}>
    @if ($elevated)
        <x-icon name="shield" class="h-3 w-3" />
    @endif

    <span class="font-medium">{{ $meta['label'] }}</span>

    @if ($showScope)
        <span class="{{ $elevated ? 'text-fern-400' : 'text-fern-500' }}">· {{ $meta['scope'] }}</span>
    @endif

    @if ($removable)
        <button type="button"
                class="-mr-0.5 ml-0.5 rounded-xs {{ $elevated ? 'text-fern-400 hover:text-white' : 'text-fern-500 hover:text-basalt-900' }}"
                aria-label="Remove the {{ $meta['label'] }} role">
            <x-icon name="x" class="h-3 w-3" />
        </button>
    @endif
</span>
