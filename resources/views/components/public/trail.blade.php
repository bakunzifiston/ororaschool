@props(['items' => []])

@if (count($items))
    <nav {{ $attributes->merge(['class' => 'mb-3']) }} aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 text-micro text-fern-500">
            @foreach ($items as $crumb)
                <li class="flex items-center gap-1.5">
                    @if (! $loop->first)
                        <x-icon name="chevron-right" class="h-3 w-3 text-fern-500" />
                    @endif

                    @php
                        $url = $crumb['url'] ?? (
                            ! empty($crumb['route']) && Route::has($crumb['route'])
                                ? route($crumb['route'], $crumb['params'] ?? [])
                                : null
                        );
                    @endphp

                    @if ($url && ! $loop->last)
                        <a href="{{ $url }}" class="rounded-xs underline-offset-2 hover:text-basalt-800 hover:underline">
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span @class(['text-basalt-700' => $loop->last])>{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
