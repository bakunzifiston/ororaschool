@props(['title' => null])

@php
    $shell = \App\Support\DemoData\DemoData::shell('super-admin');
    $platformCount = count(\App\Support\DemoData\Platforms::all());
@endphp

<x-layouts.shell experience="super-admin" :title="$title" railed :user="$shell['user']">

    <x-slot:sidebar>
        <div class="flex h-14 shrink-0 items-center justify-between gap-2 px-4">
            <x-layouts.partials.brand :href="route('admin.dashboard')" on-basalt />
            <button type="button" x-on:click="nav = false"
                    class="rounded-sm p-1 text-fern-400 hover:text-white lg:hidden"
                    aria-label="Close navigation">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>

        {{-- Scope reminder: this experience spans every platform. --}}
        <div class="border-y border-basalt-800 px-4 py-3">
            <p class="text-micro text-fern-400">Scope</p>
            <p class="mt-0.5 text-dense text-white">All {{ $platformCount }} platforms</p>
        </div>

        <x-layouts.partials.sidebar-nav :items="$shell['navigation']" />

        <div class="shrink-0 border-t border-basalt-800 p-3">
            <div class="flex items-center gap-2.5">
                <x-avatar :name="$shell['user']['name']" size="md" on-basalt />
                <div class="min-w-0">
                    <p class="truncate text-dense text-white">{{ $shell['user']['name'] }}</p>
                    <p class="truncate text-micro text-fern-400">{{ $shell['user']['title'] }}</p>
                </div>
            </div>

            <x-layouts.partials.rail-sign-out />
        </div>
    </x-slot:sidebar>

    <x-slot:topbarEnd>
        <x-layouts.partials.user-menu :user="$shell['user']" :role="$shell['user']['role']" />
    </x-slot:topbarEnd>

    {{ $slot }}
</x-layouts.shell>
