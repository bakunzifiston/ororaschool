<x-layouts.super-admin title="Dashboard">
    <x-page-header :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']"
                   :meta="$page['header']['updated'] ?? null">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('admin.analytics')" icon="chart">Analytics</x-button>
            <x-button icon="plus" :href="route('admin.platforms.create')">Add academy</x-button>
        </x-slot:actions>
    </x-page-header>

    <section class="mt-8" aria-label="Key performance indicators">
        <x-kpi-strip :items="$page['stats']" />
    </section>

    <section class="mt-10" aria-labelledby="platform-overview-heading">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0 max-w-2xl">
                <h2 id="platform-overview-heading" class="font-display text-section text-basalt-900">Academy overview</h2>
                <p class="mt-1 text-dense text-fern-500">Status, users and course availability across the estate.</p>
            </div>
            <x-button variant="ghost" size="sm" :href="route('admin.platforms')" icon-after="arrow-right">All academies</x-button>
        </div>

        <div class="mt-5 min-w-0 overflow-x-auto">
            <table class="w-full border-collapse text-dense" style="min-width: 40rem">
                <thead>
                    <tr class="border-b border-clay-200">
                        <th scope="col" class="py-3 pr-4 text-left text-micro font-medium text-fern-500">Academy</th>
                        <th scope="col" class="px-4 py-3 text-left text-micro font-medium text-fern-500">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-500">Academies</th>
                        <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-500">Users</th>
                        <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-500">Courses</th>
                        <th scope="col" class="py-3 pl-4 text-right text-micro font-medium text-fern-500"><span class="sr-only">Open</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($page['platforms']['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0">
                            <td class="py-3.5 pr-4">
                                <a href="{{ $row['href'] }}" class="flex min-w-0 items-center gap-3 rounded-sm">
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent-50 text-accent-600" aria-hidden="true">
                                        <x-icon :name="$row['icon']" class="h-4 w-4" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-basalt-900 hover:text-accent-600">{{ $row['name'] }}</span>
                                        <span class="block truncate text-micro text-fern-500">{{ $row['discipline'] }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-4 py-3.5"><x-status-badge :status="$row['status']" /></td>
                            <td class="figure px-4 py-3.5 text-right text-micro text-fern-500">{{ $row['academies'] }}</td>
                            <td class="figure px-4 py-3.5 text-right text-micro text-fern-500">{{ number_format($row['users']) }}</td>
                            <td class="figure px-4 py-3.5 text-right text-micro text-fern-500">{{ $row['courses'] }}</td>
                            <td class="py-3.5 pl-4 text-right">
                                <a href="{{ $row['href'] }}" class="text-dense font-medium text-accent-700 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-10" aria-labelledby="analytics-heading">
        <h2 id="analytics-heading" class="font-display text-section text-basalt-900">Analytics</h2>
        <div class="mt-5 grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-16">
            <x-bar-chart orientation="horizontal"
                         :title="$page['charts']['completion']['title']"
                         :headline="$page['charts']['completion']['headline'] ?? null"
                         :trend="$page['charts']['completion']['trend'] ?? null"
                         :trend-note="$page['charts']['completion']['trend_note'] ?? null"
                         :labels="$page['charts']['completion']['labels']"
                         :values="$page['charts']['completion']['values']"
                         :suffix="$page['charts']['completion']['suffix']"
                         :max="$page['charts']['completion']['max'] ?? null" />
            <x-bar-chart orientation="horizontal"
                         :title="$page['charts']['distribution']['title']"
                         :subtitle="$page['charts']['distribution']['subtitle']"
                         :labels="$page['charts']['distribution']['labels']"
                         :values="$page['charts']['distribution']['values']" />
        </div>
    </section>

    <section class="mt-10" aria-labelledby="attention-heading">
        <h2 id="attention-heading" class="font-display text-section text-basalt-900">Needs attention</h2>

        @if (count($page['attention']))
            <ul class="mt-4 divide-y divide-clay-100 border-y border-clay-200">
                @foreach ($page['attention'] as $item)
                    <li>
                        <a href="{{ $item['href'] }}" class="flex items-start justify-between gap-4 py-4 transition-colors hover:bg-accent-50/40">
                            <span class="min-w-0">
                                <span class="block text-dense font-medium text-basalt-900">{{ $item['title'] }}</span>
                                <span class="mt-1 block text-micro leading-relaxed text-fern-500">{{ $item['explanation'] }}</span>
                            </span>
                            <span class="inline-flex shrink-0 items-center gap-1 text-dense font-medium text-accent-700">
                                View
                                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-4 text-dense text-fern-500">Everything is up to date.</p>
        @endif
    </section>

    <section class="mt-10" aria-labelledby="activity-heading">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 id="activity-heading" class="font-display text-section text-basalt-900">Recent activity</h2>
            <x-button variant="ghost" size="sm" :href="route('admin.activity')" icon-after="arrow-right">View all activity</x-button>
        </div>

        @if (count($page['activity']))
            <ol class="mt-4 divide-y divide-clay-100 border-y border-clay-200">
                @foreach ($page['activity'] as $entry)
                    <li class="flex gap-3 py-3.5">
                        <x-avatar :name="$entry['actor']" size="sm" class="mt-0.5" />
                        <div class="min-w-0 grow">
                            <p class="text-dense leading-snug text-basalt-800">
                                <span class="font-medium text-basalt-900">{{ $entry['actor'] }}</span>
                                {{ $entry['action_label'] }}
                                <span class="text-basalt-900">{{ $entry['target'] }}</span>
                            </p>
                            <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-micro text-fern-500">
                                <x-icon :name="$entry['icon']" class="h-3 w-3" />
                                <span>{{ $entry['platform'] }}</span>
                                <span aria-hidden="true">·</span>
                                <time>{{ $entry['at'] }}</time>
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        @else
            <x-empty-state class="mt-4" icon="history" title="No activity on the estate yet"
                           message="Publishing a course, inviting a user or assigning a role writes a row here." />
        @endif
    </section>

    <section class="mt-10" aria-labelledby="quick-actions-heading">
        <h2 id="quick-actions-heading" class="font-display text-section text-basalt-900">Quick actions</h2>
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($page['actions'] as $action)
                <x-button size="sm" :variant="$action['variant']" :icon="$action['icon']" :href="route($action['route'])">
                    {{ $action['label'] }}
                </x-button>
            @endforeach
        </div>
    </section>
</x-layouts.super-admin>
