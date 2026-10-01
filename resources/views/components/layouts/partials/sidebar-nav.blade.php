@props(['items' => [], 'params' => []])

@php
    $groups = [];

    foreach ($items as $item) {
        $groups[$item['group'] ?? ''][] = $item;
    }

    $grouped = count($groups) > 1 || (count($groups) === 1 && array_key_first($groups) !== '');
@endphp

<nav class="mt-3 grow overflow-y-auto px-3 py-4" aria-label="Sections">
    <ul class="grid gap-4">
        @foreach ($groups as $group => $groupItems)
            <li>
                @if ($grouped && $group !== '')
                    <p class="px-2.5 pb-1.5 text-micro font-medium uppercase tracking-wider text-fern-400">{{ $group }}</p>
                @endif

                <ul class="grid gap-1">
                    @foreach ($groupItems as $item)
                        @php
                            $exists = Route::has($item['route']);
                            $url = $exists ? route($item['route'], $params) : null;
                            $active = $exists && request()->routeIs($item['route'], $item['route'].'.*');
                        @endphp

                        <li>
                            <a href="{{ $url ?? '#' }}"
                               @class([
                                   'flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-dense transition-colors',
                                   'bg-basalt-800 text-white' => $active,
                                   'text-clay-200 hover:bg-basalt-800 hover:text-white' => ! $active,
                               ])
                               @if ($active) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" class="h-4 w-4 {{ $active ? 'text-accent-400' : 'text-fern-400' }}" />
                                <span class="grow truncate">{{ $item['label'] }}</span>
                                @if (! empty($item['badge']))
                                    <span class="figure rounded-full bg-accent-500 px-1.5 text-micro text-accent-on">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</nav>
