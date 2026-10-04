<x-layouts.super-admin :title="$page['header']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.roles.edit', $page['role']['key'])">Edit</x-button>
            <x-button variant="danger" x-on:click="$dispatch('open-modal', 'delete-{{ $page['role']['key'] }}')">Delete</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-panel title="Role" class="lg:col-span-1">
            <dl class="grid gap-3 text-dense">
                <div>
                    <dt class="text-micro text-fern-500">Type</dt>
                    <dd class="mt-0.5 text-basalt-800">{{ $page['role']['system'] ? 'System-protected' : 'Custom' }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Scope</dt>
                    <dd class="mt-0.5 text-basalt-800">{{ $page['role']['scope'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Holders</dt>
                    <dd class="figure mt-0.5 text-basalt-800">{{ number_format($page['role']['holders']) }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Permissions</dt>
                    <dd class="figure mt-0.5 text-basalt-800">{{ $page['heldCount'] }} of {{ $page['totalCount'] }}</dd>
                </div>
            </dl>
        </x-panel>

        <x-panel class="lg:col-span-2" title="Granted permissions" :padded="false">
            @if (count($page['catalog']))
                <div class="divide-y divide-clay-100">
                    @foreach ($page['catalog'] as $group)
                        <section class="px-4 py-3">
                            <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $group['label'] }}</h2>
                            <ul class="mt-2 grid gap-1">
                                @foreach ($group['permissions'] as $permission)
                                    <li class="flex items-baseline justify-between gap-3 text-dense">
                                        <span class="text-basalt-800">{{ $permission['label'] }}</span>
                                        <span class="shrink-0 font-mono text-micro text-fern-500">{{ $permission['key'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            @else
                <x-empty-state icon="key"
                               title="No permissions on this role"
                               :message="$page['role']['label'].' can sign in, but it cannot open a staff surface.'" />
            @endif
        </x-panel>
    </div>

    <x-modal name="delete-{{ $page['role']['key'] }}" width="md"
             :title="'Delete '.$page['role']['label'].'?'"
             :subtitle="$page['role']['scope']">
        <p>{{ $page['role']['label'] }} will leave the roles list. People who already hold it keep their assignments.</p>

        <x-slot:actions>
            <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
            <form method="POST" action="{{ route('admin.roles.destroy', $page['role']['key']) }}">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Delete role</x-button>
            </form>
        </x-slot:actions>
    </x-modal>
</x-layouts.super-admin>
