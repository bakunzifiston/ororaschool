<x-layouts.learner title="My learning">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @if ($page['continue']['lesson'] ?? null)
                <x-button icon-after="arrow-right"
                          :href="route('learner.courses.lessons.show', ['course' => $page['continue']['course'], 'lesson' => $page['continue']['lesson']['id']])">
                    Continue lesson
                </x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    @if ($page['continue'])
        @php $next = $page['continue']; @endphp
        <div class="mt-6 rounded-md border border-clay-200 border-l-2 border-l-accent-500 bg-chalk p-5">
            <p class="text-micro text-fern-500">{{ $next['platform_name'] }} · next lesson</p>
            <h2 class="mt-1 font-display text-section leading-snug text-basalt-900">
                {{ $next['next'] }}
            </h2>
            <p class="mt-1 text-read text-fern-500">
                From {{ $next['course_data']['title'] }} · due {{ $next['due'] }}
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-4">
                <x-progress-bar :value="$next['progress']"
                                :meta="$next['lessons_done'] . ' of ' . $next['course_data']['lessons'] . ' lessons finished'"
                                class="max-w-sm" />
                @if ($next['lesson'] ?? null)
                    <x-button size="lg" icon="play"
                              :href="route('learner.courses.lessons.show', ['course' => $next['course'], 'lesson' => $next['lesson']['id']])">
                        Continue lesson
                    </x-button>
                @endif
            </div>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($page['stats'] as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']"
                         :trend="$stat['trend']" :direction="$stat['direction']" :note="$stat['note']" />
        @endforeach
    </div>

    <section class="mt-6 min-w-0">
        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="font-display text-section text-basalt-900">Courses in progress</h2>
            <a href="{{ route('learner.courses', ['status' => 'active']) }}" class="text-micro font-medium text-accent-700 hover:underline">All of them</a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($page['inProgress'] as $enrolment)
                <x-course-card :course="$enrolment['course_data']"
                               :platform-label="$enrolment['platform_name']"
                               :progress="$enrolment['progress']"
                               :status="$enrolment['status']"
                               :href="route('learner.courses.show', ['course' => $enrolment['course']])" />
            @endforeach
        </div>
    </section>
</x-layouts.learner>
