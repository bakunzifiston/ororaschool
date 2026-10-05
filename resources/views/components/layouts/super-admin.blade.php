@props(['title' => null])

@php
    $shell = \App\Support\DemoData\DemoData::shell('super-admin');
    $platformCount = count(\App\Support\DemoData\Platforms::all());
@endphp

<x-layouts.shell experience="super-admin" :title="$title" railed :user="$shell['user']" content-class="estate-wide">

    <x-slot:sidebar>
        <div class="flex h-14 shrink-0 items-center justify-between gap-2 px-4">
            <x-layouts.partials.brand :href="route('admin.dashboard')" on-basalt />
            <button type="button" x-on:click="nav = false"
                    class="rounded-full p-1.5 text-fern-400 hover:bg-basalt-800 hover:text-white lg:hidden"
                    aria-label="Close navigation">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>

        <div class="mx-3 flex items-center justify-between gap-2 rounded-lg bg-basalt-800 px-3 py-2.5" role="status">
            <div class="min-w-0">
                <p class="text-micro text-fern-400">Scope</p>
                <p class="mt-0.5 truncate text-dense text-white">All academies</p>
            </div>
            <span class="figure shrink-0 text-micro text-fern-400">{{ $platformCount }}</span>
        </div>

        <x-layouts.partials.sidebar-nav :items="$shell['navigation']" />

        <div class="shrink-0 border-t border-basalt-800 p-3">
            <div class="flex items-center gap-2.5 rounded-lg px-1 py-1">
                <x-avatar :name="$shell['user']['name']" size="md" on-basalt />
                <div class="min-w-0">
                    <p class="truncate text-dense text-white">{{ $shell['user']['name'] }}</p>
                    <p class="truncate text-micro text-fern-400">{{ $shell['role']['label'] ?? 'Super Admin' }}</p>
                </div>
            </div>

            <x-layouts.partials.rail-sign-out />
        </div>
    </x-slot:sidebar>

    <x-slot:topbarEnd>
        <x-layouts.partials.user-menu
            :user="$shell['user']"
            :role="$shell['role']"
            :links="[['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'cog']]" />
    </x-slot:topbarEnd>

    {{ $slot }}
</x-layouts.shell>
