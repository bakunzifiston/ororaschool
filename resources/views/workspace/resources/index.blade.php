<x-layouts.platform-workspace title="Resources" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.resources', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('workspace.resources.create', ['platform' => $platformSlug])">Upload</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.resources', ['platform' => $platformSlug]) }}" class="mt-6 flex flex-wrap items-end gap-3">
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
                                ['key' => 'type', 'label' => 'Type'],
                                ['key' => 'attached_to', 'label' => 'Attached to'],
                                ['key' => 'size', 'label' => 'Size', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5 font-medium text-basalt-900">{{ $row['title'] }}</td>
                            <td class="px-3 py-2.5 text-micro capitalize text-fern-500">{{ $row['type'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-600">{{ $row['attached_to'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['size'] }}</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
