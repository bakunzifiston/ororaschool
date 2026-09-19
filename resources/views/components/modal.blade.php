@props([
    'name' => 'modal',
    'title' => null,
    'subtitle' => null,
    'width' => 'lg',
])

@php
    $widths = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-2xl'];
@endphp

{{--
    Reusable dialog shell for any future confirm/create/edit flow.

    Open it from anywhere on the page:
        <x-button x-on:click="$dispatch('open-modal', 'delete-course')">…</x-button>
    Close from inside via `open = false`, from outside via
        $dispatch('close-modal')
    or with the Escape key.

    Motion is confined to overlays like this one: a short scale-and-fade explains
    where the panel came from. Persistent content never animates in.
--}}
<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') { open = true; $nextTick(() => $refs.panel.focus()) }"
     x-on:close-modal.window="open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-6">

    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-on:click="open = false"
         class="absolute inset-0 bg-basalt-950/50"
         aria-hidden="true"></div>

    <div x-ref="panel"
         x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         tabindex="-1"
         role="dialog"
         aria-modal="true"
         @if ($title) aria-label="{{ $title }}" @endif
         {{-- The panel takes focus so assistive tech lands inside the dialog, but
              the visible ring belongs on its controls, not the container. --}}
         class="relative w-full {{ $widths[$width] ?? $widths['lg'] }} rounded-t-md border border-clay-300 bg-chalk focus-visible:outline-none sm:rounded-md">

        @if ($title)
            <header class="flex items-start justify-between gap-4 border-b border-clay-200 px-4 py-3">
                <div class="min-w-0">
                    <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $title }}</h2>
                    @if ($subtitle)
                        <p class="mt-0.5 text-micro text-fern-500">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" x-on:click="open = false"
                        class="-mr-1 rounded-sm p-1 text-fern-500 hover:bg-clay-100 hover:text-basalt-900"
                        aria-label="Close dialog">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </header>
        @endif

        <div class="px-4 py-4 text-dense leading-relaxed text-fern-600">
            {{ $slot }}
        </div>

        @isset($actions)
            <footer class="flex flex-wrap items-center justify-end gap-2 border-t border-clay-200 px-4 py-3">
                {{ $actions }}
            </footer>
        @endisset
    </div>
</div>
