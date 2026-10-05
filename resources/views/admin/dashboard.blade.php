<x-layouts.super-admin title="Dashboard">
    @php
        $firstName = str(auth()->user()->name ?? 'there')->before(' ');
        $hour = (int) now()->format('G');
        $hello = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $updatedOn = now()->toFormattedDateString();
    @endphp

    <div data-dashboard="estate">
    <header class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0 max-w-2xl">
            <h1 class="estate-title text-basalt-900">
                {{ $hello }}, {{ $firstName }}
                <span aria-hidden="true">👋</span>
            </h1>
            <p class="mt-2 text-dense leading-relaxed text-fern-500">{{ $page['header']['subtitle'] }}</p>
        </div>
        <div class="flex shrink-0 flex-wrap items-center gap-3">
            <p class="estate-chip">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#eef3ef] text-fern-500" aria-hidden="true">
                    <x-icon name="calendar" class="h-4 w-4" />
                </span>
                <span>
                    <span class="block text-[0.65rem] tracking-wide text-fern-400">Last updated</span>
                    <span class="figure font-medium text-basalt-800">{{ $updatedOn }}</span>
                </span>
            </p>
            <x-button variant="leaf" icon="plus" :href="route('admin.platforms.create')" class="h-11 rounded-full px-5">
                Add academy
            </x-button>
        </div>
    </header>

    <section class="mt-8" aria-label="Key performance indicators">
        <x-kpi-strip :items="$page['stats']" solid />
    </section>

    <div class="estate-stack estate-split">
        <x-panel>
            <x-pie-chart icon="book"
                         :title="$page['charts']['catalogue']['title']"
                         :subtitle="$page['charts']['catalogue']['subtitle']"
                         :headline="$page['charts']['catalogue']['headline'] ?? null"
                         :caption="$page['charts']['catalogue']['caption'] ?? null"
                         :labels="$page['charts']['catalogue']['labels']"
                         :values="$page['charts']['catalogue']['values']" />
        </x-panel>
        <x-panel>
            <x-line-chart icon="users"
                          :title="$page['charts']['users']['title']"
                          :subtitle="$page['charts']['users']['subtitle']"
                          :labels="$page['charts']['users']['labels']"
                          :values="$page['charts']['users']['values']" />
        </x-panel>
    </div>

    <section class="estate-stack" aria-labelledby="platform-overview-heading">
        <x-panel>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex min-w-0 items-start gap-2.5">
                    <span class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full kpi-tint-green" aria-hidden="true">
                        <x-icon name="cap" class="h-4 w-4" />
                    </span>
                    <div class="min-w-0">
                        <h2 id="platform-overview-heading" class="font-semibold text-basalt-900">Academy overview</h2>
                        <p class="mt-0.5 text-micro text-fern-500">Status, users and course availability across the estate.</p>
                    </div>
                </div>
                <a href="{{ route('admin.platforms') }}" class="inline-flex items-center gap-1.5 text-dense font-medium text-ok">
                    All academies
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            <div class="mt-5 min-w-0 overflow-x-auto">
                <table class="w-full border-collapse text-dense" style="min-width: 40rem">
                    <thead>
                        <tr class="border-b border-clay-100">
                            <th scope="col" class="py-3 pr-4 text-left text-micro font-medium text-fern-400">Academy</th>
                            <th scope="col" class="px-4 py-3 text-left text-micro font-medium text-fern-400">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-400">Academies</th>
                            <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-400">Users</th>
                            <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-400">Courses</th>
                            <th scope="col" class="py-3 pl-4 text-right text-micro font-medium text-fern-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($page['platforms']['rows'] as $row)
                            <tr class="border-b border-clay-100 last:border-b-0">
                                <td class="py-4 pr-4">
                                    <a href="{{ $row['view'] }}" class="flex min-w-0 items-center gap-3 rounded-sm">
                                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full kpi-tint-green" aria-hidden="true">
                                            <x-icon :name="$row['icon']" class="h-4 w-4" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate font-medium text-basalt-900">{{ $row['name'] }}</span>
                                            <span class="block truncate text-micro text-fern-500">{{ $row['discipline'] }}</span>
                                        </span>
                                    </a>
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-micro font-medium',
                                        'bg-[#e7f5dc] text-[#3f8c1f]' => $row['status'] === 'active',
                                        'bg-st-pending-bg text-st-pending' => $row['status'] !== 'active',
                                    ])>
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        {{ $row['status'] === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="figure px-4 py-4 text-right text-micro text-fern-500">{{ $row['academies'] }}</td>
                                <td class="figure px-4 py-4 text-right text-micro text-fern-500">{{ number_format($row['users']) }}</td>
                                <td class="figure px-4 py-4 text-right text-micro text-fern-500">{{ $row['courses'] }}</td>
                                <td class="py-4 pl-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-button variant="secondary" size="sm" icon="eye" :href="$row['view']" class="rounded-full">
                                            View
                                        </x-button>
                                        <div class="relative" x-data="{ open: false }">
                                            <button type="button"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full text-fern-400 hover:bg-clay-100 hover:text-basalt-800"
                                                    x-on:click="open = ! open"
                                                    :aria-expanded="open"
                                                    aria-label="More actions">
                                                <x-icon name="more" class="h-4 w-4" />
                                            </button>
                                            <div x-show="open"
                                                 x-cloak
                                                 x-on:click.outside="open = false"
                                                 class="absolute right-0 z-20 mt-1 w-32 overflow-hidden rounded-xl border border-clay-200 bg-white py-1 shadow-lg">
                                                <a href="{{ $row['edit'] }}" class="block px-3 py-2 text-left text-dense text-basalt-800 hover:bg-[#eef3ef]">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-panel>
    </section>

    <section class="estate-stack" aria-labelledby="attention-heading">
        <x-panel>
            <h2 id="attention-heading" class="font-semibold text-basalt-900">Needs attention</h2>

            @if (count($page['attention']))
                <ul class="mt-4 divide-y divide-clay-100">
                    @foreach ($page['attention'] as $item)
                        <li>
                            <a href="{{ $item['href'] }}" class="flex items-start justify-between gap-4 py-3.5">
                                <span class="min-w-0">
                                    <span class="block text-dense font-medium text-basalt-900">{{ $item['title'] }}</span>
                                    <span class="mt-1 block text-micro leading-relaxed text-fern-500">{{ $item['explanation'] }}</span>
                                </span>
                                <span class="inline-flex shrink-0 items-center gap-1 text-dense font-medium text-ok">
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
        </x-panel>
    </section>

    <section class="estate-stack" aria-labelledby="activity-heading">
        <x-panel>
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 id="activity-heading" class="font-semibold text-basalt-900">Recent activity</h2>
                <a href="{{ route('admin.activity') }}" class="inline-flex items-center gap-1.5 text-dense font-medium text-ok">
                    View all activity
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>

            @if (count($page['activity']))
                <ol class="mt-4 divide-y divide-clay-100">
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
        </x-panel>
    </section>

    <section class="estate-stack" aria-labelledby="quick-actions-heading">
        <x-panel>
            <h2 id="quick-actions-heading" class="font-semibold text-basalt-900">Quick actions</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($page['actions'] as $action)
                    <x-button size="sm" :variant="$action['variant'] === 'primary' ? 'leaf' : 'secondary'" :icon="$action['icon']" :href="route($action['route'])" class="rounded-full">
                        {{ $action['label'] }}
                    </x-button>
                @endforeach
            </div>
        </x-panel>
    </section>
    </div>
</x-layouts.super-admin>
