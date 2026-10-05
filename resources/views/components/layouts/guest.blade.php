@props(['title' => null, 'cover' => null, 'wide' => false])

@php
    // Named from the same fixture as the rest of the app, so this line cannot
    // fall out of date with the platforms that actually exist.
    $platforms = collect(\App\Support\DemoData\Platforms::active())->pluck('name');
    $covered = filled($cover);
    $contentClass = $covered ? 'w-full max-w-none' : ($wide ? 'max-w-5xl' : 'max-w-md');
@endphp

<x-layouts.shell
    experience="guest"
    :title="$title"
    :content-class="$contentClass"
    :main-class="$covered ? 'flex min-h-0 grow flex-col p-0' : 'min-w-0 grow px-4 py-6 sm:px-6 lg:px-8 lg:py-10'">

    @unless ($covered)
        <x-slot:masthead>
            <div class="bg-basalt-950">
                <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">
                    <x-layouts.partials.brand :href="route('home')" on-basalt />
                    <x-button size="sm" :href="route('register')">Get started</x-button>
                </div>
            </div>
        </x-slot:masthead>
    @endunless

    @if ($covered)
        {{ $slot }}

        <p class="sr-only">
            {{ config('app.name') }} serves {{ $platforms->join(', ', ' and ') }}.
            Your training record follows you across every academy you work on.
        </p>
    @else
        <div class="py-10 sm:py-14">
            {{ $slot }}

            <p class="mt-10 text-micro leading-relaxed text-fern-500">
                {{ config('app.name') }} serves {{ $platforms->join(', ', ' and ') }}.
                Your training record follows you across every academy you work on.
            </p>
        </div>
    @endif

    @if ($covered)
        <x-slot:footer></x-slot:footer>
    @endif
</x-layouts.shell>
