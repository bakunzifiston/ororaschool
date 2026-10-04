@props([
    'breadcrumb' => [],
    'title' => '',
    'subtitle' => null,
    'meta' => null,
    'size' => 'default',
])

<header {{ $attributes->merge(['class' => 'page-header border-b border-clay-200 pb-5']) }}>
    @if (count($breadcrumb))
        {{-- Sentence case at body weight: orientation, not a tracked-out
             all-caps eyebrow. --}}
        <nav aria-label="Breadcrumb" class="mb-2">
            <ol class="flex flex-wrap items-center gap-1.5 text-micro text-fern-500">
                @foreach ($breadcrumb as $crumb)
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

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0 max-w-2xl">
            <h1 @class([
                'text-basalt-900',
                'font-display text-hero' => $size === 'display',
                'text-title' => $size !== 'display',
            ])>{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 text-dense leading-relaxed text-fern-500">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($meta || isset($actions))
            <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                @if ($meta)
                    <p class="text-micro text-fern-500">{{ $meta }}</p>
                @endif
                @isset($actions)
                    <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>
        @endif
    </div>
</header>
