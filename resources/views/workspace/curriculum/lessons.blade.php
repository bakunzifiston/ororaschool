<x-layouts.platform-workspace title="Lessons" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.lessons', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
            <x-button :href="route('workspace.modules', ['platform' => $platformSlug])">Open builder</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Lesson'],
                                ['key' => 'course', 'label' => 'Course'],
                                ['key' => 'module', 'label' => 'Module'],
                                ['key' => 'type', 'label' => 'Type'],
                                ['key' => 'duration', 'label' => 'Mins', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="52rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.modules', ['platform' => $platformSlug, 'course' => $row['course_slug']]) }}"
                                   class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['title'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['course'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['module'] }}</td>
                            <td class="px-3 py-2.5"><x-content-type :type="$row['type']" /></td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['duration'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
