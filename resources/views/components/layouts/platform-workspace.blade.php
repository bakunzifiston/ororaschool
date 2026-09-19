@props(['title' => null, 'platform' => null])

@php
    $slug = $platform ?? request()->route('platform') ?? 'gemura';
    $shell = \App\Support\DemoData\DemoData::shell('platform-workspace', $slug);
    $params = ['platform' => $shell['platform']['slug']];
@endphp

<x-layouts.shell experience="platform-workspace" :title="$title" railed :user="$shell['user']">

    <x-slot:sidebar>
        <div class="flex h-14 shrink-0 items-center justify-between gap-2 px-4">
            <x-layouts.partials.brand :href="route('workspace.dashboard', $params)" on-basalt />
            <button type="button" x-on:click="nav = false"
                    class="rounded-sm p-1 text-fern-400 hover:text-white lg:hidden"
                    aria-label="Close navigation">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>

        {{-- Which platform this rail is scoped to. The switcher itself lives in
             the top bar, per the approved layout. --}}
        <div class="border-y border-basalt-800 px-4 py-3">
            <p class="text-micro text-fern-400">Platform</p>
            <p class="mt-0.5 truncate text-dense text-white">{{ $shell['platform']['name'] }}</p>
            <p class="truncate text-micro text-fern-400">{{ $shell['platform']['discipline'] }}</p>
        </div>

        <x-layouts.partials.sidebar-nav :items="$shell['navigation']" :params="$params" />

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

    <x-slot:mobileBrand>
        <x-layouts.partials.workspace-switcher id="workspace-switcher-mobile"
                                               :current="$shell['platform']" :platforms="$shell['platforms']" />
    </x-slot:mobileBrand>

    <x-slot:topbarStart>
        <x-layouts.partials.workspace-switcher id="workspace-switcher-topbar"
                                               :current="$shell['platform']" :platforms="$shell['platforms']" />
    </x-slot:topbarStart>

    <x-slot:topbarEnd>
        <x-layouts.partials.user-menu :user="$shell['user']" :role="$shell['user']['role']" />
    </x-slot:topbarEnd>

    {{ $slot }}
</x-layouts.shell>
