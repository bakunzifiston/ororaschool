@props(['title' => null])

@php
    // Named from the same fixture as the rest of the app, so this line cannot
    // fall out of date with the platforms that actually exist.
    $platforms = collect(\App\Support\DemoData\Platforms::active())->pluck('name');
@endphp

<x-layouts.shell experience="guest" :title="$title" content-class="max-w-md">

    {{-- Basalt band rather than a brand panel filling half the screen: on a
         low-end phone, chrome that pushes the form below the fold costs more
         than it communicates. --}}
    <x-slot:masthead>
        <div class="on-basalt bg-basalt-900">
            <div class="mx-auto flex h-14 max-w-6xl items-center justify-between gap-3 px-4 sm:px-6">
                <x-layouts.partials.brand :href="route('login')" on-basalt />
                <p class="text-micro text-fern-400">Training and certification</p>
            </div>
        </div>
    </x-slot:masthead>

    <div class="py-4 sm:py-8">
        {{ $slot }}

        <p class="mt-8 border-t border-clay-200 pt-4 text-micro leading-relaxed text-fern-400">
            Orora School serves {{ $platforms->join(', ', ' and ') }}.
            Your training record follows you across every platform you work on.
        </p>
    </div>
</x-layouts.shell>
