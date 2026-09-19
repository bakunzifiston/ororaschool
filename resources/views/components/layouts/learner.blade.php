@props(['title' => null, 'width' => 'wide'])

@php
    $shell = \App\Support\DemoData\DemoData::shell('learner');

    // Learner pages are centred and narrower than the staff experiences: nothing
    // here is a control surface, it is material to read and work through.
    $widths = ['wide' => 'max-w-5xl', 'read' => 'max-w-2xl'];
@endphp

<x-layouts.shell experience="learner" :title="$title"
                 :content-class="$widths[$width] ?? $widths['wide']"
                 :user="$shell['user']">

    <x-slot:topbarStart>
        <x-layouts.partials.brand :href="route('learner.dashboard')" class="shrink-0" />

        {{-- Seven destinations: a horizontal bar from md up, a disclosure below. --}}
        <nav class="hidden min-w-0 md:flex md:items-center md:gap-0.5" aria-label="Primary navigation">
            @foreach ($shell['navigation'] as $item)
                @php $active = Route::has($item['route']) && request()->routeIs($item['route'], $item['route'].'.*'); @endphp

                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                   @class([
                       'rounded-sm px-2.5 py-1.5 text-dense transition-colors',
                       'bg-accent-50 font-medium text-accent-700' => $active,
                       'text-fern-500 hover:bg-clay-100 hover:text-basalt-900' => ! $active,
                   ])
                   @if ($active) aria-current="page" @endif>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <button type="button" x-on:click="nav = ! nav"
                :aria-expanded="nav ? 'true' : 'false'"
                class="ml-auto inline-flex h-9 items-center gap-1.5 rounded-md border border-clay-200 bg-chalk px-2.5 text-dense text-fern-500 md:hidden">
            <x-icon name="menu" class="h-4 w-4" />
            Menu
        </button>
    </x-slot:topbarStart>

    <x-slot:topbarEnd>
        <span class="hidden md:block">
            <x-layouts.partials.user-menu :user="$shell['user']" compact />
        </span>
    </x-slot:topbarEnd>

    <x-slot:mobilePanel>
        <div x-show="nav" x-cloak class="border-b border-clay-200 bg-chalk px-4 py-3 md:hidden">
            <nav aria-label="Primary navigation">
                <ul class="grid gap-0.5">
                    @foreach ($shell['navigation'] as $item)
                        @php $active = Route::has($item['route']) && request()->routeIs($item['route'], $item['route'].'.*'); @endphp
                        <li>
                            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                               @class([
                                   'flex items-center gap-2.5 rounded-sm px-2 py-2 text-body',
                                   'bg-accent-50 font-medium text-accent-700' => $active,
                                   'text-basalt-800 hover:bg-papyrus' => ! $active,
                               ])
                               @if ($active) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" class="h-4 w-4 text-fern-400" />
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="mt-3 flex items-center justify-between gap-3 border-t border-clay-100 pt-3">
                <span class="flex min-w-0 items-center gap-2">
                    <x-avatar :name="$shell['user']['name']" size="sm" />
                    <span class="min-w-0">
                        <span class="block truncate text-dense text-basalt-800">{{ $shell['user']['name'] }}</span>
                        <span class="block truncate text-micro text-fern-400">{{ $shell['user']['title'] }}</span>
                    </span>
                </span>

                <form method="POST" action="{{ route('sign-out') }}" class="shrink-0">
                    @csrf
                    <x-button variant="secondary" size="sm" type="submit" icon="log-out">Sign out</x-button>
                </form>
            </div>
        </div>
    </x-slot:mobilePanel>

    {{ $slot }}
</x-layouts.shell>
