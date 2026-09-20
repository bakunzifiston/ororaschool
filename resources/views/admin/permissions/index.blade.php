<x-layouts.super-admin title="Permissions">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.permissions', ['empty' => 1])">Preview empty</x-button>
            <x-button variant="secondary" :href="route('admin.roles')">Open the role editor</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        @if (! count($page['catalog']))
            <x-panel :padded="false">
                <x-empty-state icon="key"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button :href="route('admin.roles')">Open the role editor</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        @else
            <div class="grid grid-cols-1 gap-3">
                @foreach ($page['catalog'] as $group)
                    <section class="min-w-0 rounded-md border border-clay-200 bg-chalk">
                        <header class="flex items-baseline justify-between gap-3 border-b-2 border-basalt-800 bg-papyrus px-4 py-2.5">
                            <div>
                                <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $group['label'] }}</h2>
                                <p class="figure text-micro text-fern-500">{{ $group['area'] }}.*</p>
                            </div>
                            <p class="figure text-micro text-fern-500">{{ count($group['permissions']) }} keys</p>
                        </header>
                        <ul class="divide-y divide-clay-100">
                            @foreach ($group['permissions'] as $permission)
                                <li class="px-4 py-2.5">
                                    <p class="text-dense font-medium text-basalt-800">{{ $permission['label'] }}</p>
                                    <p class="mt-0.5 font-mono text-micro text-fern-500">{{ $permission['key'] }}</p>
                                    <p class="mt-0.5 text-micro leading-relaxed text-fern-500">{{ $permission['hint'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.super-admin>
