<x-layouts.platform-workspace title="Resources" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button icon="plus" :href="route('workspace.resources.create', ['platform' => $platformSlug])">Upload</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.resources', ['platform' => $platformSlug]) }}" class="app-filters">
        <div class="w-52">
            <x-select name="type" label="Type" size="sm" :autosubmit="true"
                      :options="$page['filters']['types']"
                      :selected="$page['filters']['type']" />
        </div>
    </form>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Resource'],
                                ['key' => 'type', 'label' => 'Type'],
                                ['key' => 'attached_to', 'label' => 'Attached to'],
                                ['key' => 'size', 'label' => 'Size', 'align' => 'right'],
                                ['key' => 'updated', 'label' => 'Updated', 'align' => 'right'],
                                ['key' => 'actions', 'label' => '', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0">
                            <td class="px-4 py-3.5 font-medium text-basalt-900">
                                <a href="{{ $row['href'] }}" class="hover:text-accent-700 hover:underline">{{ $row['title'] }}</a>
                            </td>
                            <td class="px-4 py-3.5 text-dense text-fern-500">{{ $row['type_label'] }}</td>
                            <td class="px-4 py-3.5 text-dense">{{ $row['attached_to'] }}</td>
                            <td class="figure px-4 py-3.5 text-right text-micro text-fern-500">{{ $row['size'] }}</td>
                            <td class="px-4 py-3.5 text-right text-micro text-fern-500">{{ $row['updated'] }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <x-row-actions :view="$row['href']" :edit="$row['edit']" :delete="$row['delete']" />
                            </td>
                        </tr>
                    @endforeach
                @endif

                <x-slot:empty-actions>
                    <x-button icon="plus" :href="route('workspace.resources.create', ['platform' => $platformSlug])">Upload</x-button>
                </x-slot:empty-actions>
            </x-data-table>
        </x-panel>
    </div>

    @foreach ($page['rows'] as $row)
        <x-modal :name="$row['delete']" width="md"
                 :title="'Delete '.$row['title'].'?'"
                 :subtitle="$row['type_label'].' · '.$row['attached_to']">
            <p>{{ $row['title'] }} will leave this academy and the public catalogue if it was an open handout.</p>

            <x-slot:actions>
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <form method="POST" action="{{ $row['destroy'] }}">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger">Delete resource</x-button>
                </form>
            </x-slot:actions>
        </x-modal>
    @endforeach
</x-layouts.platform-workspace>
