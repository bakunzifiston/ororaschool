@props(['current' => [], 'platforms' => [], 'id' => 'workspace-switcher'])

{{--
    Workspace switcher. Choosing a platform is plain navigation to that
    platform's current section (dashboard, courses, certificates, …) — anchors,
    so it works without JavaScript and is keyboard-operable for free. Nested
    screens (course edit, quiz builder) drop back to the section index so the
    other platform is not asked for a record that does not exist there. There
    is no access check here: the list comes straight from the fixture user's
    `platforms`.

    Modelled as a disclosure, not an ARIA menu: these are links, and role="menu"
    would promise arrow-key semantics this does not implement.
--}}
<div x-data="{ open: false }"
     x-on:keydown.escape.window="open = false"
     class="relative">
    <button type="button"
            x-on:click="open = ! open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="{{ $id }}"
            class="flex h-9 items-center gap-2 rounded-md border border-clay-200 bg-chalk pl-1.5 pr-2 text-left transition-colors hover:border-fern-400">
        <span class="sr-only">Change platform. Currently </span>
        <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-sm bg-accent-500 text-micro font-semibold text-accent-on" aria-hidden="true">
            {{ mb_substr($current['name'] ?? '?', 0, 1) }}
        </span>

        <span class="min-w-0">
            <span class="block truncate text-dense font-medium leading-tight text-basalt-900">{{ $current['name'] ?? '' }}</span>
            <span class="hidden truncate text-micro leading-tight text-fern-500 sm:block">{{ $current['discipline'] ?? '' }}</span>
        </span>

        <x-icon name="chevron-down" class="h-3.5 w-3.5 text-fern-500 transition-transform"
                ::class="open && 'rotate-180'" />
    </button>

    <div id="{{ $id }}"
         x-show="open"
         x-cloak
         x-on:click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.97]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute left-0 z-50 mt-1 w-72 origin-top overflow-hidden rounded-md border border-clay-200 bg-chalk">

        <p class="border-b border-clay-100 px-3 py-2 text-micro text-fern-500">
            Platforms you work on
        </p>

        <ul>
            @php
                $section = \App\Support\DemoData\Navigation::workspaceSectionRoute(request()->route()?->getName());
            @endphp
            @foreach ($platforms as $platform)
                @php $isCurrent = ($platform['slug'] ?? null) === ($current['slug'] ?? null); @endphp

                <li>
                    <a href="{{ route($section, ['platform' => $platform['slug']]) }}"
                       @if ($isCurrent) aria-current="true" @endif
                       @class([
                           'flex items-start gap-2.5 px-3 py-2.5 transition-colors',
                           'bg-accent-50' => $isCurrent,
                           'hover:bg-papyrus' => ! $isCurrent,
                       ])>
                        <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-sm border {{ $isCurrent ? 'border-accent-500 bg-accent-500 text-accent-on' : 'border-clay-200 bg-papyrus text-fern-500' }} text-micro font-semibold"
                              aria-hidden="true">
                            {{ mb_substr($platform['name'], 0, 1) }}
                        </span>

                        <span class="min-w-0 grow">
                            <span class="block truncate text-dense font-medium text-basalt-900">{{ $platform['name'] }}</span>
                            <span class="figure block truncate text-micro text-fern-500">
                                {{ number_format($platform['learners']) }} learners · {{ $platform['courses'] }} courses
                            </span>
                        </span>

                        @if ($isCurrent)
                            <x-icon name="check" class="mt-1 h-3.5 w-3.5 shrink-0 text-accent-500" />
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
