<x-layouts.super-admin title="Permissions">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.permissions', ['empty' => 1])">Preview empty</x-button>
            <x-button variant="secondary" :href="route('admin.roles')">Open roles</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-8">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="$page['columns']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-accent-50/60">
                            <td class="px-4 py-3 font-medium text-basalt-900">{{ $row['label'] }}</td>
                            <td class="px-3 py-2.5 font-mono text-micro text-fern-500">{{ $row['key'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['area'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.super-admin>
