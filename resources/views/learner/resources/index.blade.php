<x-layouts.learner title="Resources">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="GET" action="{{ route('learner.resources') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="w-52">
            <x-select name="platform" label="Academy" size="sm" :autosubmit="true"
                      :options="$page['filters']['platforms']"
                      :selected="$page['filters']['platform']" />
        </div>
        <div class="w-52">
            <x-select name="type" label="Type" size="sm" :autosubmit="true"
                      :options="$page['filters']['types']"
                      :selected="$page['filters']['type']" />
        </div>
        @if (filled($page['filters']['platform']) || filled($page['filters']['type']))
            <a href="{{ route('learner.resources') }}" class="mb-1 text-dense font-medium text-accent-700 hover:underline">Clear filters</a>
        @endif
    </form>

    @if (count($page['rows']))
        <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($page['rows'] as $row)
                @php
                    $type = (string) ($row['type'] ?? '');
                    $icons = [
                        'manual' => 'book',
                        'guide' => 'book',
                        'template' => 'file',
                        'document' => 'file',
                        'infographic' => 'layers',
                        'presentation' => 'chart',
                        'pdf' => 'file',
                        'video' => 'video',
                    ];
                    $icon = $icons[$type] ?? 'file';
                @endphp
                <article class="app-card group flex min-w-0 flex-col overflow-hidden rounded-md border border-clay-200 bg-chalk transition-colors hover:border-clay-300 focus-within:border-accent-500">
                    <div class="flex grow flex-col gap-1.5 p-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-clay-200 bg-papyrus text-fern-500">
                                <x-icon :name="$icon" class="h-4 w-4" />
                            </span>
                            <span class="inline-flex items-center rounded-full border border-clay-200 bg-papyrus px-2 py-0.5 text-micro font-medium capitalize text-fern-500">
                                {{ $row['type_label'] ?? $type }}
                            </span>
                        </div>

                        <h2 class="mt-1 font-display text-dense font-semibold leading-snug text-basalt-900">
                            <a href="{{ $row['href'] }}" class="rounded-xs hover:text-accent-600">{{ $row['title'] }}</a>
                        </h2>

                        <p class="line-clamp-2 text-micro leading-relaxed text-fern-500">
                            {{ $row['platform_name'] }}
                            @if (($row['attached_to'] ?? '') !== '')
                                · {{ $row['attached_to'] }}
                            @endif
                        </p>

                        @if (($row['size'] ?? '') !== '')
                            <p class="text-micro text-fern-500">{{ $row['size'] }}</p>
                        @endif
                    </div>

                    <div class="border-t border-clay-100 px-3.5 py-2.5">
                        <x-button size="sm" :href="$row['href']">Open</x-button>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="mt-6">
            <x-panel :padded="false">
                <x-empty-state icon="file"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']">
                    <x-slot:actions>
                        <x-button :href="route('learner.courses')">My courses</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        </div>
    @endif
</x-layouts.learner>
