<x-layouts.learner title="Learning paths">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($page['paths'] as $path)
            <article class="app-card group flex min-w-0 flex-col overflow-hidden rounded-md border border-clay-200 bg-chalk transition-colors hover:border-clay-300 focus-within:border-accent-500">
                <div class="relative aspect-[16/9] overflow-hidden bg-accent-50">
                    @if ($path['cover'] ?? null)
                        <img src="{{ asset($path['cover']) }}"
                             alt=""
                             width="640"
                             height="360"
                             loading="lazy"
                             decoding="async"
                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
                    @else
                        <span class="flex h-full items-center justify-center">
                            <x-icon name="path" class="h-7 w-7 text-accent-400" />
                        </span>
                    @endif
                    <span class="absolute right-2 top-2">
                        @if ($path['cross_platform'])
                            <span class="inline-flex items-center rounded-full border border-basalt-800 bg-basalt-800 px-2 py-0.5 text-micro font-medium text-clay-100">
                                {{ $path['platform_count'] }} academies
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full border border-clay-200 bg-papyrus px-2 py-0.5 text-micro font-medium text-fern-500">
                                1 academy
                            </span>
                        @endif
                    </span>
                </div>

                <div class="flex grow flex-col gap-1.5 p-3.5">
                    <h2 class="font-display text-dense font-semibold leading-snug text-basalt-900">
                        <a href="{{ route('learner.paths.show', ['path' => $path['slug']]) }}"
                           class="rounded-xs hover:text-accent-600">{{ $path['title'] }}</a>
                    </h2>

                    <p class="line-clamp-2 text-micro leading-relaxed text-fern-500">{{ $path['summary'] }}</p>

                    <p class="text-micro text-fern-500">
                        {{ $path['course_count'] }} {{ $path['course_count'] === 1 ? 'course' : 'courses' }}
                        @if ($path['next'] ?? null)
                            · Next: {{ $path['next']['course']['title'] }}
                        @endif
                    </p>

                    <div class="mt-auto pt-1.5">
                        <x-progress-bar :value="$path['progress']" size="sm" :meta="implode(' · ', $path['platforms'])" />
                    </div>
                </div>

                <div class="border-t border-clay-100 px-3.5 py-2.5">
                    <x-button size="sm"
                              :icon="$path['started'] ? 'play' : null"
                              :href="route('learner.paths.show', ['path' => $path['slug']])">
                        {{ $path['cta'] }}
                    </x-button>
                </div>
            </article>
        @empty
            <x-panel class="sm:col-span-2 xl:col-span-3" :padded="false">
                <x-empty-state icon="path"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button :href="route('learner.courses')">My courses</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        @endforelse
    </div>
</x-layouts.learner>
