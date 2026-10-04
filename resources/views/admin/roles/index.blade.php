<x-layouts.super-admin title="Roles">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('admin.roles', ['empty' => 1])">Preview empty</x-button>
            <x-button icon="plus" :href="route('admin.roles.create')">New custom role</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-8">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="$page['columns']"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['roles']))
                    @foreach ($page['roles'] as $role)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-accent-50/60">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.roles.show', $role['key']) }}"
                                   class="font-medium text-basalt-900 hover:text-accent-600">{{ $role['label'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">
                                @if ($role['system'])
                                    System-protected
                                @else
                                    Custom
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $role['scope'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ number_format($role['holders']) }}</td>
                            <td class="px-3 py-2.5 text-right">
                                <x-row-actions
                                    :view="route('admin.roles.show', $role['key'])"
                                    :edit="route('admin.roles.edit', $role['key'])"
                                    :delete="'delete-'.$role['key']" />
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>

    @foreach ($page['roles'] as $role)
        <x-modal name="delete-{{ $role['key'] }}" width="md"
                 :title="'Delete '.$role['label'].'?'"
                 :subtitle="$role['scope']">
            <p>{{ $role['label'] }} will leave the roles list. People who already hold it keep their assignments.</p>

            <x-slot:actions>
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <form method="POST" action="{{ route('admin.roles.destroy', $role['key']) }}">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger">Delete role</x-button>
                </form>
            </x-slot:actions>
        </x-modal>
    @endforeach
</x-layouts.super-admin>
