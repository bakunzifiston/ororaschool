<x-layouts.learner title="My learning">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <section class="mt-8" aria-label="Key performance indicators">
        <x-kpi-strip :items="$page['stats']" solid />
    </section>

    @if (array_sum($page['charts']['mix']['values']) > 0)
        <section class="mt-10" aria-labelledby="learner-analytics-heading">
            <h2 id="learner-analytics-heading" class="font-display text-section text-basalt-900">Analytics</h2>
            <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <x-panel>
                    <x-pie-chart :title="$page['charts']['mix']['title']"
                                 :subtitle="$page['charts']['mix']['subtitle']"
                                 :headline="$page['charts']['mix']['headline'] ?? null"
                                 :labels="$page['charts']['mix']['labels']"
                                 :values="$page['charts']['mix']['values']" />
                </x-panel>
                <x-panel>
                    <x-bar-chart orientation="horizontal"
                                 :title="$page['charts']['progress']['title']"
                                 :subtitle="$page['charts']['progress']['subtitle']"
                                 :labels="$page['charts']['progress']['labels']"
                                 :values="$page['charts']['progress']['values']"
                                 :suffix="$page['charts']['progress']['suffix'] ?? ''"
                                 :max="$page['charts']['progress']['max'] ?? null" />
                </x-panel>
            </div>
        </section>
    @endif

    @if ($page['continue'])
        @php $next = $page['continue']; @endphp
        <section class="mt-8" aria-labelledby="continue-heading">
            <p class="text-micro text-fern-500">{{ $next['platform_name'] }} · next lesson</p>
            <h2 id="continue-heading" class="mt-1 font-display text-section leading-snug text-basalt-900">
                {{ $next['next'] }}
            </h2>
            <p class="mt-1 text-read text-fern-500">
                From {{ $next['course_data']['title'] }} · due {{ $next['due'] }}
            </p>

            <div class="mt-5 flex flex-wrap items-center gap-4">
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

            <p class="mt-5 text-dense text-fern-500">
                <a href="{{ route('learner.certificates') }}" class="font-medium text-accent-700 hover:underline">
                    {{ $page['certificatesCount'] === 1
                        ? '1 certificate earned'
                        : $page['certificatesCount'].' certificates earned' }}
                </a>
            </p>
        </section>
    @else
        <p class="mt-8 text-dense text-fern-500">
            <a href="{{ route('learner.certificates') }}" class="font-medium text-accent-700 hover:underline">
                {{ $page['certificatesCount'] === 1
                    ? '1 certificate earned'
                    : $page['certificatesCount'].' certificates earned' }}
            </a>
        </p>
    @endif

    <section class="mt-10 min-w-0" aria-labelledby="in-progress-heading">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 id="in-progress-heading" class="font-display text-section text-basalt-900">Courses in progress</h2>
            <a href="{{ route('learner.courses', ['status' => 'active']) }}" class="text-micro font-medium text-accent-700 hover:underline">All in-progress courses</a>
        </div>

        @if (count($page['inProgress']))
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($page['inProgress'] as $enrolment)
                    <x-course-card :course="$enrolment['course_data']"
                                   :platform-label="$enrolment['platform_name']"
                                   :progress="$enrolment['progress']"
                                   :status="$enrolment['status']"
                                   :href="route('learner.courses.show', ['course' => $enrolment['course']])" />
                @endforeach
            </div>
        @else
            <x-empty-state class="mt-5" icon="book" title="No courses in progress"
                           message="When a coordinator enrols you, or you start an open course, the next lesson lands here.">
                <x-slot:actions>
                    <x-button variant="secondary" :href="route('learner.courses')">My courses</x-button>
                </x-slot:actions>
            </x-empty-state>
        @endif
    </section>
</x-layouts.learner>
