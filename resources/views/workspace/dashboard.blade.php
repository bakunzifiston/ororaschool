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
        <x-kpi-strip :items="array_merge($page['stats'], $page['secondaryStats'])" />
    </section>

    <section class="mt-10" aria-labelledby="recent-courses-heading">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 id="recent-courses-heading" class="font-display text-section text-basalt-900">Recent courses</h2>
            <x-button variant="ghost" size="sm" :href="route('workspace.courses', ['platform' => $platformSlug])" icon-after="arrow-right">
                Catalogue
            </x-button>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($page['recent'] as $course)
                <x-course-card :course="$course"
                               :platform-label="$page['platform']['name'] . ' · ' . $course['level']"
                               :href="route('workspace.courses.show', ['platform' => $platformSlug, 'course' => $course['slug']])" />
            @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <x-empty-state icon="book" title="No courses in this workspace yet"
                                   message="Open a draft. It stays off the learner catalogue until it is published.">
                        <x-slot:actions>
                            <x-button icon="plus" :href="route('workspace.courses.create', ['platform' => $platformSlug])">New course</x-button>
                        </x-slot:actions>
                    </x-empty-state>
                </div>
            @endforelse
        </div>
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
                        <li class="py-3.5">
                            <p class="text-dense font-medium text-basalt-900">{{ $session['title'] }}</p>
                            <p class="mt-0.5 text-micro text-fern-500">{{ $session['instructor'] }} · {{ $session['starts'] }}</p>
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
