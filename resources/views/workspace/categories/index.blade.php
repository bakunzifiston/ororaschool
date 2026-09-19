<x-layouts.platform-workspace title="Categories" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.categories', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        @if (! count($page['tree']))
            <x-panel :padded="false">
                <x-empty-state icon="folder"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @else
            <ul class="grid gap-3">
                @foreach ($page['tree'] as $academy)
                    <li class="rounded-md border border-clay-200 bg-chalk">
                        <div class="flex items-center gap-2 border-b-2 border-basalt-800 bg-papyrus px-3 py-2.5">
                            <span class="cursor-grab text-fern-400" aria-hidden="true"><x-icon name="grip" class="h-4 w-4" /></span>
                            <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $academy['name'] }}</h2>
                            <span class="text-micro text-fern-400">Academy</span>
                        </div>
                        <ul class="divide-y divide-clay-100">
                            @foreach ($academy['categories'] as $category)
                                <li class="px-3 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="cursor-grab text-fern-300" aria-hidden="true"><x-icon name="grip" class="h-3.5 w-3.5" /></span>
                                        <p class="text-dense font-medium text-basalt-800">{{ $category['name'] }}</p>
                                        <span class="text-micro text-fern-400">Category</span>
                                    </div>
                                    @if (count($category['children'] ?? []))
                                        <ul class="mt-2 space-y-1 border-l border-clay-200 pl-6">
                                            @foreach ($category['children'] as $child)
                                                <li class="flex items-center gap-2 py-0.5">
                                                    <span class="cursor-grab text-fern-300" aria-hidden="true"><x-icon name="grip" class="h-3 w-3" /></span>
                                                    <span class="text-dense text-fern-600">{{ $child['name'] }}</span>
                                                    <span class="text-micro text-fern-400">Sub-category</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.platform-workspace>
