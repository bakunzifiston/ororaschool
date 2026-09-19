<x-layouts.platform-workspace :title="$page['platform']['name'] . ' workspace'" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="secondary" icon="video" :href="route('workspace.sessions', ['platform' => $platformSlug])">
                Live sessions
            </x-button>
            <x-button icon="plus" :href="route('workspace.courses.create', ['platform' => $platformSlug])">New course</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($page['stats'] as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']"
                         :trend="$stat['trend']" :direction="$stat['direction']" :note="$stat['note']" />
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($page['recent'] as $course)
            <x-course-card :course="$course"
                           :platform-label="$page['platform']['name'] . ' · ' . $course['level']"
                           :href="route('workspace.courses.show', ['platform' => $platformSlug, 'course' => $course['slug']])" />
        @empty
            <x-panel class="sm:col-span-2 lg:col-span-3" :padded="false">
                <x-empty-state icon="book" title="No courses in this workspace yet"
                               message="Open a draft. It stays off the learner catalogue until it is published.">
                    <x-slot:actions>
                        <x-button icon="plus" :href="route('workspace.courses.create', ['platform' => $platformSlug])">New course</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        @endforelse
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <x-panel class="lg:col-span-3" title="Recent activity" :padded="false">
            <x-slot:actions>
                <x-button variant="ghost" size="sm" :href="route('workspace.courses', ['platform' => $platformSlug])" icon-after="arrow-right">
                    Catalogue
                </x-button>
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
                                <p class="mt-0.5 text-micro text-fern-400">{{ $entry['at'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="history" title="No activity on this platform yet"
                               message="Publishing a course or scheduling a clinic writes a row here." />
            @endif
        </x-panel>

        <x-panel class="lg:col-span-2" title="Upcoming live sessions" :padded="false">
            <x-slot:actions>
                <x-button variant="ghost" size="sm" :href="route('workspace.sessions', ['platform' => $platformSlug])" icon-after="arrow-right">
                    All sessions
                </x-button>
            </x-slot:actions>

            @if (count($page['sessions']))
                <ul class="divide-y divide-clay-100">
                    @foreach ($page['sessions'] as $session)
                        <li class="px-4 py-2.5">
                            <p class="text-dense font-medium text-basalt-900">{{ $session['title'] }}</p>
                            <p class="mt-0.5 text-micro text-fern-500">{{ $session['instructor'] }} · {{ $session['starts'] }}</p>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="video" title="Nothing scheduled this month"
                               message="Field officers book onto clinics up to two weeks ahead.">
                    <x-slot:actions>
                        <x-button variant="secondary" size="sm" icon="plus"
                                  :href="route('workspace.sessions.create', ['platform' => $platformSlug])">
                            Schedule a session
                        </x-button>
                    </x-slot:actions>
                </x-empty-state>
            @endif
        </x-panel>
    </div>
</x-layouts.platform-workspace>
