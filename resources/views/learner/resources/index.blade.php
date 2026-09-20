<x-layouts.learner title="Resources">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('learner.resources', ['empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('learner.resources') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="w-52">
            <x-select name="type" label="Type" size="sm" :autosubmit="true"
                      :options="$page['filters']['types']"
                      :selected="$page['filters']['type']" />
        </div>
    </form>

    <div class="mt-4">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Resource'],
                                ['key' => 'platform', 'label' => 'Platform'],
                                ['key' => 'attached_to', 'label' => 'Attached to'],
                                ['key' => 'type', 'label' => 'Type'],
                                ['key' => 'download', 'label' => ''],
                            ]"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="44rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5 font-medium text-basalt-900">{{ $row['title'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['platform_name'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['attached_to'] }}</td>
                            <td class="px-3 py-2.5 text-micro capitalize text-fern-500">{{ $row['type'] }}</td>
                            <td class="px-3 py-2.5 text-right">
                                <a href="#{{ $row['slug'] }}" class="text-dense font-medium text-accent-700 hover:underline">Download</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.learner>
