<x-layouts.platform-workspace title="Live sessions" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.sessions', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('workspace.sessions.create', ['platform' => $platformSlug])">Schedule</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Session'],
                                ['key' => 'instructor', 'label' => 'Instructor'],
                                ['key' => 'course', 'label' => 'Course'],
                                ['key' => 'starts_at', 'label' => 'When'],
                                ['key' => 'status', 'label' => 'Status'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="52rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.sessions.edit', ['platform' => $platformSlug, 'session' => $row['id']]) }}"
                                   class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['title'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['instructor'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['course'] }}</td>
                            <td class="px-3 py-2.5 text-micro text-fern-500">{{ $row['starts'] }}</td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
