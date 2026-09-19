<x-layouts.platform-workspace title="Instructors" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.instructors', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        @if (! count($page['rows']))
            <x-panel :padded="false">
                <x-empty-state icon="teacher"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @else
            <ul class="grid gap-3">
                @foreach ($page['rows'] as $row)
                    <li>
                        <a href="{{ route('workspace.instructors.show', ['platform' => $platformSlug, 'instructor' => $row['id']]) }}"
                           class="flex flex-wrap items-start justify-between gap-4 rounded-md border border-clay-200 bg-chalk px-4 py-3.5 transition-colors hover:border-clay-300">
                            <span class="flex items-center gap-3">
                                <x-avatar :name="$row['name']" size="md" />
                                <span>
                                    <span class="block font-medium text-basalt-900">{{ $row['name'] }}</span>
                                    <span class="block text-micro text-fern-500">{{ $row['district'] }}</span>
                                </span>
                            </span>
                            <span class="min-w-0 text-right">
                                <span class="figure block text-micro text-fern-400">{{ $row['courses_count'] }} courses</span>
                                @if (count($row['courses']))
                                    <span class="mt-0.5 block truncate text-micro text-fern-500">{{ implode(' · ', $row['courses']) }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layouts.platform-workspace>
