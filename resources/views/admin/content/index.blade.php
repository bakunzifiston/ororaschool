<x-layouts.super-admin title="Global content">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.content', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="$page['columns']"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        @php
                            $href = $row['kind'] === 'Course'
                                ? route('workspace.courses', ['platform' => $row['platform_slug']])
                                : route('workspace.categories', ['platform' => $row['platform_slug']]);
                        @endphp
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ $href }}" class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['title'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-micro text-fern-500">{{ $row['kind'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['platform'] }}</td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.super-admin>
