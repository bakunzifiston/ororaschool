@props(['items' => [], 'params' => []])

<nav class="grow overflow-y-auto px-3 py-3" aria-label="Sections">
    <ul class="grid gap-0.5">
        @foreach ($items as $item)
            @php
                $exists = Route::has($item['route']);
                $url = $exists ? route($item['route'], $params) : null;
                $active = $exists && request()->routeIs($item['route']);
            @endphp

            <li>
                <a href="{{ $url ?? '#' }}"
                   @class([
                       'flex items-center gap-2.5 rounded-sm px-2 py-1.5 text-dense transition-colors',
                       'bg-basalt-800 text-white' => $active,
                       'text-clay-200 hover:bg-basalt-800 hover:text-white' => ! $active,
                   ])
                   @if ($active) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" class="h-4 w-4 {{ $active ? 'text-accent-400' : 'text-fern-400' }}" />
                    <span class="grow truncate">{{ $item['label'] }}</span>
                    @if (! empty($item['badge']))
                        <span class="figure rounded-sm bg-accent-500 px-1.5 text-micro text-accent-on">{{ $item['badge'] }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</nav>
