<x-layouts.learner title="Learning paths">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('learner.paths', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid gap-4">
        @forelse ($page['paths'] as $path)
            <a href="{{ route('learner.paths.show', ['path' => $path['slug']]) }}"
               class="block rounded-md border border-clay-200 bg-chalk px-4 py-4 transition-colors hover:border-clay-300">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $path['title'] }}</h2>
                        <p class="mt-1 text-dense text-fern-500">{{ $path['summary'] }}</p>
                    </div>
                    @if ($path['cross_platform'])
                        <span class="shrink-0 rounded-sm border border-basalt-800 bg-basalt-800 px-2 py-0.5 text-micro font-medium text-clay-100">
                            {{ $path['platform_count'] }} platforms
                        </span>
                    @else
                        <span class="shrink-0 rounded-sm border border-clay-200 bg-papyrus px-2 py-0.5 text-micro text-fern-500">
                            1 platform
                        </span>
                    @endif
                </div>
                <div class="mt-4">
                    <x-progress-bar :value="$path['progress']" size="sm" :meta="implode(' · ', $path['platforms'])" />
                </div>
            </a>
        @empty
            <x-panel :padded="false">
                <x-empty-state icon="path"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @endforelse
    </div>
</x-layouts.learner>
