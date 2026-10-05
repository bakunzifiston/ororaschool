<x-layouts.platform-workspace title="Dashboard" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']"
                   :meta="$page['header']['updated'] ?? null">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('workspace.sessions', ['platform' => $platformSlug])" icon="video">
                Live sessions
            </x-button>
            <x-button icon="plus" :href="route('workspace.courses.create', ['platform' => $platformSlug])">New course</x-button>
        </x-slot:actions>
    </x-page-header>

    <section class="mt-8" aria-label="Key performance indicators">
        <x-kpi-strip :items="array_merge($page['stats'], $page['secondaryStats'])" solid />
    </section>

    <section class="mt-10" aria-label="Course charts">
        <div class="mt-0 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <x-panel>
                <x-pie-chart :title="$page['charts']['status']['title']"
                             :subtitle="$page['charts']['status']['subtitle']"
                             :headline="$page['charts']['status']['headline'] ?? null"
                             :labels="$page['charts']['status']['labels']"
                             :values="$page['charts']['status']['values']" />
            </x-panel>
            <x-panel>
                <x-line-chart :title="$page['charts']['enrolment']['title']"
                              :subtitle="$page['charts']['enrolment']['subtitle']"
                              :labels="$page['charts']['enrolment']['labels']"
                              :values="$page['charts']['enrolment']['values']" />
            </x-panel>
        </div>
    </section>

    <section class="mt-10" aria-labelledby="recent-courses-heading">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="min-w-0 max-w-2xl">
                <h2 id="recent-courses-heading" class="font-display text-section text-basalt-900">Recent courses</h2>
                <p class="mt-1 text-dense text-fern-500">Latest catalogue work on this academy.</p>
            </div>
            <x-button variant="ghost" size="sm" :href="route('workspace.courses', ['platform' => $platformSlug])" icon-after="arrow-right">
                Catalogue
            </x-button>
        </div>

        @if (count($page['recent']))
            <div class="mt-5 min-w-0 overflow-x-auto">
                <table class="w-full border-collapse text-dense" style="min-width: 40rem">
                    <thead>
                        <tr class="border-b border-clay-200">
                            <th scope="col" class="py-3 pr-4 text-left text-micro font-medium text-fern-500">Course</th>
                            <th scope="col" class="px-4 py-3 text-left text-micro font-medium text-fern-500">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-micro font-medium text-fern-500">Instructor</th>
                            <th scope="col" class="px-4 py-3 text-right text-micro font-medium text-fern-500">Enrolled</th>
                            <th scope="col" class="py-3 pl-4 text-right text-micro font-medium text-fern-500"><span class="sr-only">Open</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($page['recent'] as $course)
                            <tr class="border-b border-clay-100 last:border-b-0">
                                <td class="py-3.5 pr-4">
                                    <a href="{{ route('workspace.courses.show', ['platform' => $platformSlug, 'course' => $course['slug']]) }}"
                                       class="font-medium text-basalt-900 hover:text-accent-600">{{ $course['title'] }}</a>
                                    <p class="text-micro text-fern-500">{{ $course['level'] }} · {{ $course['lessons'] }} lessons</p>
                                </td>
                                <td class="px-4 py-3.5"><x-status-badge :status="$course['status']" /></td>
                                <td class="px-4 py-3.5 text-dense">{{ $course['instructor'] }}</td>
                                <td class="figure px-4 py-3.5 text-right text-micro text-fern-500">{{ number_format($course['enrolled']) }}</td>
                                <td class="py-3.5 pl-4 text-right">
                                    <x-row-actions
                                        :view="route('workspace.courses.show', ['platform' => $platformSlug, 'course' => $course['slug']])"
                                        :edit="route('workspace.courses.edit', ['platform' => $platformSlug, 'course' => $course['slug']])" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-empty-state class="mt-5" icon="book" title="No courses in this workspace yet"
                           message="Open a draft. It stays off the learner catalogue until it is published.">
                <x-slot:actions>
                    <x-button icon="plus" :href="route('workspace.courses.create', ['platform' => $platformSlug])">New course</x-button>
                </x-slot:actions>
            </x-empty-state>
        @endif
    </section>

    <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-5">
        <section class="lg:col-span-3" aria-labelledby="activity-heading">
            <h2 id="activity-heading" class="font-display text-section text-basalt-900">Recent activity</h2>

            @if (count($page['activity']))
                <ol class="mt-4 divide-y divide-clay-100 border-y border-clay-200">
                    @foreach ($page['activity'] as $entry)
                        <li class="flex gap-3 py-3.5">
                            <x-avatar :name="$entry['actor']" size="sm" class="mt-0.5" />
                            <div class="min-w-0">
                                <p class="text-dense leading-snug text-basalt-800">
                                    <span class="font-medium text-basalt-900">{{ $entry['actor'] }}</span>
                                    {{ $entry['action_label'] }}
                                    <span class="text-basalt-900">{{ $entry['target'] }}</span>
                                </p>
                                <p class="mt-0.5 text-micro text-fern-500">{{ $entry['at'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @else
                <x-empty-state class="mt-4" icon="history" title="No activity on this academy yet"
                               message="Publishing a course or scheduling a clinic writes a row here." />
            @endif
        </section>

        <section class="lg:col-span-2" aria-labelledby="sessions-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 id="sessions-heading" class="font-display text-section text-basalt-900">Upcoming live sessions</h2>
                <x-button variant="ghost" size="sm" :href="route('workspace.sessions', ['platform' => $platformSlug])" icon-after="arrow-right">
                    All sessions
                </x-button>
            </div>

            @if (count($page['sessions']))
                <ul class="mt-4 divide-y divide-clay-100 border-y border-clay-200">
                    @foreach ($page['sessions'] as $session)
                        <li class="flex items-start justify-between gap-3 py-3.5">
                            <div class="min-w-0">
                                <p class="text-dense font-medium text-basalt-900">{{ $session['title'] }}</p>
                                <p class="mt-0.5 text-micro text-fern-500">{{ $session['instructor'] }} · {{ $session['starts'] }}</p>
                            </div>
                            <x-row-actions :edit="route('workspace.sessions.edit', ['platform' => $platformSlug, 'session' => $session['id']])" class="shrink-0" />
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state class="mt-4" icon="video" title="Nothing scheduled this month"
                               message="Field officers book onto clinics up to two weeks ahead.">
                    <x-slot:actions>
                        <x-button variant="secondary" icon="plus"
                                  :href="route('workspace.sessions.create', ['platform' => $platformSlug])">
                            Schedule a session
                        </x-button>
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </section>
    </div>
</x-layouts.platform-workspace>
