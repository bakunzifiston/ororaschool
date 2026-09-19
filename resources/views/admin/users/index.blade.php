<x-layouts.super-admin title="Users">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.users', ['empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('admin.users.create')">Invite a user</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('admin.users') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-48 grow">
            <x-field name="q" label="Search" size="sm" :value="$page['filters']['q']"
                     placeholder="Name or email" />
        </div>
        <div class="w-44">
            <x-select name="status" label="Status" size="sm"
                      :options="$page['filters']['statuses']"
                      :selected="$page['filters']['status']" />
        </div>
        <div class="w-48">
            <x-select name="platform" label="Platform" size="sm"
                      :options="$page['filters']['platforms']"
                      :selected="$page['filters']['platform']" />
        </div>
        <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
    </form>

    <div class="mt-4">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="$page['columns']"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('admin.users.show', $row['id']) }}" class="flex items-center gap-2 rounded-xs">
                                    <x-avatar :name="$row['name']" size="sm" />
                                    <span class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['name'] }}</span>
                                </a>
                            </td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['email'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['district'] }}</td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                            <td class="px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['last_seen'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.super-admin>
