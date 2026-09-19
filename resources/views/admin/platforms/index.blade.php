<x-layouts.super-admin title="Platforms">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.platforms', ['empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('admin.platforms.create')">Add a platform</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="$page['columns']"
                          :rows="$page['rows']"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('admin.platforms.edit', $row['slug']) }}"
                                   class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['name'] }}</a>
                                <p class="text-micro text-fern-500">{{ $row['discipline'] }}</p>
                            </td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['academies'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ number_format($row['users']) }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['created'] }}</td>
                            <td class="px-3 py-2.5 text-right">
                                <div class="flex justify-end gap-2">
                                    <x-button variant="ghost" size="sm" :href="route('admin.platforms.edit', $row['slug'])">Edit</x-button>
                                    <x-button variant="{{ $row['status'] === 'active' ? 'danger' : 'secondary' }}" size="sm"
                                              x-on:click="$dispatch('open-modal', 'toggle-{{ $row['slug'] }}')">
                                        {{ $row['status'] === 'active' ? 'Deactivate' : 'Activate' }}
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>

    @foreach ($page['rows'] as $row)
        <x-modal name="toggle-{{ $row['slug'] }}" width="md"
                 :title="$row['status'] === 'active' ? 'Deactivate '.$row['name'].'?' : 'Activate '.$row['name'].'?'"
                 :subtitle="$row['discipline']">
            @if ($row['status'] === 'active')
                <p>{{ $row['name'] }} has {{ number_format($row['users']) }} users and {{ $row['academies'] }} academies.
                    Deactivating hides the workspace from new sign-ins. Certificates already issued still resolve.</p>
            @else
                <p>Activating {{ $row['name'] }} opens its workspace and lets assigned staff sign in. Confirm the owner is in place first.</p>
            @endif

            <x-slot:actions>
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <form method="POST" action="{{ route('admin.platforms.toggle', $row['slug']) }}">
                    @csrf
                    <x-button type="submit" :variant="$row['status'] === 'active' ? 'danger' : 'primary'">
                        {{ $row['status'] === 'active' ? 'Deactivate platform' : 'Activate platform' }}
                    </x-button>
                </form>
            </x-slot:actions>
        </x-modal>
    @endforeach
</x-layouts.super-admin>
