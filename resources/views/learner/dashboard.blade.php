<x-layouts.learner title="My learning">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

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

            <p class="mt-4 text-dense text-fern-500">
                <a href="{{ route('learner.certificates') }}" class="font-medium text-accent-700 hover:underline">
                    {{ $page['certificatesCount'] === 1
                        ? '1 certificate earned'
                        : $page['certificatesCount'].' certificates earned' }}
                </a>
            </p>
        </div>
    @else
        <p class="mt-6 text-dense text-fern-500">
            <a href="{{ route('learner.certificates') }}" class="font-medium text-accent-700 hover:underline">
                {{ $page['certificatesCount'] === 1
                    ? '1 certificate earned'
                    : $page['certificatesCount'].' certificates earned' }}
            </a>
        </p>
    @endif

    <section class="mt-6 min-w-0">
        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="font-display text-section text-basalt-900">Courses in progress</h2>
            <a href="{{ route('learner.courses', ['status' => 'active']) }}" class="text-micro font-medium text-accent-700 hover:underline">All in-progress courses</a>
        </div>

        @if (count($page['inProgress']))
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($page['inProgress'] as $enrolment)
                    <x-course-card :course="$enrolment['course_data']"
                                   :platform-label="$enrolment['platform_name']"
                                   :progress="$enrolment['progress']"
                                   :status="$enrolment['status']"
                                   :href="route('learner.courses.show', ['course' => $enrolment['course']])" />
                @endforeach
            </div>
        @else
            <x-panel :padded="false">
                <x-empty-state icon="book" title="No courses in progress"
                               message="When a coordinator enrols you, or you start an open course, the next lesson lands here.">
                    <x-slot:actions>
                        <x-button variant="secondary" :href="route('learner.courses')">My courses</x-button>
                    </x-slot:actions>
                </x-empty-state>
            </x-panel>
        @endif
    </section>
</x-layouts.learner>
