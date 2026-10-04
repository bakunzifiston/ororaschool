<x-layouts.super-admin title="Users">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.users', ['empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('admin.users.create')">Create a user</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('admin.users') }}" class="app-filters">
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
            <x-select name="platform" label="Academy" size="sm"
                      :options="$page['filters']['platforms']"
                      :selected="$page['filters']['platform']" />
        </div>
        <x-button type="submit" variant="secondary">Apply filters</x-button>
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
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-accent-50/60">
                            <td class="px-3 py-2.5">
                                <a href="{{ $row['view'] }}" class="flex items-center gap-2 rounded-xs">
                                    <x-avatar :name="$row['name']" size="sm" />
                                    <span class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['name'] }}</span>
                                </a>
                            </td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['email'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['district'] }}</td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                            <td class="px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['last_seen'] }}</td>
                            <td class="px-3 py-2.5 text-right">
                                <x-row-actions :view="$row['view']" :edit="$row['edit']" :delete="$row['delete']" />
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>

    @foreach ($page['rows'] as $row)
        @continue(! $row['delete'])
        <x-modal :name="$row['delete']" width="md"
                 :title="'Delete '.$row['name'].'?'"
                 :subtitle="$row['email']">
            <p>{{ $row['name'] }} will no longer be able to sign in. Enrolments on this account are removed with it.</p>

            <x-slot:actions>
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <form method="POST" action="{{ $row['destroy'] }}">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger">Delete user</x-button>
                </form>
            </x-slot:actions>
        </x-modal>
    @endforeach
</x-layouts.super-admin>
