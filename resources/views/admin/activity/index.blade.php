<x-layouts.super-admin title="Activity logs">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.activity', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('admin.activity') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="w-56">
            <x-select name="user" label="User" size="sm"
                      :options="$page['filters']['users']"
                      :selected="$page['filters']['user']" />
        </div>
        <div class="w-48">
            <x-select name="platform" label="Platform" size="sm"
                      :options="$page['filters']['platforms']"
                      :selected="$page['filters']['platform']" />
        </div>
        <div class="w-56">
            <x-select name="action" label="Action" size="sm"
                      :options="$page['filters']['actions']"
                      :selected="$page['filters']['action']" />
        </div>
        <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
    </form>

    <div class="mt-4">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="$page['columns']"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="56rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <span class="flex items-center gap-2">
                                    <x-avatar :name="$row['user']" size="sm" />
                                    <span>{{ $row['user'] }}</span>
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['platform'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['action_label'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-600">{{ $row['target'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['date'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.super-admin>
