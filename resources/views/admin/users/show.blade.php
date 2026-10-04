<x-layouts.super-admin :title="$page['user']['name']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['persisted'])
                <x-button variant="secondary" :href="route('admin.accounts.edit', $page['user']['id'])">Edit</x-button>
                @if ($page['canDelete'])
                    <x-button variant="danger" x-on:click="$dispatch('open-modal', 'delete-user')">Delete</x-button>
                @endif
            @else
                <x-button variant="secondary" :href="route('admin.users.edit', $page['user']['id'])">Edit</x-button>
                <x-button icon="plus" x-on:click="$dispatch('open-modal', 'assign-platform')">Assign to academy</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-panel title="Account" class="lg:col-span-1">
            <dl class="grid gap-3 text-dense">
                <div>
                    <dt class="text-micro text-fern-500">Status</dt>
                    <dd class="mt-1"><x-status-badge :status="$page['user']['status']" /></dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">District</dt>
                    <dd class="mt-0.5 text-basalt-800">{{ $page['user']['district'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Last seen</dt>
                    <dd class="mt-0.5 text-basalt-800">{{ $page['user']['last_seen'] }}</dd>
                </div>
            </dl>
        </x-panel>

        <x-panel class="lg:col-span-2" title="Academy assignments"
                 subtitle="One person, a different role on each academy — the user_platform_roles shape."
                 :padded="false">
            @if (count($page['assignments']))
                <ul class="divide-y divide-clay-100">
                    @foreach ($page['assignments'] as $assignment)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-medium text-basalt-900">{{ $assignment['platform'] }}</p>
                                <p class="text-micro text-fern-500">{{ $assignment['discipline'] }}</p>
                            </div>
                            <x-role-chip :role="$assignment['role']" :show-scope="! ($page['persisted'] ?? false)" />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="layers"
                               title="Not assigned to any academy yet"
                               message="Assign {{ $page['user']['name'] }} to an academy with a role. Until then they can sign in but will not see a workspace.">
                    <x-slot:actions>
                        @if ($page['persisted'])
                            <x-button size="sm" :href="route('admin.accounts.edit', $page['user']['id'])">Edit</x-button>
                        @else
                            <x-button size="sm" x-on:click="$dispatch('open-modal', 'assign-platform')">Assign to academy</x-button>
                        @endif
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </x-panel>
    </div>

    @if ($page['persisted'] && $page['canDelete'])
        <x-modal name="delete-user" width="md"
                 :title="'Delete '.$page['user']['name'].'?'"
                 :subtitle="$page['user']['email']">
            <p>{{ $page['user']['name'] }} will no longer be able to sign in. Enrolments on this account are removed with it.</p>

            <x-slot:actions>
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <form method="POST" action="{{ route('admin.accounts.destroy', $page['user']['id']) }}">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger">Delete user</x-button>
                </form>
            </x-slot:actions>
        </x-modal>
    @endif

    @unless ($page['persisted'])
    <x-modal name="assign-platform" title="Assign to an academy"
             :subtitle="$page['user']['name']" width="md">
        <form method="POST" action="{{ route('admin.users.assign', $page['user']['id']) }}" class="grid gap-4">
            @csrf
            <x-select name="platform" label="Academy" size="sm"
                      :options="$page['platforms']" />
            <x-select name="role" label="Role on that academy" size="sm"
                      :options="$page['roles']" />
            <p class="text-micro text-fern-500">
                This writes a user_platform_roles row. Nothing is saved in this build.
            </p>
            <div class="flex justify-end gap-2">
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <x-button type="submit">Assign role</x-button>
            </div>
        </form>
    </x-modal>
    @endunless
</x-layouts.super-admin>
