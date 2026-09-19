<x-layouts.platform-workspace title="Modules" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm"
                      :href="route('workspace.modules', ['platform' => $platformSlug, 'course' => $page['course']['slug'] ?? null, 'empty' => 1])">
                Preview empty
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.modules', ['platform' => $platformSlug]) }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-72 grow">
            <x-select name="course" label="Course" size="sm"
                      :options="$page['courses']"
                      :selected="$page['course']['slug'] ?? ''" />
        </div>
        <x-button type="submit" variant="secondary" size="sm">Open builder</x-button>
    </form>

    @if ($page['course'])
        <div class="mt-4 flex flex-wrap gap-2">
            <form method="POST" action="{{ route('workspace.modules.store', ['platform' => $platformSlug]) }}">
                @csrf
                <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                <x-button type="submit" icon="plus" size="sm">Add module</x-button>
            </form>
            <form method="POST" action="{{ route('workspace.lessons.store', ['platform' => $platformSlug]) }}">
                @csrf
                <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                <x-button type="submit" variant="secondary" icon="plus" size="sm">Add lesson</x-button>
            </form>
        </div>
    @endif

    <div class="mt-4">
        @if (! count($page['modules']))
            <x-panel :padded="false">
                <x-empty-state icon="layers"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @else
            <ol class="grid gap-3">
                @foreach ($page['modules'] as $module)
                    <li class="rounded-md border border-clay-200 bg-chalk">
                        <div class="flex items-center gap-2 border-b border-clay-200 bg-papyrus px-3 py-2.5">
                            <span class="cursor-grab text-fern-400" aria-hidden="true"><x-icon name="grip" class="h-4 w-4" /></span>
                            <span class="figure text-micro text-fern-400">{{ $module['order'] }}</span>
                            <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $module['title'] }}</h2>
                        </div>
                        <ul class="divide-y divide-clay-100">
                            @foreach ($module['lessons'] as $lesson)
                                <li class="flex flex-wrap items-center gap-3 px-3 py-2.5">
                                    <span class="cursor-grab text-fern-300" aria-hidden="true"><x-icon name="grip" class="h-3.5 w-3.5" /></span>
                                    <p class="min-w-0 grow text-dense text-basalt-800">{{ $lesson['title'] }}</p>
                                    <x-content-type :type="$lesson['type']" />
                                    <span class="figure text-micro text-fern-400">{{ $lesson['duration'] }}m</span>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</x-layouts.platform-workspace>
