<x-layouts.platform-workspace title="Courses" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.courses', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('workspace.courses.create', ['platform' => $platformSlug])">New course</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.courses', ['platform' => $platformSlug]) }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="w-48">
            <x-select name="status" label="Status" size="sm"
                      :options="$page['filters']['statuses']"
                      :selected="$page['filters']['status']" />
        </div>
        <div class="w-56">
            <x-select name="academy" label="Academy" size="sm"
                      :options="$page['filters']['academies']"
                      :selected="$page['filters']['academy']" />
        </div>
        <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
    </form>

    <div class="mt-4">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Course'],
                                ['key' => 'academy', 'label' => 'Academy'],
                                ['key' => 'category', 'label' => 'Category'],
                                ['key' => 'instructor', 'label' => 'Instructor'],
                                ['key' => 'difficulty', 'label' => 'Difficulty'],
                                ['key' => 'status', 'label' => 'Status'],
                                ['key' => 'enrolled', 'label' => 'Enrolled', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="64rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.courses.show', ['platform' => $platformSlug, 'course' => $row['slug']]) }}"
                                   class="flex items-center gap-2.5">
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-sm border border-clay-200 bg-accent-50 text-accent-500">
                                        <x-icon :name="['gemura' => 'droplet', 'buchapro' => 'tag', 'ororafarm' => 'sprout', 'feedgrid' => 'layers'][$row['platform']] ?? 'book'" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block font-medium text-basalt-900 hover:text-accent-600">{{ $row['title'] }}</span>
                                        <span class="text-micro text-fern-400">{{ $row['lessons'] }} lessons</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['academy'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['category'] }}</td>
                            <td class="px-3 py-2.5">
                                <span class="flex items-center gap-2">
                                    <x-avatar :name="$row['instructor']" size="sm" />
                                    <span class="text-dense">{{ $row['instructor'] }}</span>
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-micro">{{ $row['difficulty'] }}</td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ number_format($row['enrolled']) }}</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
