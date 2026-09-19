<x-layouts.super-admin :title="$page['header']['title']">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['role']['system'] ?? false)
                <span class="inline-flex items-center gap-1.5 rounded-sm border border-clay-200 bg-papyrus px-2 py-1 text-micro text-fern-500">
                    <x-icon name="lock" class="h-3.5 w-3.5" />
                    System-protected — permissions are fixed
                </span>
            @endif
        </x-slot:actions>
    </x-page-header>

    <form method="POST"
          action="{{ ($page['role']['key'] ?? '') ? route('admin.roles.update', $page['role']['key']) : route('admin.roles.store') }}"
          class="mt-6">
        @csrf

        @if (empty($page['role']['key']))
            <x-panel title="Role" class="mb-6 max-w-2xl">
                <div class="grid gap-5">
                    <x-field name="label" label="Name" size="sm" placeholder="e.g. District reviewer" />
                    <x-textarea name="description" label="What this role is for" rows="2" />
                </div>
            </x-panel>
        @endif

        <p class="mb-3 text-dense text-fern-500">
            <span class="figure font-medium text-basalt-800">{{ $page['heldCount'] }}</span>
            of
            <span class="figure font-medium text-basalt-800">{{ $page['totalCount'] }}</span>
            keys granted. Permissions are data on the role, not a hardcoded switch.
        </p>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            @foreach ($page['catalog'] as $group)
                @php
                    $groupHeld = count(array_filter(
                        $group['permissions'],
                        fn ($permission) => in_array($permission['key'], $page['held'], true)
                    ));
                @endphp

                <section class="min-w-0 rounded-md border border-clay-200 bg-chalk">
                    <header class="flex items-baseline justify-between gap-3 border-b-2 border-basalt-800 bg-papyrus px-4 py-2.5">
                        <div>
                            <h2 class="font-display text-panel font-semibold text-basalt-900">{{ $group['label'] }}</h2>
                            <p class="figure text-micro text-fern-400">{{ $group['area'] }}.*</p>
                        </div>
                        <p class="figure text-micro text-fern-500">{{ $groupHeld }}/{{ count($group['permissions']) }}</p>
                    </header>

                    <ul class="divide-y divide-clay-100">
                        @foreach ($group['permissions'] as $permission)
                            @php
                                $id = 'perm-'.str_replace('.', '-', $permission['key']);
                                $checked = in_array($permission['key'], $page['held'], true);
                                $locked = $page['role']['system'] ?? false;
                            @endphp
                            <li class="flex items-start gap-3 px-4 py-2.5">
                                <input type="checkbox"
                                       id="{{ $id }}"
                                       name="permissions[]"
                                       value="{{ $permission['key'] }}"
                                       @checked($checked)
                                       @disabled($locked)
                                       class="mt-1 h-4 w-4 shrink-0 rounded-xs border-clay-300 accent-[var(--color-accent-500)]">
                                <label for="{{ $id }}" class="min-w-0 grow {{ $locked ? 'cursor-default' : 'cursor-pointer' }}">
                                    <span class="block text-dense font-medium text-basalt-800">{{ $permission['label'] }}</span>
                                    <span class="mt-0.5 block font-mono text-micro text-fern-400">{{ $permission['key'] }}</span>
                                    <span class="mt-0.5 block text-micro leading-relaxed text-fern-500">{{ $permission['hint'] }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            @unless ($page['role']['system'] ?? false)
                <x-button type="submit">Save permissions</x-button>
            @endunless
            <x-button variant="secondary" :href="route('admin.roles')">Back to roles</x-button>
        </div>
    </form>
</x-layouts.super-admin>
