@props(['user' => [], 'role' => null, 'compact' => false, 'links' => [], 'menuId' => 'user-menu'])

@php
    $roleLabel = is_array($role) ? ($role['label'] ?? null) : $role;
@endphp

<div x-data="{ open: false }"
     x-on:keydown.escape.window="open = false"
     class="relative">
    <button type="button"
            x-on:click="open = ! open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{{ $menuId }}"
            class="flex items-center gap-2 rounded-full py-1 pr-1 pl-1 text-left transition-colors hover:bg-clay-100">
        <x-avatar :name="$user['name'] ?? ''" size="sm" />
        @unless ($compact)
            <span class="hidden whitespace-nowrap md:block">
                <span class="block truncate text-micro font-medium text-basalt-800">{{ $user['name'] ?? '' }}</span>
                @if ($roleLabel)
                    <span class="block truncate text-micro text-fern-500">{{ $roleLabel }}</span>
                @endif
            </span>
        @endunless
        <x-icon name="chevron-down" class="hidden h-3.5 w-3.5 text-fern-500 md:block" />
        <span class="sr-only">Account menu</span>
    </button>

    <div id="{{ $menuId }}"
         x-show="open"
         x-cloak
         x-on:click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute right-0 z-50 mt-1.5 w-56 origin-top-right overflow-hidden rounded-lg border border-clay-200 bg-chalk py-1 shadow-lg">
        <div class="border-b border-clay-100 px-3 py-2">
            <p class="truncate text-dense font-medium text-basalt-900">{{ $user['name'] ?? '' }}</p>
            @if (! empty($user['email']))
                <p class="truncate text-micro text-fern-500">{{ $user['email'] }}</p>
            @endif
            @if ($roleLabel)
                <p class="truncate text-micro text-fern-500">{{ $roleLabel }}</p>
            @endif
        </div>

        @foreach ($links as $link)
            @if (Route::has($link['route']))
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-2 px-3 py-2 text-dense text-basalt-800 hover:bg-papyrus">
                    @if (! empty($link['icon']))
                        <x-icon :name="$link['icon']" class="h-4 w-4 text-fern-500" />
                    @endif
                    {{ $link['label'] }}
                </a>
            @endif
        @endforeach

        <form method="POST" action="{{ route('sign-out') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-dense text-basalt-800 hover:bg-papyrus">
                <x-icon name="log-out" class="h-4 w-4 text-fern-500" />
                Sign out
            </button>
        </form>
    </div>
</div>
