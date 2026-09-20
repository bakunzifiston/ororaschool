<x-layouts.super-admin title="Dashboard">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <a href="{{ route('admin.analytics') }}" class="text-dense font-medium text-accent-700 hover:underline">Analytics</a>
            <x-button icon="plus" :href="route('admin.platforms.create')">Add a platform</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($page['stats'] as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']"
                         :trend="$stat['trend']" :direction="$stat['direction']" :note="$stat['note']" />
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <x-panel class="lg:col-span-3" title="Platforms at a glance"
                 subtitle="Academies, users and course counts, derived from the same fixtures as the list."
                 variant="table" :padded="false">
            <x-slot:actions>
                <x-button variant="ghost" size="sm" :href="route('admin.platforms')" icon-after="arrow-right">All platforms</x-button>
            </x-slot:actions>

            <x-data-table :columns="$page['platforms']['columns']"
                          :rows="$page['platforms']['rows']"
                          min-width="36rem" />
        </x-panel>

        <x-panel class="lg:col-span-2" title="Recent activity" :padded="false">
            <x-slot:actions>
                <x-button variant="ghost" size="sm" :href="route('admin.activity')" icon-after="arrow-right">All logs</x-button>
            </x-slot:actions>

            @if (count($page['activity']))
                <ul class="divide-y divide-clay-100">
                    @foreach ($page['activity'] as $entry)
                        <li class="flex gap-2.5 px-4 py-2.5">
                            <x-avatar :name="$entry['actor']" size="sm" class="mt-0.5" />
                            <div class="min-w-0">
                                <p class="text-dense leading-snug text-basalt-800">
                                    <span class="font-medium">{{ $entry['actor'] }}</span>
                                    {{ $entry['action_label'] }}
                                    <span class="text-fern-600">{{ $entry['target'] }}</span>
                                </p>
                                <p class="mt-0.5 text-micro text-fern-500">{{ $entry['platform'] }} · {{ $entry['at'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="history" title="No activity on the estate yet"
                               message="Publishing a course, inviting a user or assigning a role writes a row here." />
            @endif
        </x-panel>
    </div>
</x-layouts.super-admin>
