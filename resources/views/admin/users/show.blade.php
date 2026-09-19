<x-layouts.super-admin :title="$page['user']['name']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.users.edit', $page['user']['id'])">Edit account</x-button>
            <x-button icon="plus" x-on:click="$dispatch('open-modal', 'assign-platform')">Assign to platform</x-button>
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

        <x-panel class="lg:col-span-2" title="Platform assignments"
                 subtitle="One person, a different role on each platform — the user_platform_roles shape."
                 :padded="false">
            @if (count($page['assignments']))
                <ul class="divide-y divide-clay-100">
                    @foreach ($page['assignments'] as $assignment)
                        <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-medium text-basalt-900">{{ $assignment['platform'] }}</p>
                                <p class="text-micro text-fern-500">{{ $assignment['discipline'] }}</p>
                            </div>
                            <x-role-chip :role="$assignment['role']" show-scope />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="layers"
                               title="Not assigned to any platform yet"
                               message="Assign {{ $page['user']['name'] }} to a platform with a role. Until then they can sign in but will not see a workspace.">
                    <x-slot:actions>
                        <x-button size="sm" x-on:click="$dispatch('open-modal', 'assign-platform')">Assign to platform</x-button>
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </x-panel>
    </div>

    <x-modal name="assign-platform" title="Assign to a platform"
             :subtitle="$page['user']['name']" width="md">
        <form method="POST" action="{{ route('admin.users.assign', $page['user']['id']) }}" class="grid gap-4">
            @csrf
            <x-select name="platform" label="Platform" size="sm"
                      :options="$page['platforms']" />
            <x-select name="role" label="Role on that platform" size="sm"
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
</x-layouts.super-admin>
